<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_loads(): void
    {
        $this->get(route('home'))->assertOk();
    }

    public function test_product_page_loads_for_published_product(): void
    {
        $product = Product::create([
            'name_en' => 'Test Product', 'name_bm' => 'Produk Ujian', 'slug' => 'test-product',
            'category' => 'Ebook', 'description_en' => 'Desc', 'description_bm' => 'Huraian',
            'price_cents' => 1000, 'status' => 'published',
        ]);

        $this->get(route('products.show', $product))->assertOk();
    }

    public function test_unpublished_product_page_returns_404(): void
    {
        $product = Product::create([
            'name_en' => 'Draft Product', 'name_bm' => 'Draf', 'slug' => 'draft-product',
            'category' => 'Ebook', 'description_en' => 'Desc', 'description_bm' => 'Huraian',
            'price_cents' => 1000, 'status' => 'draft',
        ]);

        $this->get(route('products.show', $product))->assertNotFound();
    }

    public function test_legal_pages_load(): void
    {
        $this->get(route('privacy'))->assertOk();
        $this->get(route('terms'))->assertOk();
        $this->get(route('refund'))->assertOk();
        $this->get(route('license'))->assertOk();
    }

    public function test_checkout_is_disabled_by_default(): void
    {
        $product = Product::create([
            'name_en' => 'Test Product', 'name_bm' => 'Produk Ujian', 'slug' => 'checkout-test',
            'category' => 'Ebook', 'description_en' => 'Desc', 'description_bm' => 'Huraian',
            'price_cents' => 1000, 'status' => 'published',
        ]);

        $this->post(route('checkout.store'), [
            'product_id' => $product->id, 'name' => 'Test', 'email' => 'test@example.com', 'phone' => '0123456789',
        ])->assertStatus(503);
    }

    public function test_health_check_responds(): void
    {
        $this->get('/up')->assertOk();
    }
}
