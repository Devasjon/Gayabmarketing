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

        // Admin: products, customers, orders, files, reports and marketing (per PRD role table).
        Role::findByName(RoleEnum::Admin->value)->givePermissionTo([
            PermissionEnum::AccessAdmin->value,
            PermissionEnum::ViewProducts->value,
            PermissionEnum::ManageProducts->value,
            PermissionEnum::ManageCategories->value,
            PermissionEnum::ViewOrders->value,
            PermissionEnum::ViewCustomers->value,
            PermissionEnum::ManageCustomerNotes->value,
            PermissionEnum::ManageTasks->value,
            PermissionEnum::ViewAuditLogs->value,
        ]);

        // Content Manager: products, content, media and SEO.
        Role::findByName(RoleEnum::ContentManager->value)->givePermissionTo([
            PermissionEnum::AccessAdmin->value,
            PermissionEnum::ViewProducts->value,
            PermissionEnum::ManageProducts->value,
            PermissionEnum::ManageCategories->value,
            PermissionEnum::ManageTasks->value,
        ]);

        // Finance: payments, transactions, invoices, refunds and financial reports.
        Role::findByName(RoleEnum::Finance->value)->givePermissionTo([
            PermissionEnum::AccessAdmin->value,
            PermissionEnum::ViewOrders->value,
            PermissionEnum::ManageTasks->value,
        ]);

        // Support: customers, orders, tickets and communication; no credential access.
        Role::findByName(RoleEnum::Support->value)->givePermissionTo([
            PermissionEnum::AccessAdmin->value,
            PermissionEnum::ViewOrders->value,
            PermissionEnum::ViewCustomers->value,
            PermissionEnum::ManageCustomerNotes->value,
            PermissionEnum::ManageTasks->value,
        ]);

        // Customer intentionally has no admin permissions.
    }
}
