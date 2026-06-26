<?php

namespace App\Repositories;

use CodeIgniter\Database\BaseConnection;
use Config\Database;

class FleetVehicleRepository
{
    private BaseConnection $db;

    public function __construct(?BaseConnection $db = null)
    {
        $this->db = $db ?? Database::connect();
    }

    public function findIdByTuroVehicleId(?string $turoVehicleId): ?int
    {
        if ($turoVehicleId === null || trim($turoVehicleId) === '') {
            return null;
        }

        $row = $this->db->table('vehicle_turo_listings')
            ->select('fleet_vehicle_id')
            ->where('turo_vehicle_id', trim($turoVehicleId))
            ->where('is_active', true)
            ->get()
            ->getRowArray();

        return $row === null ? null : (int) $row['fleet_vehicle_id'];
    }

    public function findIdByFleetCode(?string $fleetCode): ?int
    {
        if ($fleetCode === null || trim($fleetCode) === '') {
            return null;
        }

        $row = $this->db->table('fleet_vehicles')
            ->select('id')
            ->where('fleet_code', trim($fleetCode))
            ->get()
            ->getRowArray();

        return $row === null ? null : (int) $row['id'];
    }
}
