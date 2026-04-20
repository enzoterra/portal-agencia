<?php

namespace App\Domain\Financial\Models;

use App\Support\Traits\BelongsToClient;
use App\Domain\Client\Models\Client;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Invoice extends Model
{
    use BelongsToClient, SoftDeletes;

    protected $fillable = [
        'uuid',
        'client_id',
        'invoice_number',
        'amount',
        'issue_date',
        'due_date',
        'file_path',
        'description',
        'reference_month',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'issue_date' => 'date',
            'due_date' => 'date',
            'reference_month' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::creating(fn(Invoice $i) => $i->uuid = (string) Str::uuid());
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
