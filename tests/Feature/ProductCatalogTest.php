<?php

namespace Tests\Feature;

use App\Livewire\ProductCatalog;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProductCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_shows_all_published_products_by_default(): void
    {
        Product::create(['name_en' => 'Alpha Guidebook', 'name_bm' => 'Alpha Guidebook', 'slug' => 'alpha-guidebook', 'category' => 'Ebook', 'description_en' => 'd', 'description_bm' => 'd', 'price_cents' => 100, 'status' => 'published']);
        Product::create(['name_en' => 'Beta Workbook', 'name_bm' => 'Beta Workbook', 'slug' => 'beta-workbook', 'category' => 'Workbook', 'description_en' => 'd', 'description_bm' => 'd', 'price_cents' => 100, 'status' => 'published']);
        Product::create(['name_en' => 'Zeta Draft Template', 'name_bm' => 'Zeta Draft Template', 'slug' => 'zeta-draft-template', 'category' => 'Ebook', 'description_en' => 'd', 'description_bm' => 'd', 'price_cents' => 100, 'status' => 'draft']);

        Livewire::test(ProductCatalog::class)
            ->assertSee('Alpha Guidebook')
            ->assertSee('Beta Workbook')
            ->assertDontSee('Zeta Draft Template');
    }

    public function test_it_filters_by_category(): void
    {
        Product::create(['name_en' => 'Ebook Item', 'name_bm' => 'x', 'slug' => 'ebook-item', 'category' => 'Ebook', 'description_en' => 'd', 'description_bm' => 'd', 'price_cents' => 100, 'status' => 'published']);
        Product::create(['name_en' => 'Workbook Item', 'name_bm' => 'x', 'slug' => 'workbook-item', 'category' => 'Workbook', 'description_en' => 'd', 'description_bm' => 'd', 'price_cents' => 100, 'status' => 'published']);

        Livewire::test(ProductCatalog::class)
            ->call('setCategory', 'Ebook')
            ->assertSee('Ebook Item')
            ->assertDontSee('Workbook Item');
    }
}
