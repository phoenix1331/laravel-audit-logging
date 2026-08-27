<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\AuditLogAction;
use App\Enums\AuditLogType;
use App\Models\AuditLog;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

class AuditLogger
{
    public function __construct(
        private readonly Request $request,
        private readonly Guard $auth,
    ) {}

    public function log(AuditLogType $type, AuditLogAction $action, array $eventData = []): AuditLog
    {
        return AuditLog::create([
            'type' => $type,
            'action' => $action,
            'event_data' => $eventData,
            'user_id' => $this->auth->id(),
            'ip_address' => $this->request->ip(),
            'user_agent' => $this->request->userAgent(),
            'request_route' => $this->request->route()?->getName(),
            'organisation_id' => $this->resolveOrganisationId(),
        ]);
    }

    /**
     * Resolve the organisation ID from the Origin header subdomain.
     * Extend this method to look up the organisation by subdomain in your database,
     * e.g. return Organisation::where('subdomain', $subdomain)->value('id');
     */
    private function resolveOrganisationId(): ?int
    {
        $origin = $this->request->header('Origin');

        if (! $origin) {
            return null;
        }

        $parts = parse_url($origin);

        if (! $parts || ! isset($parts['host'])) {
            return null;
        }

        $subdomain = explode('.', $parts['host'])[0] ?? null;

        return null;
    }
}
