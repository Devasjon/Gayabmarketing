<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void { Schema::create('products',function(Blueprint $table){$table->id();$table->string('name_en');$table->string('name_bm');$table->string('slug')->unique();$table->text('description_en')->nullable();$table->text('description_bm')->nullable();$table->string('category');$table->unsignedInteger('price_cents')->default(0);$table->enum('status',['draft','published','archived'])->default('draft');$table->string('file_path')->nullable();$table->string('cover_path')->nullable();$table->timestamps();}); } public function down(): void { Schema::dropIfExists('products'); } };

