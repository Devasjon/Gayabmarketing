<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Entitlements record what a customer paid for and is used to reconcile
     * order/payment history — self-service account deletion must not destroy
     * that record, so user_id nulls out instead of cascading like the other
     * FKs on this table.
     */
    public function up(): void
    {
        Schema::table('entitlements', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('entitlements', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();
        });

        Schema::table('entitlements', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('entitlements', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('entitlements', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable(false)->change();
        });

        Schema::table('entitlements', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
