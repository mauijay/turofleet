<?php

namespace App\Services\Turo;

use App\Repositories\FleetVehicleRepository;

class TuroVehicleMatcher
{
    public function __construct(
        private readonly FleetVehicleRepository $fleetVehicles = new FleetVehicleRepository(),
    ) {
    }

    public function match(?string $turoVehicleId, ?string $fleetCode = null): ?int
    {
        $fleetVehicleId = $this->fleetVehicles->findIdByTuroVehicleId($turoVehicleId);

        if ($fleetVehicleId !== null) {
            return $fleetVehicleId;
        }

        return $this->fleetVehicles->findIdByFleetCode($fleetCode);
    }
}
