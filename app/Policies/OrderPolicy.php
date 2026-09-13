<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\User;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permission::ViewOrders->value);
    }
}
