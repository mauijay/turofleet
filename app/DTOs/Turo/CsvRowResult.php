<?php

namespace App\DTOs\Turo;

class CsvRowResult
{
    /** @param array<string, string|null> $row */
    public function __construct(
        public readonly int $rowNumber,
        public readonly array $row,
    ) {
    }
}
