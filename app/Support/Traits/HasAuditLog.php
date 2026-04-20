<?php

namespace App\Support\Traits;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

trait HasAuditLog
{
    /**
     * Record an administrative action in the audit log.
     */
    protected function recordActivity(string $action, Model $subject, ?array $oldValues = null, ?array $newValues = null): void
    {
        AuditLog::create([
            'user_id'        => auth()->id(),
            'client_id'      => $subject->client_id ?? null,
            'action'         => $action,
            'auditable_type' => get_class($subject),
            'auditable_id'   => $subject->id,
            'old_values'     => $oldValues,
            'new_values'     => $newValues,
            'ip_address'     => request()->ip(),
            'user_agent'     => request()->userAgent(),
        ]);
    }
}
