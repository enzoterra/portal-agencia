<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Clientes
            'clients.view',
            'clients.create',
            'clients.edit',
            'clients.delete',

            // Relatórios
            'reports.view',
            'reports.create',
            'reports.edit',
            'reports.publish',
            'reports.archive',
            'reports.delete',

            // Financeiro
            'financial.view',
            'financial.manage',

            // Notas fiscais
            'invoices.view',
            'invoices.upload',
            'invoices.download',
            'invoices.delete',

            // Mídias
            'media.view',
            'media.manage',

            // Calendário
            'calendar.view',

            // Permissões
            'permissions.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Super Admin — acesso total
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->syncPermissions(Permission::all());

        // Admin — tudo exceto gerenciar permissões
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(
            Permission::where('name', '!=', 'permissions.manage')->get()
        );

        // Client — apenas visualização dos próprios dados
        $client = Role::firstOrCreate(['name' => 'client']);
        $client->syncPermissions([
            'reports.view',
            'financial.view',
            'invoices.view',
            'invoices.download',
            'media.view',
            'calendar.view',
        ]);
    }
}