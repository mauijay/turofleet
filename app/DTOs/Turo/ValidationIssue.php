<?php

namespace App\DTOs\Turo;

class ValidationIssue
{
    public function __construct(
        public readonly string $code,
        public readonly string $message,
        public readonly ?string $fieldName = null,
        public readonly string $severity = 'error',
    ) {
    }
}
