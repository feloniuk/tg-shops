<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Очистка существующих ролей и разрешений
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
            'manage orders'
        ];

        // Permissions для менеджеров
        $managerPermissions = [
            'view client shops',
            'manage client support',
            'view support tickets',
            'respond to support tickets'
        ];

        // Permissions для администраторов
        $adminPermissions = [
            'manage plans',
            'manage users',
            'view global statistics',
            'manage system settings'
        ];

        // Создание разрешений
        collect($clientPermissions)
            ->merge($managerPermissions)
            ->merge($adminPermissions)
            ->each(fn($permission) => Permission::create(['name' => $permission]));

        // Создание ролей
        $clientRole = Role::create(['name' => 'client']);
        $clientRole->syncPermissions($clientPermissions);

        $managerRole = Role::create(['name' => 'manager']);
        $managerRole->syncPermissions($managerPermissions);

        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->syncPermissions(
            array_merge($clientPermissions, $managerPermissions, $adminPermissions)
        );
    }
}