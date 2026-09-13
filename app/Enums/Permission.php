<?php

namespace App\Enums;

enum Permission: string
{
    case AccessAdmin = 'admin.access';
    case ViewProducts = 'products.view';
    case ManageProducts = 'products.manage';
    case ManageCategories = 'categories.manage';
}
