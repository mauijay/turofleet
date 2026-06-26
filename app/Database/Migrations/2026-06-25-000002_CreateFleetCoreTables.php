<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFleetCoreTables extends Migration
{
    public function up(): void
    {
        $this->createCompaniesTable();
        $this->createVehicleCatalogTables();
        $this->createVehicleLookupTables();
        $this->createVehicleSpecsTable();
        $this->createFleetVehiclesTable();
        $this->createSharedPivotTables();
        $this->createFleetVehicleFeaturesTable();
        $this->createVehicleTuroListingsTable();
    }

    public function down(): void
    {
        $this->forge->dropTable('vehicle_turo_listings', true);
        $this->forge->dropTable('fleet_vehicle_features', true);
        $this->forge->dropTable('vehicle_notes', true);
        $this->forge->dropTable('vehicle_files', true);
        $this->forge->dropTable('vehicle_images', true);
        $this->forge->dropTable('vehicle_addresses', true);
        $this->forge->dropTable('company_phone_numbers', true);
        $this->forge->dropTable('company_addresses', true);
        $this->forge->dropTable('fleet_vehicles', true);
        $this->forge->dropTable('vehicle_specs', true);
        $this->forge->dropTable('vehicle_statuses', true);
        $this->forge->dropTable('vehicle_drivetrains', true);
        $this->forge->dropTable('vehicle_trim_levels', true);
        $this->forge->dropTable('vehicle_features', true);
        $this->forge->dropTable('vehicle_colors', true);
        $this->forge->dropTable('vehicle_body_styles', true);
        $this->forge->dropTable('vehicle_models', true);
        $this->forge->dropTable('vehicle_makes', true);
        $this->forge->dropTable('companies', true);
    }

    private function createCompaniesTable(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'company_type_lookup_value_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 190],
            'legal_name' => ['type' => 'VARCHAR', 'constraint' => 190, 'null' => true],
            'slug' => ['type' => 'VARCHAR', 'constraint' => 120],
            'email' => ['type' => 'VARCHAR', 'constraint' => 190, 'null' => true],
            'website_url' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'tax_id_last4' => ['type' => 'VARCHAR', 'constraint' => 4, 'null' => true],
            'is_active' => ['type' => 'BOOLEAN', 'default' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('slug');
        $this->forge->addKey('company_type_lookup_value_id');
        $this->forge->addForeignKey('company_type_lookup_value_id', 'lookup_values', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('companies');
    }

    private function createVehicleCatalogTables(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'code' => ['type' => 'VARCHAR', 'constraint' => 80],
            'name' => ['type' => 'VARCHAR', 'constraint' => 120],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('code');
        $this->forge->createTable('vehicle_makes');

        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'vehicle_make_id' => ['type' => 'INT', 'unsigned' => true],
            'code' => ['type' => 'VARCHAR', 'constraint' => 80],
            'name' => ['type' => 'VARCHAR', 'constraint' => 120],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('vehicle_make_id');
        $this->forge->addUniqueKey(['vehicle_make_id', 'code']);
        $this->forge->addForeignKey('vehicle_make_id', 'vehicle_makes', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('vehicle_models');

        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'code' => ['type' => 'VARCHAR', 'constraint' => 80],
            'name' => ['type' => 'VARCHAR', 'constraint' => 120],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('code');
        $this->forge->createTable('vehicle_body_styles');

        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'code' => ['type' => 'VARCHAR', 'constraint' => 80],
            'name' => ['type' => 'VARCHAR', 'constraint' => 120],
            'hex_color' => ['type' => 'CHAR', 'constraint' => 7, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('code');
        $this->forge->createTable('vehicle_colors');

        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'code' => ['type' => 'VARCHAR', 'constraint' => 80],
            'name' => ['type' => 'VARCHAR', 'constraint' => 120],
            'description' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('code');
        $this->forge->createTable('vehicle_features');
    }

    private function createVehicleLookupTables(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'code' => ['type' => 'VARCHAR', 'constraint' => 80],
            'name' => ['type' => 'VARCHAR', 'constraint' => 120],
            'is_premium' => ['type' => 'BOOLEAN', 'default' => false],
            'sort_order' => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('code');
        $this->forge->createTable('vehicle_trim_levels');

        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'code' => ['type' => 'VARCHAR', 'constraint' => 80],
            'name' => ['type' => 'VARCHAR', 'constraint' => 120],
            'motor_count' => ['type' => 'TINYINT', 'unsigned' => true, 'null' => true],
            'sort_order' => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('code');
        $this->forge->createTable('vehicle_drivetrains');

        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'code' => ['type' => 'VARCHAR', 'constraint' => 80],
            'name' => ['type' => 'VARCHAR', 'constraint' => 120],
            'is_available_for_booking' => ['type' => 'BOOLEAN', 'default' => false],
            'sort_order' => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('code');
        $this->forge->createTable('vehicle_statuses');
    }

    private function createVehicleSpecsTable(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'vehicle_model_id' => ['type' => 'INT', 'unsigned' => true],
            'model_year' => ['type' => 'SMALLINT', 'unsigned' => true],
            'vehicle_body_style_id' => ['type' => 'INT', 'unsigned' => true],
            'exterior_vehicle_color_id' => ['type' => 'INT', 'unsigned' => true],
            'interior_vehicle_color_id' => ['type' => 'INT', 'unsigned' => true],
            'battery_description' => ['type' => 'VARCHAR', 'constraint' => 120, 'default' => ''],
            'seating_capacity' => ['type' => 'TINYINT', 'unsigned' => true, 'default' => 5],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('vehicle_model_id');
        $this->forge->addKey('vehicle_body_style_id');
        $this->forge->addKey('exterior_vehicle_color_id');
        $this->forge->addKey('interior_vehicle_color_id');
        $this->forge->addUniqueKey([
            'vehicle_model_id',
            'model_year',
            'vehicle_body_style_id',
            'exterior_vehicle_color_id',
            'interior_vehicle_color_id',
            'battery_description',
            'seating_capacity',
        ], 'vehicle_specs_natural_key');
        $this->forge->addForeignKey('vehicle_model_id', 'vehicle_models', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('vehicle_body_style_id', 'vehicle_body_styles', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('exterior_vehicle_color_id', 'vehicle_colors', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('interior_vehicle_color_id', 'vehicle_colors', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('vehicle_specs');
    }

    private function createFleetVehiclesTable(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'company_id' => ['type' => 'INT', 'unsigned' => true],
            'vehicle_spec_id' => ['type' => 'INT', 'unsigned' => true],
            'vehicle_trim_level_id' => ['type' => 'INT', 'unsigned' => true],
            'vehicle_drivetrain_id' => ['type' => 'INT', 'unsigned' => true],
            'vehicle_status_id' => ['type' => 'INT', 'unsigned' => true],
            'fleet_code' => ['type' => 'VARCHAR', 'constraint' => 80],
            'display_name' => ['type' => 'VARCHAR', 'constraint' => 150],
            'vin' => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => true],
            'license_plate' => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => true],
            'purchase_date' => ['type' => 'DATE', 'null' => true],
            'in_service_date' => ['type' => 'DATE', 'null' => true],
            'out_of_service_date' => ['type' => 'DATE', 'null' => true],
            'odometer_miles' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'sort_order' => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('fleet_code');
        $this->forge->addUniqueKey('vin');
        $this->forge->addKey(['company_id', 'vehicle_status_id']);
        $this->forge->addForeignKey('company_id', 'companies', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('vehicle_spec_id', 'vehicle_specs', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('vehicle_trim_level_id', 'vehicle_trim_levels', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('vehicle_drivetrain_id', 'vehicle_drivetrains', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('vehicle_status_id', 'vehicle_statuses', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('fleet_vehicles');
    }

    private function createSharedPivotTables(): void
    {
        $this->createCompanyAddressesTable();
        $this->createCompanyPhoneNumbersTable();
        $this->createVehicleAddressesTable();
        $this->createVehicleImagesTable();
        $this->createVehicleFilesTable();
        $this->createVehicleNotesTable();
    }

    private function createCompanyAddressesTable(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'company_id' => ['type' => 'INT', 'unsigned' => true],
            'address_id' => ['type' => 'INT', 'unsigned' => true],
            'address_type_lookup_value_id' => ['type' => 'INT', 'unsigned' => true],
            'is_primary' => ['type' => 'BOOLEAN', 'default' => false],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['company_id', 'address_id', 'address_type_lookup_value_id'], 'company_addresses_unique');
        $this->forge->addForeignKey('company_id', 'companies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('address_id', 'addresses', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('address_type_lookup_value_id', 'lookup_values', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('company_addresses');
    }

    private function createCompanyPhoneNumbersTable(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'company_id' => ['type' => 'INT', 'unsigned' => true],
            'phone_number_id' => ['type' => 'INT', 'unsigned' => true],
            'phone_type_lookup_value_id' => ['type' => 'INT', 'unsigned' => true],
            'is_primary' => ['type' => 'BOOLEAN', 'default' => false],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['company_id', 'phone_number_id', 'phone_type_lookup_value_id'], 'company_phone_numbers_unique');
        $this->forge->addForeignKey('company_id', 'companies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('phone_number_id', 'phone_numbers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('phone_type_lookup_value_id', 'lookup_values', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('company_phone_numbers');
    }

    private function createVehicleAddressesTable(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'fleet_vehicle_id' => ['type' => 'INT', 'unsigned' => true],
            'address_id' => ['type' => 'INT', 'unsigned' => true],
            'address_type_lookup_value_id' => ['type' => 'INT', 'unsigned' => true],
            'starts_on' => ['type' => 'DATE', 'null' => true],
            'ends_on' => ['type' => 'DATE', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['fleet_vehicle_id', 'address_id', 'address_type_lookup_value_id'], 'vehicle_addresses_unique');
        $this->forge->addForeignKey('fleet_vehicle_id', 'fleet_vehicles', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('address_id', 'addresses', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('address_type_lookup_value_id', 'lookup_values', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('vehicle_addresses');
    }

    private function createVehicleImagesTable(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'fleet_vehicle_id' => ['type' => 'INT', 'unsigned' => true],
            'image_id' => ['type' => 'INT', 'unsigned' => true],
            'image_type_lookup_value_id' => ['type' => 'INT', 'unsigned' => true],
            'caption' => ['type' => 'VARCHAR', 'constraint' => 190, 'null' => true],
            'sort_order' => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
            'is_primary' => ['type' => 'BOOLEAN', 'default' => false],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['fleet_vehicle_id', 'image_id']);
        $this->forge->addForeignKey('fleet_vehicle_id', 'fleet_vehicles', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('image_id', 'images', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('image_type_lookup_value_id', 'lookup_values', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('vehicle_images');
    }

    private function createVehicleFilesTable(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'fleet_vehicle_id' => ['type' => 'INT', 'unsigned' => true],
            'file_id' => ['type' => 'INT', 'unsigned' => true],
            'file_type_lookup_value_id' => ['type' => 'INT', 'unsigned' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['fleet_vehicle_id', 'file_id']);
        $this->forge->addForeignKey('fleet_vehicle_id', 'fleet_vehicles', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('file_id', 'files', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('file_type_lookup_value_id', 'lookup_values', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('vehicle_files');
    }

    private function createVehicleNotesTable(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'fleet_vehicle_id' => ['type' => 'INT', 'unsigned' => true],
            'note_id' => ['type' => 'INT', 'unsigned' => true],
            'note_type_lookup_value_id' => ['type' => 'INT', 'unsigned' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['fleet_vehicle_id', 'note_id']);
        $this->forge->addForeignKey('fleet_vehicle_id', 'fleet_vehicles', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('note_id', 'notes', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('note_type_lookup_value_id', 'lookup_values', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('vehicle_notes');
    }

    private function createFleetVehicleFeaturesTable(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'fleet_vehicle_id' => ['type' => 'INT', 'unsigned' => true],
            'vehicle_feature_id' => ['type' => 'INT', 'unsigned' => true],
            'starts_on' => ['type' => 'DATE', 'null' => true],
            'ends_on' => ['type' => 'DATE', 'null' => true],
            'notes' => ['type' => 'VARCHAR', 'constraint' => 190, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['fleet_vehicle_id', 'vehicle_feature_id'], 'fleet_vehicle_features_unique');
        $this->forge->addForeignKey('fleet_vehicle_id', 'fleet_vehicles', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('vehicle_feature_id', 'vehicle_features', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('fleet_vehicle_features');
    }

    private function createVehicleTuroListingsTable(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'fleet_vehicle_id' => ['type' => 'INT', 'unsigned' => true],
            'turo_vehicle_id' => ['type' => 'VARCHAR', 'constraint' => 80],
            'listing_url' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'daily_rate' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => true],
            'is_active' => ['type' => 'BOOLEAN', 'default' => true],
            'listed_at' => ['type' => 'DATETIME', 'null' => true],
            'unlisted_at' => ['type' => 'DATETIME', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('fleet_vehicle_id');
        $this->forge->addUniqueKey('turo_vehicle_id');
        $this->forge->addForeignKey('fleet_vehicle_id', 'fleet_vehicles', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('vehicle_turo_listings');
    }
}
