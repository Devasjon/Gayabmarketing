<?php

namespace Database\Seeders;

use App\Enums\Permission as PermissionEnum;
use App\Enums\Role as RoleEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        foreach (PermissionEnum::cases() as $permission) {
            Permission::findOrCreate($permission->value);
        }

        foreach (RoleEnum::cases() as $role) {
            Role::findOrCreate($role->value);
        }

        Role::findByName(RoleEnum::SuperAdmin->value)->givePermissionTo(Permission::all());

        Role::findByName(RoleEnum::Admin->value)->givePermissionTo([
            PermissionEnum::AccessAdmin->value,
            PermissionEnum::ViewProducts->value,
            PermissionEnum::ManageProducts->value,
            PermissionEnum::ManageCategories->value,
        ]);

        Role::findByName(RoleEnum::ContentManager->value)->givePermissionTo([
            PermissionEnum::AccessAdmin->value,
            PermissionEnum::ViewProducts->value,
            PermissionEnum::ManageProducts->value,
            PermissionEnum::ManageCategories->value,
        ]);

        Role::findByName(RoleEnum::Finance->value)->givePermissionTo([
            PermissionEnum::AccessAdmin->value,
        ]);

        Role::findByName(RoleEnum::Support->value)->givePermissionTo([
            PermissionEnum::AccessAdmin->value,
        ]);

        // Customer intentionally has no admin permissions.
    }
}
