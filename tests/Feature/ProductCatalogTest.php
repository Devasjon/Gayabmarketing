<?php

namespace Tests\Feature;

use App\Livewire\ProductCatalog;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProductCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_shows_all_published_products_by_default(): void
    {
        $ebook = Category::factory()->create(['name' => 'Ebook', 'slug' => 'ebook']);

        $alpha = Product::factory()->create(['category_id' => $ebook->id]);
        $alpha->translations()->updateOrCreate(['locale' => 'en'], ['name' => 'Alpha Guidebook', 'description' => 'd']);

        $beta = Product::factory()->create(['category_id' => $ebook->id]);
        $beta->translations()->updateOrCreate(['locale' => 'en'], ['name' => 'Beta Workbook', 'description' => 'd']);

        $draft = Product::factory()->draft()->create(['category_id' => $ebook->id]);
        $draft->translations()->updateOrCreate(['locale' => 'en'], ['name' => 'Zeta Draft Template', 'description' => 'd']);

        Livewire::test(ProductCatalog::class)
            ->assertSee('Alpha Guidebook')
            ->assertSee('Beta Workbook')
            ->assertDontSee('Zeta Draft Template');
    }

    public function test_it_filters_by_category(): void
    {
        $ebook = Category::factory()->create(['name' => 'Ebook', 'slug' => 'ebook']);
        $workbook = Category::factory()->create(['name' => 'Workbook', 'slug' => 'workbook']);

        $ebookProduct = Product::factory()->create(['category_id' => $ebook->id]);
        $ebookProduct->translations()->updateOrCreate(['locale' => 'en'], ['name' => 'Ebook Item', 'description' => 'd']);

        $workbookProduct = Product::factory()->create(['category_id' => $workbook->id]);
        $workbookProduct->translations()->updateOrCreate(['locale' => 'en'], ['name' => 'Workbook Item', 'description' => 'd']);

        Livewire::test(ProductCatalog::class)
            ->call('setCategory', 'ebook')
            ->assertSee('Ebook Item')
            ->assertDontSee('Workbook Item');
    }
}
