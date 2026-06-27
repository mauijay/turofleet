<?php

namespace App\DTOs\Fleet;

use DateTimeImmutable;

final readonly class Recommendation
{
    /** @param array<string, int|float|string|null> $metrics */
    public function __construct(
        public string $title,
        public string $category,
        public string $priority,
        public int $confidence,
        public string $reason,
        public array $metrics,
        public string $action,
        public DateTimeImmutable $generatedAt,
        public DateTimeImmutable $expiresAt,
        public string $sourceService,
    ) {
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'category' => $this->category,
            'priority' => $this->priority,
            'confidence' => $this->confidence,
            'reason' => $this->reason,
            'metrics' => $this->metrics,
            'action' => $this->action,
            'generated_at' => $this->generatedAt->format('Y-m-d H:i:s'),
            'expires_at' => $this->expiresAt->format('Y-m-d H:i:s'),
            'source_service' => $this->sourceService,
        ];
    }
}
