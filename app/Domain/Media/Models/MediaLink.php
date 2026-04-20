<?php

namespace App\Domain\Media\Models;

use App\Support\Traits\BelongsToClient;
use App\Domain\Client\Models\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MediaLink extends Model
{
    use HasUuids, SoftDeletes, BelongsToClient;

    protected $fillable = [
        'client_id',
        'title',
        'description',
        'url',
        'type',
        'month',
        'year',
        'thumbnail_url',
        'is_public',
        'sort_order',
    ];

    protected $casts = [
        'month'      => 'integer',
        'year'       => 'integer',
        'is_public'  => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Route model binding via uuid (anti-IDOR)
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    // ---- Relationships ----

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    // ---- Scopes ----

    public function scopeForMonth($query, int $month, int $year)
    {
        return $query->where('month', $month)->where('year', $year);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Define custom columns that should have UUIDs generated.
     */
    public function uniqueIds(): array
    {
        return ['uuid'];
    }
}
