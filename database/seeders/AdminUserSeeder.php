<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'enzoterra18@gmail.com'],
            [
                'name'      => 'Enzo Terra',
                'password'  => Hash::make('Engipa2016!'),
                'role'      => 'super_admin',
                'is_active' => true,
            ]
        );

        $admin->assignRole('super_admin');
    }
}