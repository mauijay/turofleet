<?php

namespace App\DTOs\Turo;

class TripMonthAllocationData
{
    public function __construct(
        public readonly ?int $fleetVehicleId,
        public readonly string $allocationMonth,
        public readonly string $allocationStartsAt,
        public readonly string $allocationEndsAt,
        public readonly string $allocatedTripDays,
        public readonly string $allocatedBillableDays,
        public readonly string $allocatedGrossRevenueAmount,
        public readonly string $allocatedHostPayoutAmount,
        public readonly string $allocatedDeliveryFeeAmount,
        public readonly string $allocatedReimbursementAmount,
        public readonly bool $isForecast,
    ) {
    }
}
