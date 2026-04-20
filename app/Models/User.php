<?php

namespace App\Models;

use App\Domain\Client\Models\Client;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles {
        hasRole as protected spatieHasRole;
    }
    use Notifiable, SoftDeletes;

    protected $fillable = [
        'client_id', 'name', 'email', 'password',
        'role', 'is_active', 'last_login_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password'      => 'hashed',
            'is_active'     => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function isClient(): bool
    {
        return !is_null($this->client_id) && $this->hasRole('client');
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(['admin', 'super_admin']);
    }

    public function hasRole($roles, $guard = null): bool
    {
        if (is_string($roles) && $roles === $this->role) {
            return true;
        }
        
        if (is_array($roles) && in_array($this->role, $roles)) {
            return true;
        }

        return $this->spatieHasRole($roles, $guard);
    }
}