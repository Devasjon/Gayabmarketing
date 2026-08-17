<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void { Schema::create('orders',function(Blueprint $table){$table->id();$table->string('reference')->unique();$table->foreignId('product_id')->constrained()->restrictOnDelete();$table->string('customer_name');$table->string('customer_email')->index();$table->string('customer_phone',30);$table->unsignedInteger('amount_cents');$table->enum('status',['pending','paid','failed','refunded'])->default('pending')->index();$table->string('billplz_bill_id')->nullable()->unique();$table->timestamp('paid_at')->nullable();$table->string('download_token',64)->unique();$table->timestamps();}); } public function down(): void { Schema::dropIfExists('orders'); } };

