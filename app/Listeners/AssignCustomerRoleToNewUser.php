<?php

namespace App\Listeners;

use App\Enums\Role;
use Illuminate\Auth\Events\Registered;
use Spatie\Permission\Models\Role as RoleModel;

class AssignCustomerRoleToNewUser
{
    public function handle(Registered $event): void
    {
        $event->user->assignRole(RoleModel::findOrCreate(Role::Customer->value));
    }
}
