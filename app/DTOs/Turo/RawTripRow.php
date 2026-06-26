<?php

namespace App\DTOs\Turo;

class RawTripRow
{
    /** @param array<string, string|null> $payload */
    public function __construct(
        public readonly int $rowNumber,
        public readonly array $payload,
        public readonly ?string $externalTripId,
        public readonly ?string $externalVehicleId,
        public readonly string $rowHash,
    ) {
    }
}
