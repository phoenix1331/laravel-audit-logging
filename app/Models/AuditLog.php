<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AuditLogAction;
use App\Enums\AuditLogType;
use MongoDB\Laravel\Eloquent\Model;

class AuditLog extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'audit_logs';

    protected $fillable = [
        'type',
        'action',
        'request_route',
        'event_data',
        'ip_address',
        'user_agent',
        'user_id',
        'organisation_id',
    ];

    protected $casts = [
        'type' => AuditLogType::class,
        'action' => AuditLogAction::class,
        'user_id' => 'integer',
        'organisation_id' => 'integer',
    ];
}
