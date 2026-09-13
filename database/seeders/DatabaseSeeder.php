<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);

        $categories = collect(['Ebook', 'Workbook', 'Template'])
            ->mapWithKeys(fn ($name) => [$name => Category::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name])->id]);

        $items = [
            ['slug' => 'panduan-homestay', 'category' => 'Ebook', 'price_cents' => 3900, 'name_en' => 'Essential Guide to Starting & Managing a Homestay', 'name_bm' => 'Panduan Asas Memulakan & Mengendalikan Homestay', 'description_en' => 'A practical guide for Malaysian homestay owners—from planning to daily operations.', 'description_bm' => 'Panduan praktikal untuk pemilik homestay Malaysia—daripada perancangan hingga operasi.'],
            ['slug' => 'worldbuilding-workbook', 'category' => 'Workbook', 'price_cents' => 4900, 'name_en' => 'Ultimate Fantasy & Romantasy Worldbuilding Workbook', 'name_bm' => 'Ultimate Fantasy & Romantasy Worldbuilding Workbook', 'description_en' => 'Build living worlds, robust magic systems and unforgettable characters.', 'description_bm' => 'Bina dunia yang hidup, sistem magis yang kukuh dan watak yang sukar dilupakan.'],
            ['slug' => 'komisen-lokum', 'category' => 'Template', 'price_cents' => 5900, 'name_en' => 'Dental Locum Commission Calculator', 'name_bm' => 'Sistem Pengiraan Komisen Lokum Klinik Gigi', 'description_en' => 'A smart spreadsheet for treatment records, daily revenue and monthly commissions.', 'description_bm' => 'Template spreadsheet pintar untuk rekod rawatan, hasil harian dan komisen bulanan.'],
            ['slug' => 'kishotenketsu-workbook', 'category' => 'Workbook', 'price_cents' => 3500, 'name_en' => 'Kishotenketsu Story Workbook', 'name_bm' => 'Kishotenketsu Story Workbook', 'description_en' => 'Plan a four-part story with a gentle, creative and guided approach.', 'description_bm' => 'Rangka cerita empat bahagian dengan pendekatan yang lembut, kreatif dan terarah.'],
        ];

        foreach ($items as $item) {
            $product = Product::updateOrCreate(
                ['slug' => $item['slug']],
                ['category_id' => $categories[$item['category']], 'price_cents' => $item['price_cents'], 'status' => 'published']
            );

            $product->translations()->updateOrCreate(['locale' => 'en'], ['name' => $item['name_en'], 'description' => $item['description_en']]);
            $product->translations()->updateOrCreate(['locale' => 'ms'], ['name' => $item['name_bm'], 'description' => $item['description_bm']]);
        }
    }
}
