<?php

namespace App\Domain\Client\Models;

use App\Domain\Calendar\Models\CalendarEvent;
use App\Domain\Financial\Models\Invoice;
use App\Domain\Financial\Models\Payment;
use App\Domain\Media\Models\MediaLink;
use App\Domain\Report\Models\Report;
use App\Models\GoogleOAuthToken;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Client extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid', 'company_name', 'trade_name', 'cnpj',
        'email', 'phone', 'address', 'contract_start',
        'contract_end', 'monthly_fee', 'show_roi', 'status', 'notes', 'settings',
    ];

    protected function casts(): array
    {
        return [
            'address'        => 'array',
            'settings'       => 'array',
            'contract_start' => 'date',
            'contract_end'   => 'date',
            'monthly_fee'    => 'decimal:2',
            'show_roi'       => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Client $client) {
            $client->uuid = (string) Str::uuid();
        });
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    // Relacionamentos
    public function users(): HasMany          { return $this->hasMany(User::class); }
    public function payments(): HasMany       { return $this->hasMany(Payment::class); }
    public function invoices(): HasMany       { return $this->hasMany(Invoice::class); }
    public function reports(): HasMany        { return $this->hasMany(Report::class); }
    public function mediaLinks(): HasMany     { return $this->hasMany(MediaLink::class); }
    public function calendarEvents(): HasMany { return $this->hasMany(CalendarEvent::class); }
}