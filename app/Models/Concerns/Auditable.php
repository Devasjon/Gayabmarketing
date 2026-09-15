<?php

namespace App\Models\Concerns;

use App\Models\AuditLog;

trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(fn ($model) => $model->writeAuditLog('created', $model->attributesToArray()));

        static::updated(function ($model) {
            $changes = $model->getChanges();
            unset($changes['updated_at']);

            if ($changes !== []) {
                $model->writeAuditLog('updated', ['before' => array_intersect_key($model->getOriginal(), $changes), 'after' => $changes]);
            }
        });

        static::deleted(fn ($model) => $model->writeAuditLog('deleted', null));
    }

    protected function writeAuditLog(string $event, ?array $changes): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'event' => $event,
            'auditable_type' => static::class,
            'auditable_id' => $this->getKey(),
            'changes' => $changes,
            'ip_address' => request()->ip(),
            'created_at' => now(),
        ]);
    }
}
