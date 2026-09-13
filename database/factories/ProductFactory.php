<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->sentence(3);

        return [
            'category_id' => Category::factory(),
            'slug' => Str::slug($name).'-'.$this->faker->unique()->numberBetween(1, 100000),
            'price_cents' => $this->faker->numberBetween(1000, 9900),
            'status' => 'published',
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Product $product) {
            if ($product->translations()->count() === 0) {
                $name = $this->faker->sentence(3);
                $product->translations()->create(['locale' => 'en', 'name' => $name, 'description' => $this->faker->sentence(12)]);
                $product->translations()->create(['locale' => 'ms', 'name' => $name, 'description' => $this->faker->sentence(12)]);
            }
        });
    }

    public function draft(): static
    {
        return $this->state(['status' => 'draft']);
    }
}
