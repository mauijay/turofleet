<?php

namespace App\Services\Turo;

use App\Repositories\AuditLogRepository;
use App\Repositories\LookupRepository;

class TuroImportAuditService
{
    public function __construct(
        private readonly AuditLogRepository $auditLogs = new AuditLogRepository(),
        private readonly LookupRepository $lookups = new LookupRepository(),
    ) {
    }

    public function imported(?int $actorUserId, string $tableName, int $recordId, ?array $newValues = null): void
    {
        $this->auditLogs->record($actorUserId, $this->lookups->valueId('audit_action', 'imported'), $tableName, $recordId, null, $newValues);
    }

    public function created(?int $actorUserId, string $tableName, int $recordId, ?array $newValues = null): void
    {
        $this->auditLogs->record($actorUserId, $this->lookups->valueId('audit_action', 'created'), $tableName, $recordId, null, $newValues);
    }

    public function updated(?int $actorUserId, string $tableName, int $recordId, ?array $oldValues = null, ?array $newValues = null): void
    {
        $this->auditLogs->record($actorUserId, $this->lookups->valueId('audit_action', 'updated'), $tableName, $recordId, $oldValues, $newValues);
    }
}
