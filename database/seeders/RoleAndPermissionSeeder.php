<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Permissions для клиентов
        $clientPermissions = [
            'create shop',
            'update shop',
            'delete shop',
            'create product',
            'update product',
            'delete product',
            'view shop statistics',
            'manage orders',
        ];

        // Permissions для менеджеров
        $managerPermissions = [
            'view client shops',
            'manage client support',
            'view support tickets',
            'respond to support tickets',
        ];

        // Permissions для администраторов
        $adminPermissions = [
            'manage plans',
            'manage users',
            'view global statistics',
            'manage system settings',
        ];

        // Создание разрешений
        $allPermissions = collect($clientPermissions)
            ->merge($managerPermissions)
            ->merge($adminPermissions)
            ->unique()
            ->toArray();

        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission, 'guard_name' => 'web']
            );
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Создание ролей
        $clientRole = Role::firstOrCreate(['name' => 'client', 'guard_name' => 'web']);
        $clientRole->syncPermissions($clientPermissions);

        $managerRole = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $managerRole->syncPermissions($managerPermissions);

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions($allPermissions);
    }
}
