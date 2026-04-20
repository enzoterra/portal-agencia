<?php

namespace Database\Seeders;

use App\Domain\Setting\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            // Google
            ['key' => 'google_client_id',     'value' => null,                           'type' => 'string',  'group' => 'google'],
            ['key' => 'google_client_secret',  'value' => null,                           'type' => 'secret',  'group' => 'google'],
            ['key' => 'google_redirect_uri',   'value' => url('/admin/google/callback'),  'type' => 'string',  'group' => 'google'],
            // Security
            ['key' => 'login_max_attempts',   'value' => '5',                            'type' => 'integer', 'group' => 'security'],
            ['key' => 'login_decay_minutes',  'value' => '1',                            'type' => 'integer', 'group' => 'security'],
            ['key' => 'session_lifetime',     'value' => '120',                          'type' => 'integer', 'group' => 'security'],
            ['key' => 'force_https',          'value' => '1',                            'type' => 'boolean', 'group' => 'security'],
            // Financeiro
            ['key' => 'pix_key', 'value' => null, 'type' => 'string', 'group' => 'general'],
            ['key' => 'pix_name', 'value' => null, 'type' => 'string', 'group' => 'general'],
            ['key' => 'pix_city', 'value' => null, 'type' => 'string', 'group' => 'general'],
        ];

        foreach ($defaults as $setting) {
            Setting::firstOrCreate(['key' => $setting['key']], $setting);
        }
    }
}