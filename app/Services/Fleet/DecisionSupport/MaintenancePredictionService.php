<?php

namespace App\Services\Fleet\DecisionSupport;

use App\DTOs\Fleet\Recommendation;
use App\Services\Fleet\FleetHealthService;
use Config\DecisionSupport;
use DateTimeImmutable;

class MaintenancePredictionService
{
    public function __construct(
        private readonly ?FleetHealthService $healthService = null,
        private readonly ?DecisionSupport $config = null,
        private readonly ?RecommendationFactory $factory = null,
    ) {
    }

    /** @return array<int, Recommendation> */
    public function recommendations(?DateTimeImmutable $asOf = null): array
    {
        $asOf ??= new DateTimeImmutable();

        return array_values(array_merge(
            $this->maintenanceDue($asOf),
            $this->registrationExpiring($asOf),
            $this->insuranceExpiring($asOf),
            $this->claimsRequiringFollowUp($asOf),
            $this->cleaningRequired($asOf),
        ));
    }

    /** @return array<int, Recommendation> */
    public function predictions(?DateTimeImmutable $asOf = null): array
    {
        return $this->recommendations($asOf);
    }

    private function maintenanceDue(DateTimeImmutable $asOf): array
    {
        return array_map(function (array $row) use ($asOf): Recommendation {
            return $this->factory()->make(
                'Maintenance due for ' . $this->vehicleLabel($row),
                'Maintenance',
                'High',
                92,
                'A maintenance record is due within the configured service horizon.',
                ['fleet_vehicle_id' => (int) ($row['fleet_vehicle_id'] ?? $row['id'] ?? 0), 'horizon_days' => $this->config()->maintenanceHorizonDays],
                'Schedule or complete the open maintenance item before the vehicle is dispatched.',
                $asOf,
                self::class,
            );
        }, $this->health()->vehiclesDueForMaintenance($asOf, $this->config()->maintenanceHorizonDays));
    }

    private function registrationExpiring(DateTimeImmutable $asOf): array
    {
        return array_map(function (array $row) use ($asOf): Recommendation {
            return $this->factory()->make(
                'Registration expiring for ' . $this->vehicleLabel($row),
                'Maintenance',
                'Medium',
                90,
                'Vehicle registration expires within the configured renewal horizon.',
                ['fleet_vehicle_id' => (int) ($row['fleet_vehicle_id'] ?? $row['id'] ?? 0), 'expires_on' => (string) ($row['expires_on'] ?? '')],
                'Renew registration and upload the updated document before expiration.',
                $asOf,
                self::class,
            );
        }, $this->health()->registrationExpiring($asOf, $this->config()->registrationHorizonDays));
    }

    private function insuranceExpiring(DateTimeImmutable $asOf): array
    {
        return array_map(function (array $row) use ($asOf): Recommendation {
            return $this->factory()->make(
                'Insurance expiring for ' . $this->vehicleLabel($row),
                'Maintenance',
                'High',
                90,
                'Vehicle insurance expires within the configured renewal horizon.',
                ['fleet_vehicle_id' => (int) ($row['fleet_vehicle_id'] ?? $row['id'] ?? 0), 'expires_on' => (string) ($row['expires_on'] ?? '')],
                'Confirm renewal coverage and attach the updated policy record.',
                $asOf,
                self::class,
            );
        }, $this->health()->insuranceExpiring($asOf, $this->config()->insuranceHorizonDays));
    }

    private function claimsRequiringFollowUp(DateTimeImmutable $asOf): array
    {
        return array_map(function (array $row) use ($asOf): Recommendation {
            return $this->factory()->make(
                'Follow up claim for ' . $this->vehicleLabel($row),
                'Maintenance',
                'High',
                88,
                'An open or unpaid damage claim is still in the follow-up queue.',
                ['fleet_vehicle_id' => (int) ($row['fleet_vehicle_id'] ?? $row['id'] ?? 0), 'claim_id' => (int) ($row['id'] ?? 0)],
                'Review claim status, payment state, and next action with the claim owner.',
                $asOf,
                self::class,
            );
        }, $this->health()->claimsRequiringFollowUp());
    }

    private function cleaningRequired(DateTimeImmutable $asOf): array
    {
        return array_map(function (array $row) use ($asOf): Recommendation {
            return $this->factory()->make(
                'Clean ' . $this->vehicleLabel($row) . ' before next dispatch',
                'Maintenance',
                'Medium',
                80,
                'A completed return is known and cleaning workflow review is required.',
                ['fleet_vehicle_id' => (int) ($row['fleet_vehicle_id'] ?? $row['id'] ?? 0), 'stale_cleaning_days' => $this->config()->staleCleaningDays],
                'Complete cleaning inspection before the vehicle is marked ready.',
                $asOf,
                self::class,
            );
        }, $this->health()->vehiclesNeedingCleaning($asOf));
    }

    private function vehicleLabel(array $row): string
    {
        return (string) ($row['fleet_code'] ?? $row['display_name'] ?? 'vehicle ' . (string) ($row['fleet_vehicle_id'] ?? $row['id'] ?? ''));
    }

    private function health(): FleetHealthService
    {
        return $this->healthService ?? service('fleetHealthService');
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
