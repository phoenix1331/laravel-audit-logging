<?php

declare(strict_types=1);

namespace App\Observers;

use App\Contracts\AuditableInterface;
use App\Enums\AuditLogAction;
use App\Services\AuditLogger;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;

class AuditObserver
{
    public function __construct(
        private readonly AuditLogger $auditLogger,
    ) {}

    public function created(Model&AuditableInterface $model): void
    {
        if (! in_array(AuditLogAction::CREATE, $model->getAuditActions(), true)) {
            return;
        }

        $this->auditLogger->log(
            $model->getAuditType(),
            AuditLogAction::CREATE,
            $this->getObjectReference($model),
        );
    }

    public function updated(Model&AuditableInterface $model): void
    {
        if (! in_array(AuditLogAction::UPDATE, $model->getAuditActions(), true)) {
            return;
        }

        $changes = $model->getChanges();
        unset($changes['updated_at']);

        if (empty($changes)) {
            return;
        }

        $original = $model->getOriginal();
        $eventData = $this->formatChangeSet($changes, $original);
        $eventData['entity_id'] = $model->getKey();

        $this->auditLogger->log(
            $model->getAuditType(),
            AuditLogAction::UPDATE,
            $eventData,
        );
    }

    public function deleted(Model&AuditableInterface $model): void
    {
        if (! in_array(AuditLogAction::DELETE, $model->getAuditActions(), true)) {
            return;
        }

        $this->auditLogger->log(
            $model->getAuditType(),
            AuditLogAction::DELETE,
            $this->getObjectReference($model),
        );
    }

    /**
     * Build a from/to change set for each modified field.
     */
    private function formatChangeSet(array $changes, array $original): array
    {
        $result = [];

        foreach ($changes as $field => $newValue) {
            $result[$field] = [
                'from' => $this->formatValue($original[$field] ?? null),
                'to' => $this->formatValue($newValue),
            ];
        }

        return $result;
    }

    private function formatValue(mixed $value): mixed
    {
        if ($value instanceof CarbonInterface || $value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }

        if (is_object($value) && method_exists($value, 'getKey')) {
            return $this->getObjectReference($value);
        }

        return $value;
    }

    /**
     * Return a reference array identifying the model by class and primary key.
     */
    private function getObjectReference(Model $model): array
    {
        return [
            'object' => get_class($model),
            'entity_id' => $model->getKey(),
        ];
    }
}
