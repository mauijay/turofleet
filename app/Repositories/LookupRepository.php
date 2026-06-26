<?php

namespace App\Repositories;

use CodeIgniter\Database\BaseConnection;
use Config\Database;
use RuntimeException;

class LookupRepository
{
    private BaseConnection $db;

    /** @var array<string, int> */
    private array $valueIds = [];

    public function __construct(?BaseConnection $db = null)
    {
        $this->db = $db ?? Database::connect();
    }

    public function valueId(string $typeCode, string $valueCode): int
    {
        $cacheKey = $typeCode . ':' . $valueCode;

        if (isset($this->valueIds[$cacheKey])) {
            return $this->valueIds[$cacheKey];
        }

        $row = $this->db->table('lookup_values')
            ->select('lookup_values.id')
            ->join('lookup_types', 'lookup_types.id = lookup_values.lookup_type_id')
            ->where('lookup_types.code', $typeCode)
            ->where('lookup_values.code', $valueCode)
            ->where('lookup_values.is_active', true)
            ->get()
            ->getRowArray();

        if ($row === null) {
            throw new RuntimeException("Missing lookup value {$typeCode}.{$valueCode}");
        }

        return $this->valueIds[$cacheKey] = (int) $row['id'];
    }
}
