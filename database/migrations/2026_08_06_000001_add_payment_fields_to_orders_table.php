<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * FIX: The original orders table was missing:
 *  - status column  (StoreController and OrderController reference it but it didn't exist)
 *  - payment_intent_id  (needed to link Stripe payment back to the order)
 *
 * Run: php artisan migrate
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Status: 0 = rejected, 1 = approved, 2 = paid/pending fulfillment
            if (! Schema::hasColumn('orders', 'status')) {
                $table->tinyInteger('status')->default(2)->after('amount');
            }

            if (! Schema::hasColumn('orders', 'payment_intent_id')) {
                $table->string('payment_intent_id')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['status', 'payment_intent_id']);
        });
    }
};
