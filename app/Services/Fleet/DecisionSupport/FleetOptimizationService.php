<?php

namespace App\Services\Fleet\DecisionSupport;

use App\DTOs\Fleet\Recommendation;
use App\Services\Fleet\FleetStatisticsService;
use Config\DecisionSupport;
use DateTimeImmutable;

class FleetOptimizationService
{
    public function __construct(
        private readonly ?FleetStatisticsService $statisticsService = null,
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
            $this->idleVehicles($vehicles, $asOf),
            $this->overPerformingVehicles($vehicles, $asOf),
            $this->revenueConcentration($vehicles, $asOf),
            $this->premiumCapacity($vehicles, $asOf),
        ));
    }

    /** @return array<int, array<string, mixed>> */
    public function utilizationRanking(?DateTimeImmutable $asOf = null): array
    {
        $vehicles = $this->statistics()->vehiclePerformance($asOf ?? new DateTimeImmutable());
        usort($vehicles, static fn (array $left, array $right): int => (float) ($right['utilization'] ?? 0) <=> (float) ($left['utilization'] ?? 0));

        return $vehicles;
    }

    /** @param array<int, array<string, mixed>> $vehicles */
    private function idleVehicles(array $vehicles, DateTimeImmutable $asOf): array
    {
        $recommendations = [];

        foreach ($vehicles as $vehicle) {
            $utilization = (float) ($vehicle['utilization'] ?? 0);
            $revenue = (float) ($vehicle['completed_revenue'] ?? $vehicle['host_payout'] ?? 0);

            if ($utilization > $this->config()->lowUtilization || $revenue > 0.0) {
                continue;
            }

            $fleetCode = (string) ($vehicle['fleet_code'] ?? $vehicle['display_name'] ?? 'Vehicle');
            $recommendations[] = $this->factory()->make(
                'Investigate idle ' . $fleetCode,
                'Fleet Optimization',
                'High',
                88,
                'Vehicle has low utilization and no completed revenue in the measured period.',
                ['utilization' => $this->percent($utilization), 'completed_revenue' => round($revenue, 2)],
                'Review listing quality, availability, pricing, and replacement fit before keeping this vehicle idle.',
                $asOf,
                self::class,
            );
        }

        return $recommendations;
    }

    /** @param array<int, array<string, mixed>> $vehicles */
    private function overPerformingVehicles(array $vehicles, DateTimeImmutable $asOf): array
    {
        $averageRevenue = $this->averageRevenue($vehicles);
        $recommendations = [];

        foreach ($vehicles as $vehicle) {
            $utilization = (float) ($vehicle['utilization'] ?? 0);
            $revenue = (float) ($vehicle['completed_revenue'] ?? $vehicle['host_payout'] ?? 0);

            if ($utilization < $this->config()->highUtilization || $revenue < $averageRevenue) {
                continue;
            }

            $fleetCode = (string) ($vehicle['fleet_code'] ?? $vehicle['display_name'] ?? 'Vehicle');
            $recommendations[] = $this->factory()->make(
                $fleetCode . ' is over-performing',
                'Fleet Optimization',
                'Medium',
                86,
                'Vehicle utilization is high and completed revenue exceeds the fleet average.',
                ['utilization' => $this->percent($utilization), 'completed_revenue' => round($revenue, 2), 'fleet_average_revenue' => round($averageRevenue, 2)],
                'Protect availability for this vehicle and use it as the benchmark for similar inventory.',
                $asOf,
                self::class,
            );
        }

        return $recommendations;
    }

    /** @param array<int, array<string, mixed>> $vehicles */
    private function revenueConcentration(array $vehicles, DateTimeImmutable $asOf): array
    {
        $totalRevenue = array_reduce($vehicles, static fn (float $total, array $vehicle): float => $total + (float) ($vehicle['completed_revenue'] ?? $vehicle['host_payout'] ?? 0), 0.0);

        if ($totalRevenue <= 0.0) {
            return [];
        }

        usort($vehicles, static fn (array $left, array $right): int => (float) ($right['completed_revenue'] ?? $right['host_payout'] ?? 0) <=> (float) ($left['completed_revenue'] ?? $left['host_payout'] ?? 0));
        $topVehicle = $vehicles[0];
        $topRevenue = (float) ($topVehicle['completed_revenue'] ?? $topVehicle['host_payout'] ?? 0);
        $share = $topRevenue / $totalRevenue;

        if ($share < $this->config()->revenueConcentrationShare) {
            return [];
        }

        return [$this->factory()->make(
            'Revenue is concentrated in ' . (string) ($topVehicle['fleet_code'] ?? 'one vehicle'),
            'Fleet Optimization',
            'Medium',
            82,
            'One vehicle contributes more revenue than the configured concentration threshold.',
            ['top_vehicle_revenue_share' => $this->percent($share), 'top_vehicle_revenue' => round($topRevenue, 2), 'fleet_revenue' => round($totalRevenue, 2)],
            'Reduce dependence on one vehicle by improving under-performing listings or expanding similar inventory.',
            $asOf,
            self::class,
        )];
    }

    /** @param array<int, array<string, mixed>> $vehicles */
    private function premiumCapacity(array $vehicles, DateTimeImmutable $asOf): array
    {
        $premium = array_values(array_filter($vehicles, static fn (array $vehicle): bool => (bool) ($vehicle['is_premium'] ?? false)));

        if ($premium === []) {
            return [];
        }

        $averageUtilization = array_reduce($premium, static fn (float $total, array $vehicle): float => $total + (float) ($vehicle['utilization'] ?? 0), 0.0) / count($premium);

        if ($averageUtilization < $this->config()->segmentCapacityUtilization) {
            return [];
        }

        return [$this->factory()->make(
            'Premium fleet is operating near capacity',
            'Fleet Optimization',
            'High',
            84,
            'Premium segment average utilization exceeds the configured capacity threshold.',
            ['premium_utilization' => $this->percent($averageUtilization), 'premium_vehicle_count' => count($premium)],
            'Evaluate adding another premium vehicle before demand is constrained by availability.',
            $asOf,
            self::class,
        )];
    }

    /** @param array<int, array<string, mixed>> $vehicles */
    private function averageRevenue(array $vehicles): float
    {
        return array_reduce($vehicles, static fn (float $total, array $vehicle): float => $total + (float) ($vehicle['completed_revenue'] ?? $vehicle['host_payout'] ?? 0), 0.0) / count($vehicles);
    }

    private function percent(float $value): int
    {
        return (int) round($value * 100);
    }

    private function statistics(): FleetStatisticsService
    {
        return $this->statisticsService ?? service('fleetStatisticsService');
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
