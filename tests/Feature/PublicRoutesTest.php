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

    public function test_home_page_links_to_registration_for_guests(): void
    {
        $this->get(route('home'))->assertSee(route('register'), false);
    }

    public function test_product_page_loads_for_published_product(): void
    {
        $product = Product::factory()->create(['slug' => 'test-product']);

        $this->get(route('products.show', $product))->assertOk();
    }

    public function test_unpublished_product_page_returns_404(): void
    {
        $product = Product::factory()->draft()->create(['slug' => 'draft-product']);

        $this->get(route('products.show', $product))->assertNotFound();
    }

    public function test_legal_pages_load(): void
    {
        $this->get(route('privacy'))->assertOk();
        $this->get(route('terms'))->assertOk();
        $this->get(route('refund'))->assertOk();
        $this->get(route('license'))->assertOk();
    }

    public function test_health_check_responds(): void
    {
        $this->get('/up')->assertOk();
    }
}
