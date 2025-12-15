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

        // Очистка таблиц (удаление существующих данных)
        \DB::table('model_has_permissions')->delete();
        \DB::table('model_has_roles')->delete();
        \DB::table('role_has_permissions')->delete();
        Permission::query()->delete();
        Role::query()->delete();

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
        $allPermissions = collect($clientPermissions)
            ->merge($managerPermissions)
            ->merge($adminPermissions)
            ->unique()
            ->toArray();

        foreach ($allPermissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }

        // Очистка кеша после создания разрешений
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Создание ролей
        $clientRole = Role::create(['name' => 'client']);
        foreach ($clientPermissions as $permission) {
            $clientRole->givePermissionTo($permission);
        }

        $managerRole = Role::create(['name' => 'manager']);
        foreach ($managerPermissions as $permission) {
            $managerRole->givePermissionTo($permission);
        }

        $adminRole = Role::create(['name' => 'admin']);
        foreach ($allPermissions as $permission) {
            $adminRole->givePermissionTo($permission);
        }
    }
}