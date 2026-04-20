<?php
// app/Domain/Report/Models/Report.php
namespace App\Domain\Report\Models;

use App\Support\Traits\BelongsToClient;
use App\Domain\Client\Models\Client;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Report extends Model
{
    use BelongsToClient, SoftDeletes;

    protected $fillable = [
        'uuid', 'client_id', 'title', 'reference_month', 'status',
        // Resumo
        'summary', 'next_month_goals',
        // Tráfego pago
        'investment', 'revenue', 'paid_conversations', 'cpc',
        // Instagram
        'ig_publications', 'ig_interactions', 'ig_reach',
        'ig_new_followers', 'ig_views', 'ig_profile_visits',
        // Conteúdos e público
        'top_contents', 'audience_locations', 'audience_age', 'audience_gender',
        // Controle
        'published_at', 'published_by', 'current_version',
    ];

    protected function casts(): array
    {
        return [
            'reference_month'    => 'date',
            'investment'         => 'decimal:2',
            'revenue'            => 'decimal:2',
            'cpc'                => 'decimal:2',
            'roi'                => 'decimal:2',
            'top_contents'       => 'array',
            'audience_locations' => 'array',
            'audience_age'       => 'array',
            'audience_gender'    => 'array',
            'published_at'       => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(fn(Report $r) => $r->uuid = (string) Str::uuid());
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function getRouteKeyName(): string { return 'uuid'; }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function versions(): HasMany
    {
        return $this->hasMany(ReportVersion::class);
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'published_by');
    }

    // Helpers para exibição
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'published' => 'Publicado',
            'draft'     => 'Rascunho',
            'archived'  => 'Arquivado',
            default     => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'published' => 'badge-published',
            'draft'     => 'badge-draft',
            'archived'  => 'badge-archived',
            default     => 'badge-gray',
        };
    }
}