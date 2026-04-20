<?php

namespace App\Domain\Financial\Models;

use App\Support\Traits\BelongsToClient;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Payment extends Model
{
    use BelongsToClient, SoftDeletes;

    protected $fillable = [
        'uuid', 'client_id', 'invoice_id', 'amount', 'due_date',
        'paid_at', 'payment_method', 'status',
        'pix_qr_code', 'pix_key', 'reference', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount'  => 'decimal:2',
            'due_date' => 'date',
            'paid_at'  => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(fn(Payment $p) => $p->uuid = (string) Str::uuid());
    }

    public function scopePending($query)      { return $query->where('status', 'pending'); }
    public function scopeOverdue($query)      { return $query->where('status', 'overdue'); }
    public function scopeUnderReview($query)  { return $query->where('status', 'under_review'); }
    public function scopePaid($query)         { return $query->where('status', 'paid'); }

    public function getRouteKeyName(): string { return 'uuid'; }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(\App\Domain\Client\Models\Client::class);
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isUnderReview(): bool
    {
        return $this->status === 'under_review';
    }

    public function isOverdue(): bool
    {
        if ($this->status === 'overdue') {
            return true;
        }

        return $this->status === 'pending' && $this->due_date && $this->due_date->isPast();
    }
}