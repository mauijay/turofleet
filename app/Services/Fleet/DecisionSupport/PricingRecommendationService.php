<?php

namespace App\Services\Fleet\DecisionSupport;

use App\DTOs\Fleet\Recommendation;
use App\Services\Fleet\FleetStatisticsService;
use App\Services\Fleet\RevenueService;
use Config\DecisionSupport;
use DateTimeImmutable;

class PricingRecommendationService
{
    public function __construct(
        private readonly ?FleetStatisticsService $statisticsService = null,
        private readonly ?RevenueService $revenueService = null,
        private readonly ?DecisionSupport $config = null,
        private readonly ?RecommendationFactory $factory = null,
    ) {
    }

    /** @return array<int, Recommendation> */
    public function recommendations(?DateTimeImmutable $asOf = null): array
    {
        $asOf ??= new DateTimeImmutable();
        $vehicles = $this->statistics()->vehiclePerformance($asOf);

        if ($vehicles === []) {
            return [];
        }

        return array_values(array_merge(
            $this->vehiclePricing($vehicles, $asOf),
            $this->premiumBaseComparison($vehicles, $asOf),
            $this->demandTrend($asOf),
        ));
    }

    private function demandTrend(DateTimeImmutable $asOf): array
    {
        $fromMonth = $asOf->modify('-' . ($this->config()->forecastLookbackMonths - 1) . ' months')->format('Y-m-01');
        $toMonth = $asOf->format('Y-m-01');
        $rows = $this->revenue()->trends($fromMonth, $toMonth);

        if (count($rows) < 2) {
            return [];
        }

        $current = (float) ($rows[count($rows) - 1]['completed_revenue'] ?? 0);
        $priorRows = array_slice($rows, 0, -1);
        $priorAverage = array_reduce($priorRows, static fn (float $total, array $row): float => $total + (float) ($row['completed_revenue'] ?? 0), 0.0) / count($priorRows);

        if ($priorAverage <= 0.0) {
            return [];
        }

        $change = ($current - $priorAverage) / $priorAverage;

        if ($change >= $this->config()->pricingHighOccupancyDelta) {
            return [$this->factory()->make(
                'Demand trend is increasing',
                'Pricing',
                'Medium',
                78,
                'Current completed revenue is above the recent monthly average.',
                ['current_month_revenue' => round($current, 2), 'prior_month_average' => round($priorAverage, 2), 'trend_change' => $this->percent($change)],
                'Review top-performing vehicles for price increases before the trend expires.',
                $asOf,
                self::class,
            )];
        }

        if ($change <= $this->config()->pricingLowOccupancyDelta) {
            return [$this->factory()->make(
                'Demand trend is softening',
                'Pricing',
                'Medium',
                78,
                'Current completed revenue is below the recent monthly average.',
                ['current_month_revenue' => round($current, 2), 'prior_month_average' => round($priorAverage, 2), 'trend_change' => $this->percent($change)],
                'Review discounts, listing visibility, and fleet availability before lowering prices broadly.',
                $asOf,
                self::class,
            )];
        }

        return [];
    }

    /** @param array<int, array<string, mixed>> $vehicles */
    private function vehiclePricing(array $vehicles, DateTimeImmutable $asOf): array
    {
        $fleetUtilization = $this->average($vehicles, 'utilization');
        $fleetAdr = $this->average($vehicles, 'average_daily_rate');
        $recommendations = [];

        foreach ($vehicles as $vehicle) {
            $utilization = (float) ($vehicle['utilization'] ?? 0);
            $adr = (float) ($vehicle['average_daily_rate'] ?? 0);
            $fleetCode = (string) ($vehicle['fleet_code'] ?? $vehicle['display_name'] ?? 'Vehicle');
            $billableDays = (float) ($vehicle['billable_days'] ?? 0);
            $confidence = $this->confidenceForMeasuredDays($billableDays);

            if ($fleetAdr > 0 && $utilization >= $fleetUtilization + $this->config()->pricingHighOccupancyDelta && $adr >= $fleetAdr * (1 - $this->config()->adrCompetitivenessDelta)) {
                $recommendations[] = $this->factory()->make(
                    'Increase ' . $fleetCode . ' pricing',
                    'Pricing',
                    'High',
                    $confidence,
                    'Vehicle occupancy exceeds fleet average while ADR remains competitive.',
                    [
                        'occupancy' => $this->percent($utilization),
                        'fleet_average' => $this->percent($fleetUtilization),
                        'adr' => round($adr, 2),
                        'fleet_adr' => round($fleetAdr, 2),
                    ],
                    'Increase daily price by $' . $this->config()->priceStepDollars . '.',
                    $asOf,
                    self::class,
                );
                continue;
            }

            if ($fleetAdr > 0 && $utilization <= $fleetUtilization + $this->config()->pricingLowOccupancyDelta && $adr > $fleetAdr * (1 + $this->config()->adrCompetitivenessDelta)) {
                $recommendations[] = $this->factory()->make(
                    'Decrease ' . $fleetCode . ' pricing',
                    'Pricing',
                    'Medium',
                    $confidence,
                    'Vehicle occupancy trails fleet average while ADR is above fleet ADR.',
                    [
                        'occupancy' => $this->percent($utilization),
                        'fleet_average' => $this->percent($fleetUtilization),
                        'adr' => round($adr, 2),
                        'fleet_adr' => round($fleetAdr, 2),
                    ],
                    'Decrease daily price by $' . $this->config()->priceStepDollars . ' and monitor occupancy.',
                    $asOf,
                    self::class,
                );
            }
        }

        return $recommendations;
    }

    /** @param array<int, array<string, mixed>> $vehicles */
    private function premiumBaseComparison(array $vehicles, DateTimeImmutable $asOf): array
    {
        $segments = [
            'premium' => ['vehicles' => 0, 'revenue' => 0.0],
            'base' => ['vehicles' => 0, 'revenue' => 0.0],
        ];

        foreach ($vehicles as $vehicle) {
            $segment = (bool) ($vehicle['is_premium'] ?? false) ? 'premium' : 'base';
            $segments[$segment]['vehicles']++;
            $segments[$segment]['revenue'] += (float) ($vehicle['completed_revenue'] ?? $vehicle['host_payout'] ?? 0);
        }

        $totalVehicles = $segments['premium']['vehicles'] + $segments['base']['vehicles'];
        $totalRevenue = $segments['premium']['revenue'] + $segments['base']['revenue'];

        if ($totalVehicles === 0 || $totalRevenue <= 0.0) {
            return [];
        }

        $premiumInventoryShare = $segments['premium']['vehicles'] / $totalVehicles;
        $premiumRevenueShare = $segments['premium']['revenue'] / $totalRevenue;

        if ($premiumRevenueShare < $premiumInventoryShare + $this->config()->pricingHighOccupancyDelta) {
            return [];
        }

        return [$this->factory()->make(
            'Premium pricing is outperforming base inventory',
            'Pricing',
            'Informational',
            85,
            'Premium vehicles are producing a larger revenue share than their inventory share.',
            [
                'premium_revenue_share' => $this->percent($premiumRevenueShare),
                'premium_inventory_share' => $this->percent($premiumInventoryShare),
                'premium_revenue' => round($segments['premium']['revenue'], 2),
                'base_revenue' => round($segments['base']['revenue'], 2),
            ],
            'Keep premium pricing strategy under active review before discounting premium vehicles.',
            $asOf,
            self::class,
        )];
    }

    /** @param array<int, array<string, mixed>> $rows */
    private function average(array $rows, string $key): float
    {
        if ($rows === []) {
            return 0.0;
        }

        return array_reduce($rows, static fn (float $total, array $row): float => $total + (float) ($row[$key] ?? 0), 0.0) / count($rows);
    }

    private function confidenceForMeasuredDays(float $billableDays): int
    {
        $coverage = min(1.0, $billableDays / max(1, $this->config()->pricingLookbackDays));

        return (int) round(55 + ($coverage * 40));
    }

    private function percent(float $value): int
    {
        return (int) round($value * 100);
    }

    private function statistics(): FleetStatisticsService
    {
        return $this->statisticsService ?? service('fleetStatisticsService');
    }

    private function revenue(): RevenueService
    {
        return $this->revenueService ?? service('revenueService');
    }

    private function config(): DecisionSupport
    {
        return $this->config ?? config(DecisionSupport::class);
    }

    private function factory(): RecommendationFactory
    {
        return $this->factory ?? new RecommendationFactory($this->config());
    }
}
