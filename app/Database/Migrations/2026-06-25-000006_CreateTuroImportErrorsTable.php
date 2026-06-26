<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTuroImportErrorsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'turo_import_batch_id' => ['type' => 'INT', 'unsigned' => true],
            'severity_lookup_value_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'raw_table' => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => true],
            'raw_row_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
            'row_number' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'error_code' => ['type' => 'VARCHAR', 'constraint' => 120],
            'field_name' => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true],
            'message' => ['type' => 'TEXT'],
            'raw_payload' => ['type' => 'JSON', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['turo_import_batch_id', 'row_number']);
        $this->forge->addKey('severity_lookup_value_id');
        $this->forge->addKey(['raw_table', 'raw_row_id']);
        $this->forge->addForeignKey('turo_import_batch_id', 'turo_import_batches', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('severity_lookup_value_id', 'lookup_values', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('turo_import_errors');
    }

    public function down(): void
    {
        $this->forge->dropTable('turo_import_errors', true);
    }
}
