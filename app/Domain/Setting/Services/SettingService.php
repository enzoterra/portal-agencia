<?php

namespace App\Domain\Setting\Services;

use App\Domain\Setting\Models\Setting;
use Illuminate\Support\Collection;

class SettingService
{
    // Cache em memória durante a request — evita N queries
    private ?Collection $cache = null;

    // Chaves que devem ser criptografadas no banco
    private const SECRET_KEYS = [
        'google_client_secret',
    ];

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->all()->get($key, $default);
    }

    public function set(string $key, mixed $value): void
    {
        $isSecret = in_array($key, self::SECRET_KEYS, true);

        Setting::updateOrCreate(
            ['key' => $key],
            [
                'value'  => $value,
                'type'   => $isSecret ? 'secret' : $this->inferType($value),
                'group'  => $this->inferGroup($key),
            ]
        );

        // Invalida cache em memória para a próxima leitura
        $this->cache = null;
    }

    public function all(): Collection
    {
        if ($this->cache === null) {
            $this->cache = Setting::all()->pluck('cast_value', 'key');
        }

        return $this->cache;
    }

    private function inferType(mixed $value): string
    {
        return match(true) {
            is_bool($value)    => 'boolean',
            is_int($value)     => 'integer',
            default            => 'string',
        };
    }

    private function inferGroup(string $key): string
    {
        return match(true) {
            str_starts_with($key, 'google_')  => 'google',
            str_starts_with($key, 'login_')   => 'security',
            str_starts_with($key, 'session_') => 'security',
            str_starts_with($key, 'force_')   => 'security',
            default                           => 'general',
        };
    }
}
