<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('in_app_notifications')) {
            Schema::create('in_app_notifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
                $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
                $table->string('type')->default('info'); // order_status, driver_en_route, driver_nearby, driver_arrived, broadcast, alert
                $table->string('title');
                $table->text('message');
                $table->string('icon')->default('bi-bell');
                $table->string('target_role')->default('customer'); // customer, driver, admin, all
                $table->boolean('is_read')->default(false);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('in_app_notifications');
    }
};
