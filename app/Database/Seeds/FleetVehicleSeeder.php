<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FleetVehicleSeeder extends Seeder
{
    public function run(): void
    {
        $companyId = $this->firstOrCreate('companies', ['slug' => 'go808-fleetos'], [
            'company_type_lookup_value_id' => $this->lookupValueId('company_type', 'fleet_owner'),
            'name' => 'GO808 FleetOS',
            'legal_name' => 'GO808 FleetOS',
            'is_active' => true,
        ]);

        $activeStatusId = $this->idByCode('vehicle_statuses', 'active');
        $teslaMakeId = $this->firstOrCreate('vehicle_makes', ['code' => 'tesla'], ['name' => 'Tesla']);
        $model3Id = $this->firstOrCreate('vehicle_models', ['vehicle_make_id' => $teslaMakeId, 'code' => 'model_3'], ['name' => 'Model 3']);
        $modelYId = $this->firstOrCreate('vehicle_models', ['vehicle_make_id' => $teslaMakeId, 'code' => 'model_y'], ['name' => 'Model Y']);
        $sedanBodyStyleId = $this->firstOrCreate('vehicle_body_styles', ['code' => 'sedan'], ['name' => 'Sedan']);
        $suvBodyStyleId = $this->firstOrCreate('vehicle_body_styles', ['code' => 'suv'], ['name' => 'SUV']);
        $blackColorId = $this->firstOrCreate('vehicle_colors', ['code' => 'black'], ['name' => 'Black', 'hex_color' => '#000000']);
        $whiteColorId = $this->firstOrCreate('vehicle_colors', ['code' => 'white'], ['name' => 'White', 'hex_color' => '#FFFFFF']);
        $grayColorId = $this->firstOrCreate('vehicle_colors', ['code' => 'gray'], ['name' => 'Gray', 'hex_color' => '#808080']);
        $fsdFeatureId = $this->firstOrCreate('vehicle_features', ['code' => 'fsd'], ['name' => 'Full Self-Driving']);
        $freeSuperchargingFeatureId = $this->firstOrCreate('vehicle_features', ['code' => 'free_supercharging_1_year'], [
            'name' => '1-Year Free Supercharging',
        ]);

        $vehicles = [
            [
                'fleet_code' => 'Spaceship-002',
                'display_name' => 'Spaceship-002',
                'model_year' => 2021,
                'vehicle_model_id' => $model3Id,
                'vehicle_body_style_id' => $sedanBodyStyleId,
                'trim' => 'long_range',
                'drivetrain' => 'awd',
                'exterior_vehicle_color_id' => $blackColorId,
                'interior_vehicle_color_id' => $blackColorId,
                'battery_description' => 'Long Range',
                'features' => [],
            ],
            [
                'fleet_code' => 'Spaceship-003',
                'display_name' => 'Spaceship-003',
                'model_year' => 2026,
                'vehicle_model_id' => $modelYId,
                'vehicle_body_style_id' => $suvBodyStyleId,
                'trim' => 'premium',
                'drivetrain' => 'awd',
                'exterior_vehicle_color_id' => $whiteColorId,
                'interior_vehicle_color_id' => $blackColorId,
                'battery_description' => '',
                'features' => [$fsdFeatureId],
            ],
            [
                'fleet_code' => 'Spaceship-004',
                'display_name' => 'Spaceship-004',
                'model_year' => 2026,
                'vehicle_model_id' => $modelYId,
                'vehicle_body_style_id' => $suvBodyStyleId,
                'trim' => 'base',
                'drivetrain' => 'awd',
                'exterior_vehicle_color_id' => $whiteColorId,
                'interior_vehicle_color_id' => $blackColorId,
                'battery_description' => '',
                'features' => [],
            ],
            [
                'fleet_code' => 'Spaceship-005',
                'display_name' => 'Spaceship-005',
                'model_year' => 2026,
                'vehicle_model_id' => $modelYId,
                'vehicle_body_style_id' => $suvBodyStyleId,
                'trim' => 'premium',
                'drivetrain' => 'awd',
                'exterior_vehicle_color_id' => $whiteColorId,
                'interior_vehicle_color_id' => $blackColorId,
                'battery_description' => '',
                'features' => [$fsdFeatureId],
            ],
            [
                'fleet_code' => 'Spaceship-006',
                'display_name' => 'Spaceship-006',
                'model_year' => 2026,
                'vehicle_model_id' => $modelYId,
                'vehicle_body_style_id' => $suvBodyStyleId,
                'trim' => 'base',
                'drivetrain' => 'fwd',
                'exterior_vehicle_color_id' => $whiteColorId,
                'interior_vehicle_color_id' => $blackColorId,
                'battery_description' => '',
                'features' => [],
            ],
            [
                'fleet_code' => 'Spaceship-007',
                'display_name' => 'Spaceship-007',
                'model_year' => 2026,
                'vehicle_model_id' => $model3Id,
                'vehicle_body_style_id' => $sedanBodyStyleId,
                'trim' => 'premium',
                'drivetrain' => 'awd',
                'exterior_vehicle_color_id' => $whiteColorId,
                'interior_vehicle_color_id' => $blackColorId,
                'battery_description' => '',
                'features' => [$fsdFeatureId, $freeSuperchargingFeatureId],
            ],
            [
                'fleet_code' => 'Spaceship-008',
                'display_name' => 'Spaceship-008',
                'model_year' => 2026,
                'vehicle_model_id' => $modelYId,
                'vehicle_body_style_id' => $suvBodyStyleId,
                'trim' => 'premium',
                'drivetrain' => 'fwd',
                'exterior_vehicle_color_id' => $whiteColorId,
                'interior_vehicle_color_id' => $blackColorId,
                'battery_description' => '',
                'features' => [$fsdFeatureId],
            ],
            [
                'fleet_code' => 'Spaceship-009',
                'display_name' => 'Spaceship-009',
                'model_year' => 2026,
                'vehicle_model_id' => $modelYId,
                'vehicle_body_style_id' => $suvBodyStyleId,
                'trim' => 'premium',
                'drivetrain' => 'fwd',
                'exterior_vehicle_color_id' => $grayColorId,
                'interior_vehicle_color_id' => $blackColorId,
                'battery_description' => '',
                'features' => [$fsdFeatureId],
            ],
        ];

        foreach ($vehicles as $sortOrder => $vehicle) {
            $specId = $this->firstOrCreate('vehicle_specs', [
                'vehicle_model_id' => $vehicle['vehicle_model_id'],
                'model_year' => $vehicle['model_year'],
                'vehicle_body_style_id' => $vehicle['vehicle_body_style_id'],
                'exterior_vehicle_color_id' => $vehicle['exterior_vehicle_color_id'],
                'interior_vehicle_color_id' => $vehicle['interior_vehicle_color_id'],
                'battery_description' => $vehicle['battery_description'],
                'seating_capacity' => 5,
            ], [
            ]);

            $fleetVehicleId = $this->firstOrCreate('fleet_vehicles', ['fleet_code' => $vehicle['fleet_code']], [
                'company_id' => $companyId,
                'vehicle_spec_id' => $specId,
                'vehicle_trim_level_id' => $this->idByCode('vehicle_trim_levels', $vehicle['trim']),
                'vehicle_drivetrain_id' => $this->idByCode('vehicle_drivetrains', $vehicle['drivetrain']),
                'vehicle_status_id' => $activeStatusId,
                'display_name' => $vehicle['display_name'],
                'sort_order' => $sortOrder + 1,
            ]);

            foreach ($vehicle['features'] as $featureId) {
                $this->firstOrCreate('fleet_vehicle_features', [
                    'fleet_vehicle_id' => $fleetVehicleId,
                    'vehicle_feature_id' => $featureId,
                ], []);
            }
        }
    }

    private function lookupValueId(string $typeCode, string $valueCode): ?int
    {
        $row = $this->db->table('lookup_values')
            ->select('lookup_values.id')
            ->join('lookup_types', 'lookup_types.id = lookup_values.lookup_type_id')
            ->where('lookup_types.code', $typeCode)
            ->where('lookup_values.code', $valueCode)
            ->get()
            ->getRowArray();

        return $row === null ? null : (int) $row['id'];
    }

    private function idByCode(string $table, string $code): int
    {
        $row = $this->db->table($table)->where('code', $code)->get()->getRowArray();

        if ($row === null) {
            throw new \RuntimeException("Missing lookup row {$table}.{$code}");
        }

        return (int) $row['id'];
    }

    private function firstOrCreate(string $table, array $where, array $data): int
    {
        $builder = $this->db->table($table);
        $existing = $builder->where($where)->get()->getRowArray();

        if ($existing !== null) {
            return (int) $existing['id'];
        }

        $now = date('Y-m-d H:i:s');
        $fields = $this->db->getFieldNames($table);
        $insert = array_merge($where, $data);

        if (in_array('created_at', $fields, true) && ! array_key_exists('created_at', $insert)) {
            $insert['created_at'] = $now;
        }

        if (in_array('updated_at', $fields, true) && ! array_key_exists('updated_at', $insert)) {
            $insert['updated_at'] = $now;
        }

        $builder->insert($insert);

        return (int) $this->db->insertID();
    }
}
