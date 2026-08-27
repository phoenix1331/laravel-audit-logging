<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\AuditableInterface;
use App\Enums\AuditLogAction;
use App\Enums\AuditLogType;
use Illuminate\Database\Eloquent\Model;

class Post extends Model implements AuditableInterface
{
    protected $fillable = [
        'title',
        'body',
        'author_id',
        'published_at',
        'is_archived',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_archived' => 'boolean',
    ];

    public function getAuditType(): AuditLogType
    {
        return AuditLogType::POST;
    }

    public function getAuditActions(): array
    {
        return [
            AuditLogAction::CREATE,
            AuditLogAction::UPDATE,
            AuditLogAction::DELETE,
        ];
    }
}
