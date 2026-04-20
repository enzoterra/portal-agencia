<?php

namespace App\Models;

use App\Domain\Client\Models\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoogleOAuthToken extends Model
{
    protected $table = 'google_oauth_tokens';

    protected $fillable = [
        'access_token', 'refresh_token',
        'token_type', 'expires_at', 'scopes',
    ];

    protected function casts(): array
    {
        return [
            'access_token'  => 'encrypted', // AES-256 automático
            'refresh_token' => 'encrypted',
            'scopes'        => 'array',
            'expires_at'    => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}
