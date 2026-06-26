<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFleetFinanceAndOperationsTables extends Migration
{
    public function up(): void
    {
        $this->createLendersTable();
        $this->createLoansTable();
        $this->createInsurancePoliciesTable();
        $this->createRegistrationsTable();
        $this->createStartupCostsTable();
        $this->createMaintenanceLogsTable();
        $this->createDamageClaimsTable();
        $this->createChargingSessionsTable();
        $this->createAirportsTable();
        $this->createAirportDeliveriesTable();
        $this->createOperationalPivotTables();
    }

    public function down(): void
    {
        $this->forge->dropTable('maintenance_log_files', true);
        $this->forge->dropTable('maintenance_log_notes', true);
        $this->forge->dropTable('damage_claim_files', true);
        $this->forge->dropTable('damage_claim_notes', true);
        $this->forge->dropTable('airport_deliveries', true);
        $this->forge->dropTable('airports', true);
        $this->forge->dropTable('charging_sessions', true);
        $this->forge->dropTable('damage_claims', true);
        $this->forge->dropTable('maintenance_logs', true);
        $this->forge->dropTable('startup_costs', true);
        $this->forge->dropTable('registrations', true);
        $this->forge->dropTable('insurance_policies', true);
        $this->forge->dropTable('loans', true);
        $this->forge->dropTable('lenders', true);
    }

    private function createLendersTable(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'company_id' => ['type' => 'INT', 'unsigned' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('company_id');
        $this->forge->addForeignKey('company_id', 'companies', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('lenders');
    }

    private function createLoansTable(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'fleet_vehicle_id' => ['type' => 'INT', 'unsigned' => true],
            'lender_id' => ['type' => 'INT', 'unsigned' => true],
            'loan_status_lookup_value_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'account_number_last4' => ['type' => 'VARCHAR', 'constraint' => 4, 'null' => true],
            'original_principal' => ['type' => 'DECIMAL', 'constraint' => '12,2', 'null' => true],
            'current_balance' => ['type' => 'DECIMAL', 'constraint' => '12,2', 'null' => true],
            'interest_rate' => ['type' => 'DECIMAL', 'constraint' => '6,4', 'null' => true],
            'monthly_payment' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => true],
            'term_months' => ['type' => 'SMALLINT', 'unsigned' => true, 'null' => true],
            'opened_on' => ['type' => 'DATE', 'null' => true],
            'matures_on' => ['type' => 'DATE', 'null' => true],
            'paid_off_on' => ['type' => 'DATE', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['fleet_vehicle_id', 'loan_status_lookup_value_id']);
        $this->forge->addForeignKey('fleet_vehicle_id', 'fleet_vehicles', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('lender_id', 'lenders', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('loan_status_lookup_value_id', 'lookup_values', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('loans');
    }

    private function createInsurancePoliciesTable(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'fleet_vehicle_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'insurer_company_id' => ['type' => 'INT', 'unsigned' => true],
            'policy_status_lookup_value_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'policy_number' => ['type' => 'VARCHAR', 'constraint' => 120],
            'coverage_type_lookup_value_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'premium_amount' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => true],
            'deductible_amount' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => true],
            'effective_on' => ['type' => 'DATE'],
            'expires_on' => ['type' => 'DATE'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['fleet_vehicle_id', 'policy_status_lookup_value_id']);
        $this->forge->addUniqueKey(['insurer_company_id', 'policy_number']);
        $this->forge->addForeignKey('fleet_vehicle_id', 'fleet_vehicles', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('insurer_company_id', 'companies', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('policy_status_lookup_value_id', 'lookup_values', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('coverage_type_lookup_value_id', 'lookup_values', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('insurance_policies');
    }

    private function createRegistrationsTable(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'fleet_vehicle_id' => ['type' => 'INT', 'unsigned' => true],
            'state_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'registration_status_lookup_value_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'registration_number' => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true],
            'plate_number' => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => true],
            'registered_on' => ['type' => 'DATE', 'null' => true],
            'expires_on' => ['type' => 'DATE', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['fleet_vehicle_id', 'expires_on']);
        $this->forge->addForeignKey('fleet_vehicle_id', 'fleet_vehicles', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('state_id', 'states', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('registration_status_lookup_value_id', 'lookup_values', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('registrations');
    }

    private function createStartupCostsTable(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'fleet_vehicle_id' => ['type' => 'INT', 'unsigned' => true],
            'cost_type_lookup_value_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'description' => ['type' => 'VARCHAR', 'constraint' => 190],
            'amount' => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'incurred_on' => ['type' => 'DATE'],
            'file_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['fleet_vehicle_id', 'incurred_on']);
        $this->forge->addForeignKey('fleet_vehicle_id', 'fleet_vehicles', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('cost_type_lookup_value_id', 'lookup_values', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('file_id', 'files', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('startup_costs');
    }

    private function createMaintenanceLogsTable(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'fleet_vehicle_id' => ['type' => 'INT', 'unsigned' => true],
            'vendor_company_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'maintenance_type_lookup_value_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'maintenance_status_lookup_value_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'service_on' => ['type' => 'DATE', 'null' => true],
            'odometer_miles' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'description' => ['type' => 'TEXT', 'null' => true],
            'labor_amount' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0],
            'parts_amount' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0],
            'tax_amount' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0],
            'total_amount' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['fleet_vehicle_id', 'service_on']);
        $this->forge->addForeignKey('fleet_vehicle_id', 'fleet_vehicles', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('vendor_company_id', 'companies', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('maintenance_type_lookup_value_id', 'lookup_values', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('maintenance_status_lookup_value_id', 'lookup_values', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('maintenance_logs');
    }

    private function createDamageClaimsTable(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'fleet_vehicle_id' => ['type' => 'INT', 'unsigned' => true],
            'claim_status_lookup_value_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'claim_source_lookup_value_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'turo_trip_normalized_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
            'claim_number' => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true],
            'damage_occurred_on' => ['type' => 'DATE', 'null' => true],
            'reported_on' => ['type' => 'DATE', 'null' => true],
            'description' => ['type' => 'TEXT', 'null' => true],
            'estimated_repair_amount' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => true],
            'approved_reimbursement_amount' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => true],
            'paid_amount' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => true],
            'closed_on' => ['type' => 'DATE', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['fleet_vehicle_id', 'claim_status_lookup_value_id']);
        $this->forge->addKey('turo_trip_normalized_id');
        $this->forge->addForeignKey('fleet_vehicle_id', 'fleet_vehicles', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('claim_status_lookup_value_id', 'lookup_values', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('claim_source_lookup_value_id', 'lookup_values', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('damage_claims');
    }

    private function createChargingSessionsTable(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'fleet_vehicle_id' => ['type' => 'INT', 'unsigned' => true],
            'charging_provider_lookup_value_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'charging_location' => ['type' => 'VARCHAR', 'constraint' => 190, 'null' => true],
            'started_at' => ['type' => 'DATETIME', 'null' => true],
            'ended_at' => ['type' => 'DATETIME', 'null' => true],
            'kwh' => ['type' => 'DECIMAL', 'constraint' => '8,3', 'null' => true],
            'cost_amount' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0],
            'odometer_miles' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'turo_trip_normalized_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['fleet_vehicle_id', 'started_at']);
        $this->forge->addKey('turo_trip_normalized_id');
        $this->forge->addForeignKey('fleet_vehicle_id', 'fleet_vehicles', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('charging_provider_lookup_value_id', 'lookup_values', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('charging_sessions');
    }

    private function createAirportsTable(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'code' => ['type' => 'VARCHAR', 'constraint' => 10],
            'name' => ['type' => 'VARCHAR', 'constraint' => 190],
            'city_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('code');
        $this->forge->addKey('city_id');
        $this->forge->addForeignKey('city_id', 'cities', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('airports');
    }

    private function createAirportDeliveriesTable(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'fleet_vehicle_id' => ['type' => 'INT', 'unsigned' => true],
            'airport_id' => ['type' => 'INT', 'unsigned' => true],
            'delivery_status_lookup_value_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'turo_trip_normalized_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
            'scheduled_at' => ['type' => 'DATETIME', 'null' => true],
            'completed_at' => ['type' => 'DATETIME', 'null' => true],
            'delivery_fee_amount' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0],
            'parking_cost_amount' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['fleet_vehicle_id', 'scheduled_at']);
        $this->forge->addKey('airport_id');
        $this->forge->addKey('turo_trip_normalized_id');
        $this->forge->addForeignKey('fleet_vehicle_id', 'fleet_vehicles', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('airport_id', 'airports', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('delivery_status_lookup_value_id', 'lookup_values', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('airport_deliveries');
    }

    private function createOperationalPivotTables(): void
    {
        $this->createDamageClaimNotesTable();
        $this->createDamageClaimFilesTable();
        $this->createMaintenanceLogNotesTable();
        $this->createMaintenanceLogFilesTable();
    }

    private function createDamageClaimNotesTable(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'damage_claim_id' => ['type' => 'INT', 'unsigned' => true],
            'note_id' => ['type' => 'INT', 'unsigned' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['damage_claim_id', 'note_id']);
        $this->forge->addForeignKey('damage_claim_id', 'damage_claims', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('note_id', 'notes', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('damage_claim_notes');
    }

    private function createDamageClaimFilesTable(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'damage_claim_id' => ['type' => 'INT', 'unsigned' => true],
            'file_id' => ['type' => 'INT', 'unsigned' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['damage_claim_id', 'file_id']);
        $this->forge->addForeignKey('damage_claim_id', 'damage_claims', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('file_id', 'files', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('damage_claim_files');
    }

    private function createMaintenanceLogNotesTable(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'maintenance_log_id' => ['type' => 'INT', 'unsigned' => true],
            'note_id' => ['type' => 'INT', 'unsigned' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['maintenance_log_id', 'note_id'], 'maintenance_log_notes_unique');
        $this->forge->addForeignKey('maintenance_log_id', 'maintenance_logs', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('note_id', 'notes', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('maintenance_log_notes');
    }

    private function createMaintenanceLogFilesTable(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'maintenance_log_id' => ['type' => 'INT', 'unsigned' => true],
            'file_id' => ['type' => 'INT', 'unsigned' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['maintenance_log_id', 'file_id'], 'maintenance_log_files_unique');
        $this->forge->addForeignKey('maintenance_log_id', 'maintenance_logs', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('file_id', 'files', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('maintenance_log_files');
    }
}
