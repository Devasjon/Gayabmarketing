<?php

return [
    'nav' => [
        'dashboard' => 'Dashboard',
        'products' => 'Products',
        'categories' => 'Categories',
        'orders' => 'Orders',
    ],

    'orders' => [
        'title' => 'Orders',
        'items' => 'items',
        'empty' => 'No orders yet.',
    ],

    'dashboard' => [
        'title' => 'Admin Dashboard',
        'welcome' => 'Welcome back, :name.',
        'total_products' => 'Total products',
        'published_products' => 'Published',
        'total_categories' => 'Categories',
    ],

    'products' => [
        'title' => 'Products',
        'new' => 'New product',
        'edit' => 'Edit product',
        'name_en' => 'Name (English)',
        'name_ms' => 'Name (Bahasa Melayu)',
        'description_en' => 'Description (English)',
        'description_ms' => 'Description (Bahasa Melayu)',
        'category' => 'Category',
        'price' => 'Price (RM)',
        'status' => 'Status',
        'slug' => 'Slug',
        'cover' => 'Cover image',
        'save' => 'Save',
        'cancel' => 'Cancel',
        'delete' => 'Delete',
        'delete_confirm' => 'Delete this product? This cannot be undone.',
        'empty' => 'No products yet.',
        'saved' => 'Product saved.',
        'deleted' => 'Product deleted.',
        'files' => 'Downloadable files',
        'file_version' => 'Version label',
        'upload_file' => 'Upload',
        'no_files' => 'No files uploaded yet.',
        'delete_file_confirm' => 'Delete this file version?',
    ],

    'categories' => [
        'title' => 'Categories',
        'new' => 'New category',
        'name' => 'Name',
        'save' => 'Save',
        'delete' => 'Delete',
        'delete_confirm' => 'Delete this category? Products using it will need to be reassigned first.',
        'delete_blocked' => 'This category still has products assigned to it.',
        'empty' => 'No categories yet.',
        'saved' => 'Category saved.',
        'deleted' => 'Category deleted.',
    ],
];
