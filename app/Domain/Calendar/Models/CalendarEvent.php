<?php

namespace App\Domain\Calendar\Models;

use App\Domain\Client\Models\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CalendarEvent extends Model
{
    // Sem BelongsToClient — query manual no controller
    use HasUuids;

    protected $fillable = [
        'client_id',
        'google_event_id',
        'google_calendar_id',
        'title',
        'description',
        'location',
        'starts_at',
        'ends_at',
        'all_day',
        'status',
        'color',
        'synced_at',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
        'synced_at' => 'datetime',
        'all_day'   => 'boolean',
    ];

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Define custom columns that should have UUIDs generated.
     */
    public function uniqueIds(): array
    {
        return ['uuid'];
    }
}