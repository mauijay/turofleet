<?php

namespace App\Repositories;

use CodeIgniter\Database\BaseConnection;
use Config\Database;

class TuroImportErrorRepository
{
    private BaseConnection $db;

    public function __construct(?BaseConnection $db = null)
    {
        $this->db = $db ?? Database::connect();
    }

    public function create(array $data): int
    {
        if (isset($data['raw_payload']) && is_array($data['raw_payload'])) {
            $data['raw_payload'] = json_encode($data['raw_payload'], JSON_THROW_ON_ERROR);
        }

        $this->db->table('turo_import_errors')->insert(array_merge($data, [
            'created_at' => $data['created_at'] ?? date('Y-m-d H:i:s'),
        ]));

        return (int) $this->db->insertID();
    }
}
