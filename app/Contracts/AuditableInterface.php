<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Enums\AuditLogAction;
use App\Enums\AuditLogType;

interface AuditableInterface
{
    /**
     * Return the audit log type that identifies this model.
     * Each implementing model should return a specific AuditLogType enum case.
     */
    public function getAuditType(): AuditLogType;

    /**
     * Return the list of actions that should be audited for this model.
     * Returning a subset allows opting out of specific events (e.g. no DELETE logging).
     *
     * Example: return [AuditLogAction::CREATE, AuditLogAction::UPDATE, AuditLogAction::DELETE];
     *
     * @return AuditLogAction[]
     */
    public function getAuditActions(): array;
}
