<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'view-inventory',
            'create-purchase',
            'view-purchase',
            'update-purchase',
            'delete-purchase',
            'create-sale',
            'view-sale',
            'update-sale',
            'delete-sale',
            'view-report',
            'download-report',
            'set-opening-stock',
            'manage-products',
            'manage-locations',
            'manage-users',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions

        // Admin - Full Access
        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());

        // Manager - Can manage inventory and view reports
        $manager = Role::create(['name' => 'manager']);
        $manager->givePermissionTo([
            'view-inventory',
            'create-purchase',
            'view-purchase',
            'update-purchase',
            'create-sale',
            'view-sale',
            'update-sale',
            'view-report',
            'download-report',
            'manage-products',
        ]);

        // Shopman - Can only create sales and view inventory
        $shopman = Role::create(['name' => 'shopman']);
        $shopman->givePermissionTo([
            'view-inventory',
            'create-sale',
            'view-sale',
        ]);

        // Create demo users
        $adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);
        $adminUser->assignRole('admin');

        $managerUser = User::create([
            'name' => 'Manager User',
            'email' => 'manager@example.com',
            'password' => bcrypt('password'),
        ]);
        $managerUser->assignRole('manager');

        $shopmanUser = User::create([
            'name' => 'Shopman User',
            'email' => 'shopman@example.com',
            'password' => bcrypt('password'),
        ]);
        $shopmanUser->assignRole('shopman');
    }
}
