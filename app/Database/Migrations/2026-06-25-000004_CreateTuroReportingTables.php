<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTuroReportingTables extends Migration
{
    public function up(): void
    {
        $this->createTuroImportBatchesTable();
        $this->createTuroTripRawTable();
        $this->createTuroTransactionRawTable();
        $this->createTuroTripsNormalizedTable();
        $this->createTripMonthAllocationsTable();
    }

    public function down(): void
    {
        $this->forge->dropTable('trip_month_allocations', true);
        $this->forge->dropTable('turo_trips_normalized', true);
        $this->forge->dropTable('turo_transaction_raw', true);
        $this->forge->dropTable('turo_trip_raw', true);
        $this->forge->dropTable('turo_import_batches', true);
    }

    private function createTuroImportBatchesTable(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'import_type_lookup_value_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'import_status_lookup_value_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'source_filename' => ['type' => 'VARCHAR', 'constraint' => 190, 'null' => true],
            'source_hash' => ['type' => 'VARCHAR', 'constraint' => 128],
            'row_count' => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
            'started_at' => ['type' => 'DATETIME', 'null' => true],
            'completed_at' => ['type' => 'DATETIME', 'null' => true],
            'error_message' => ['type' => 'TEXT', 'null' => true],
            'created_by' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['import_type_lookup_value_id', 'import_status_lookup_value_id']);
        $this->forge->addUniqueKey('source_hash');
        $this->forge->addForeignKey('import_type_lookup_value_id', 'lookup_values', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('import_status_lookup_value_id', 'lookup_values', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('turo_import_batches');
    }

    private function createTuroTripRawTable(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'turo_import_batch_id' => ['type' => 'INT', 'unsigned' => true],
            'external_trip_id' => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => true],
            'external_vehicle_id' => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => true],
            'row_number' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'row_hash' => ['type' => 'VARCHAR', 'constraint' => 128],
            'raw_payload' => ['type' => 'JSON'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['turo_import_batch_id', 'external_trip_id']);
        $this->forge->addUniqueKey(['turo_import_batch_id', 'row_hash'], 'turo_trip_raw_batch_hash_unique');
        $this->forge->addForeignKey('turo_import_batch_id', 'turo_import_batches', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('turo_trip_raw');
    }

    private function createTuroTransactionRawTable(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'turo_import_batch_id' => ['type' => 'INT', 'unsigned' => true],
            'external_transaction_id' => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => true],
            'external_trip_id' => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => true],
            'transaction_date' => ['type' => 'DATE', 'null' => true],
            'row_number' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'row_hash' => ['type' => 'VARCHAR', 'constraint' => 128],
            'raw_payload' => ['type' => 'JSON'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['turo_import_batch_id', 'external_trip_id']);
        $this->forge->addUniqueKey(['turo_import_batch_id', 'row_hash'], 'turo_transaction_raw_batch_hash_unique');
        $this->forge->addForeignKey('turo_import_batch_id', 'turo_import_batches', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('turo_transaction_raw');
    }

    private function createTuroTripsNormalizedTable(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'fleet_vehicle_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'turo_trip_raw_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
            'trip_status_lookup_value_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'turo_trip_id' => ['type' => 'VARCHAR', 'constraint' => 80],
            'turo_reservation_id' => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => true],
            'guest_name' => ['type' => 'VARCHAR', 'constraint' => 190, 'null' => true],
            'booked_at' => ['type' => 'DATETIME', 'null' => true],
            'starts_at' => ['type' => 'DATETIME'],
            'ends_at' => ['type' => 'DATETIME'],
            'canceled_at' => ['type' => 'DATETIME', 'null' => true],
            'trip_days' => ['type' => 'DECIMAL', 'constraint' => '8,3', 'default' => 0],
            'billable_days' => ['type' => 'DECIMAL', 'constraint' => '8,3', 'default' => 0],
            'gross_revenue_amount' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0],
            'host_payout_amount' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0],
            'delivery_fee_amount' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0],
            'discount_amount' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0],
            'reimbursement_amount' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0],
            'airport_fee_amount' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0],
            'currency_code' => ['type' => 'CHAR', 'constraint' => 3, 'default' => 'USD'],
            'is_forecast' => ['type' => 'BOOLEAN', 'default' => false],
            'normalized_at' => ['type' => 'DATETIME', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('turo_trip_id');
        $this->forge->addKey(['fleet_vehicle_id', 'starts_at', 'ends_at']);
        $this->forge->addKey(['trip_status_lookup_value_id', 'is_forecast']);
        $this->forge->addForeignKey('fleet_vehicle_id', 'fleet_vehicles', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('turo_trip_raw_id', 'turo_trip_raw', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('trip_status_lookup_value_id', 'lookup_values', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('turo_trips_normalized');
    }

    private function createTripMonthAllocationsTable(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'turo_trip_normalized_id' => ['type' => 'BIGINT', 'unsigned' => true],
            'fleet_vehicle_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'allocation_month' => ['type' => 'DATE'],
            'allocation_starts_at' => ['type' => 'DATETIME'],
            'allocation_ends_at' => ['type' => 'DATETIME'],
            'allocated_trip_days' => ['type' => 'DECIMAL', 'constraint' => '8,3', 'default' => 0],
            'allocated_billable_days' => ['type' => 'DECIMAL', 'constraint' => '8,3', 'default' => 0],
            'allocated_gross_revenue_amount' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0],
            'allocated_host_payout_amount' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0],
            'allocated_delivery_fee_amount' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0],
            'allocated_reimbursement_amount' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0],
            'is_forecast' => ['type' => 'BOOLEAN', 'default' => false],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['turo_trip_normalized_id', 'allocation_month'], 'trip_month_allocations_trip_month_unique');
        $this->forge->addKey(['fleet_vehicle_id', 'allocation_month', 'is_forecast'], false, false, 'trip_month_allocations_reporting_index');
        $this->forge->addForeignKey('turo_trip_normalized_id', 'turo_trips_normalized', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('fleet_vehicle_id', 'fleet_vehicles', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('trip_month_allocations');
    }
}
