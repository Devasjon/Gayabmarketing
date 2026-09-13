<?php

namespace App\Enums;

enum Role: string
{
    case SuperAdmin = 'Super Admin';
    case Admin = 'Admin';
    case Finance = 'Finance';
    case ContentManager = 'Content Manager';
    case Support = 'Support';
    case Customer = 'Customer';

    public static function staffRoles(): array
    {
        return [self::SuperAdmin, self::Admin, self::Finance, self::ContentManager, self::Support];
    }
}
