<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'delivery_latitude')) {
                $table->decimal('delivery_latitude', 10, 7)->nullable()->after('delivery_address');
            }
            if (! Schema::hasColumn('orders', 'delivery_longitude')) {
                $table->decimal('delivery_longitude', 10, 7)->nullable()->after('delivery_latitude');
            }
            if (! Schema::hasColumn('orders', 'distance_km')) {
                $table->decimal('distance_km', 6, 2)->nullable()->after('delivery_longitude');
            }
            if (! Schema::hasColumn('orders', 'eta_minutes')) {
                $table->integer('eta_minutes')->nullable()->after('distance_km');
            }
            if (! Schema::hasColumn('orders', 'payment_method')) {
                $table->string('payment_method')->default('card')->after('payment_intent_id'); // card, cod
            }
            if (! Schema::hasColumn('orders', 'discount_amount')) {
                $table->decimal('discount_amount', 8, 2)->default(0)->after('amount');
            }
            if (! Schema::hasColumn('orders', 'promo_code')) {
                $table->string('promo_code')->nullable()->after('discount_amount');
            }
            if (! Schema::hasColumn('orders', 'cancellation_reason')) {
                $table->text('cancellation_reason')->nullable()->after('delivery_notes');
            }
            if (! Schema::hasColumn('orders', 'driver_accepted_at')) {
                $table->timestamp('driver_accepted_at')->nullable()->after('delivered_at');
            }
            if (! Schema::hasColumn('orders', 'picked_up_at')) {
                $table->timestamp('picked_up_at')->nullable()->after('driver_accepted_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $cols = [
                'delivery_latitude',
                'delivery_longitude',
                'distance_km',
                'eta_minutes',
                'payment_method',
                'discount_amount',
                'promo_code',
                'cancellation_reason',
                'driver_accepted_at',
                'picked_up_at',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('orders', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
