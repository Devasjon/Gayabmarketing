<?php

namespace Tests\Feature;

use App\Models\Entitlement;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductFile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DownloadTest extends TestCase
{
    use RefreshDatabase;

    private function entitledSetup(): array
    {
        Storage::fake('local');

        $user = User::factory()->create();
        $product = Product::factory()->create();
        $path = UploadedFile::fake()->create('workbook.pdf', 100)->store('products/files/'.$product->id, 'local');

        $file = ProductFile::create([
            'product_id' => $product->id, 'version' => '1.0', 'disk' => 'local',
            'path' => $path, 'original_name' => 'workbook.pdf', 'size_bytes' => 1000,
        ]);

        $order = Order::create(['reference' => 'GBM-1', 'user_id' => $user->id, 'customer_phone' => '0123456789', 'subtotal_cents' => 100, 'total_cents' => 100, 'currency' => 'MYR', 'status' => 'paid']);
        $entitlement = Entitlement::create(['user_id' => $user->id, 'product_id' => $product->id, 'order_id' => $order->id, 'granted_at' => now()]);

        return compact('user', 'product', 'file', 'entitlement');
    }

    public function test_entitled_user_can_download_the_file(): void
    {
        ['user' => $user, 'file' => $file, 'entitlement' => $entitlement] = $this->entitledSetup();

        $this->actingAs($user)
            ->get(route('library.download', [$entitlement, $file]))
            ->assertOk();

        $this->assertDatabaseHas('downloads', ['entitlement_id' => $entitlement->id, 'product_file_id' => $file->id]);
    }

    public function test_a_user_without_entitlement_is_forbidden(): void
    {
        ['file' => $file, 'entitlement' => $entitlement] = $this->entitledSetup();
        $intruder = User::factory()->create();

        $this->actingAs($intruder)
            ->get(route('library.download', [$entitlement, $file]))
            ->assertForbidden();
    }

    public function test_a_file_belonging_to_a_different_product_is_rejected(): void
    {
        ['user' => $user, 'entitlement' => $entitlement] = $this->entitledSetup();

        $otherProduct = Product::factory()->create();
        $unrelatedFile = ProductFile::create([
            'product_id' => $otherProduct->id, 'version' => '1.0', 'disk' => 'local',
            'path' => 'somewhere.pdf', 'original_name' => 'somewhere.pdf', 'size_bytes' => 100,
        ]);

        $this->actingAs($user)
            ->get(route('library.download', [$entitlement, $unrelatedFile]))
            ->assertForbidden();
    }
}
