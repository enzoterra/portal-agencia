<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public $timestamps = false;
    const CREATED_AT = 'created_at';

    protected $fillable = [
        'user_id', 'client_id', 'action',
        'auditable_type', 'auditable_id',
        'old_values', 'new_values',
        'ip_address', 'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(\App\Domain\Client\Models\Client::class);
    }

    public static function record(string $action, Model $subject, ?User $user = null): void
    {
        static::create([
            'user_id'        => $user?->id ?? auth()->id(),
            'client_id'      => $subject->client_id ?? null,
            'action'         => $action,
            'auditable_type' => get_class($subject),
            'auditable_id'   => $subject->id,
            'ip_address'     => request()->ip(),
            'user_agent'     => request()->userAgent(),
        ]);
    }
}