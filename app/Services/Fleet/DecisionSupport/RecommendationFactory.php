<?php

namespace App\Services\Fleet\DecisionSupport;

use App\DTOs\Fleet\Recommendation;
use Config\DecisionSupport;
use DateTimeImmutable;

final readonly class RecommendationFactory
{
    public function __construct(private DecisionSupport $config)
    {
    }

    /** @param array<string, int|float|string|null> $metrics */
    public function make(
        string $title,
        string $category,
        string $priority,
        int $confidence,
        string $reason,
        array $metrics,
        string $action,
        DateTimeImmutable $generatedAt,
        string $sourceService,
    ): Recommendation {
        return new Recommendation(
            $title,
            $category,
            $priority,
            max(0, min(100, $confidence)),
            $reason,
            $metrics,
            $action,
            $generatedAt,
            $generatedAt->modify('+' . $this->config->recommendationTtlDays . ' days'),
            $sourceService,
        );
    }
}
