<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->unsignedInteger('subtotal_cents')->default(0)->after('customer_phone');
            $table->unsignedInteger('total_cents')->default(0)->after('subtotal_cents');
            $table->string('currency', 3)->default('MYR')->after('total_cents');
        });

        foreach (DB::table('orders')->get() as $order) {
            DB::table('order_items')->insert([
                'order_id' => $order->id,
                'product_id' => $order->product_id,
                'product_name' => DB::table('product_translations')->where('product_id', $order->product_id)->where('locale', 'en')->value('name') ?? 'Product',
                'unit_price_cents' => $order->amount_cents,
                'quantity' => 1,
                'line_total_cents' => $order->amount_cents,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('orders')->where('id', $order->id)->update([
                'subtotal_cents' => $order->amount_cents,
                'total_cents' => $order->amount_cents,
            ]);
        }

        $statuses = DB::table('orders')->pluck('status', 'id');

        Schema::table('orders', function (Blueprint $table) {
            $table->dropUnique(['download_token']);
            $table->dropIndex(['status']);
            $table->dropIndex(['customer_email']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('product_id');
            $table->dropColumn(['customer_name', 'customer_email', 'amount_cents', 'download_token', 'status']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('total_cents');
        });

        foreach ($statuses as $id => $status) {
            DB::table('orders')->where('id', $id)->update(['status' => $status]);
        }
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->unsignedInteger('amount_cents')->default(0);
            $table->string('download_token', 64)->nullable();
        });

        foreach (DB::table('orders')->get() as $order) {
            $firstItem = DB::table('order_items')->where('order_id', $order->id)->first();
            $email = DB::table('users')->where('id', $order->user_id)->value('email');

            DB::table('orders')->where('id', $order->id)->update([
                'product_id' => $firstItem->product_id ?? null,
                'customer_name' => 'Unknown',
                'customer_email' => $email ?? 'unknown@example.com',
                'amount_cents' => $order->total_cents,
                'download_token' => bin2hex(random_bytes(32)),
            ]);
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
            $table->dropColumn(['subtotal_cents', 'total_cents', 'currency']);
        });
    }
};
