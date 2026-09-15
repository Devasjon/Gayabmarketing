<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });

        foreach (DB::table('products')->distinct()->pluck('category') as $categoryName) {
            if (! $categoryName) {
                continue;
            }

            $categoryId = DB::table('categories')->where('slug', Str::slug($categoryName))->value('id');

            if (! $categoryId) {
                $categoryId = DB::table('categories')->insertGetId([
                    'name' => $categoryName,
                    'slug' => Str::slug($categoryName),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('products')->where('category', $categoryName)->update(['category_id' => $categoryId]);
        }

        foreach (DB::table('products')->get() as $product) {
            DB::table('product_translations')->insert([
                'product_id' => $product->id,
                'locale' => 'en',
                'name' => $product->name_en,
                'description' => $product->description_en,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('product_translations')->insert([
                'product_id' => $product->id,
                'locale' => 'ms',
                'name' => $product->name_bm ?: $product->name_en,
                'description' => $product->description_bm ?: $product->description_en,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['category', 'name_en', 'name_bm', 'description_en', 'description_bm']);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('category')->nullable();
            $table->string('name_en')->nullable();
            $table->string('name_bm')->nullable();
            $table->text('description_en')->nullable();
            $table->text('description_bm')->nullable();
        });

        foreach (DB::table('products')->get() as $product) {
            $category = DB::table('categories')->where('id', $product->category_id)->value('name');
            $en = DB::table('product_translations')->where('product_id', $product->id)->where('locale', 'en')->first();
            $ms = DB::table('product_translations')->where('product_id', $product->id)->where('locale', 'ms')->first();

            DB::table('products')->where('id', $product->id)->update([
                'category' => $category,
                'name_en' => $en->name ?? null,
                'description_en' => $en->description ?? null,
                'name_bm' => $ms->name ?? null,
                'description_bm' => $ms->description ?? null,
            ]);
        }

        Schema::table('products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('category_id');
        });
    }
};
