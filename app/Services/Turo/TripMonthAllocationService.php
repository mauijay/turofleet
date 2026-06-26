<?php

namespace App\Services\Turo;

use App\DTOs\Turo\NormalizedTripData;
use App\DTOs\Turo\TripMonthAllocationData;
use DateInterval;
use DateTimeImmutable;

class TripMonthAllocationService
{
    /** @return TripMonthAllocationData[] */
    public function allocate(NormalizedTripData $trip): array
    {
        $startsAt = new DateTimeImmutable($trip->startsAt);
        $endsAt = new DateTimeImmutable($trip->endsAt);
        $totalSeconds = max(1, $endsAt->getTimestamp() - $startsAt->getTimestamp());
        $segments = $this->segments($startsAt, $endsAt);

        $tripDayUnits = $this->decimalToUnits($trip->tripDays, 3);
        $billableDayUnits = $this->decimalToUnits($trip->billableDays, 3);
        $grossCents = $this->decimalToUnits($trip->grossRevenueAmount, 2);
        $payoutCents = $this->decimalToUnits($trip->hostPayoutAmount, 2);
        $deliveryCents = $this->decimalToUnits($trip->deliveryFeeAmount, 2);
        $reimbursementCents = $this->decimalToUnits($trip->reimbursementAmount, 2);

        $allocations = [];
        $allocated = [
            'tripDays' => 0,
            'billableDays' => 0,
            'gross' => 0,
            'payout' => 0,
            'delivery' => 0,
            'reimbursement' => 0,
        ];

        foreach ($segments as $index => $segment) {
            $isLast = $index === array_key_last($segments);
            $segmentSeconds = max(0, $segment['endsAt']->getTimestamp() - $segment['startsAt']->getTimestamp());
            $ratio = $segmentSeconds / $totalSeconds;

            $tripDays = $this->allocateUnits($tripDayUnits, $allocated['tripDays'], $ratio, $isLast);
            $billableDays = $this->allocateUnits($billableDayUnits, $allocated['billableDays'], $ratio, $isLast);
            $gross = $this->allocateUnits($grossCents, $allocated['gross'], $ratio, $isLast);
            $payout = $this->allocateUnits($payoutCents, $allocated['payout'], $ratio, $isLast);
            $delivery = $this->allocateUnits($deliveryCents, $allocated['delivery'], $ratio, $isLast);
            $reimbursement = $this->allocateUnits($reimbursementCents, $allocated['reimbursement'], $ratio, $isLast);

            $allocated['tripDays'] += $tripDays;
            $allocated['billableDays'] += $billableDays;
            $allocated['gross'] += $gross;
            $allocated['payout'] += $payout;
            $allocated['delivery'] += $delivery;
            $allocated['reimbursement'] += $reimbursement;

            $allocations[] = new TripMonthAllocationData(
                fleetVehicleId: $trip->fleetVehicleId,
                allocationMonth: $segment['startsAt']->format('Y-m-01'),
                allocationStartsAt: $segment['startsAt']->format('Y-m-d H:i:s'),
                allocationEndsAt: $segment['endsAt']->format('Y-m-d H:i:s'),
                allocatedTripDays: $this->unitsToDecimal($tripDays, 3),
                allocatedBillableDays: $this->unitsToDecimal($billableDays, 3),
                allocatedGrossRevenueAmount: $this->unitsToDecimal($gross, 2),
                allocatedHostPayoutAmount: $this->unitsToDecimal($payout, 2),
                allocatedDeliveryFeeAmount: $this->unitsToDecimal($delivery, 2),
                allocatedReimbursementAmount: $this->unitsToDecimal($reimbursement, 2),
                isForecast: $trip->isForecast,
            );
        }

        return $allocations;
    }

    /** @return array<int, array{startsAt: DateTimeImmutable, endsAt: DateTimeImmutable}> */
    private function segments(DateTimeImmutable $startsAt, DateTimeImmutable $endsAt): array
    {
        $segments = [];
        $cursor = $startsAt;

        while ($cursor < $endsAt) {
            $nextMonth = $cursor
                ->modify('first day of next month')
                ->setTime(0, 0, 0);
            $segmentEnd = $nextMonth < $endsAt ? $nextMonth : $endsAt;
            $segments[] = ['startsAt' => $cursor, 'endsAt' => $segmentEnd];
            $cursor = $segmentEnd->add(new DateInterval('PT0S'));
        }

        return $segments;
    }

    private function allocateUnits(int $totalUnits, int $alreadyAllocated, float $ratio, bool $isLast): int
    {
        if ($isLast) {
            return $totalUnits - $alreadyAllocated;
        }

        return (int) round($totalUnits * $ratio);
    }

    private function decimalToUnits(string $amount, int $precision): int
    {
        return (int) round(((float) $amount) * (10 ** $precision));
    }

    private function unitsToDecimal(int $units, int $precision): string
    {
        return number_format($units / (10 ** $precision), $precision, '.', '');
    }
}
