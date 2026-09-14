<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('order_reviews')) {
            Schema::create('order_reviews', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('driver_id')->nullable()->constrained('users')->nullOnDelete();
                $table->tinyInteger('food_rating')->default(5); // 1-5
                $table->tinyInteger('driver_rating')->default(5); // 1-5
                $table->text('comment')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('promo_codes')) {
            Schema::create('promo_codes', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->string('discount_type')->default('percent'); // percent, fixed
                $table->decimal('discount_value', 8, 2);
                $table->decimal('min_order', 8, 2)->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
            });

            // Seed initial promotional vouchers
            DB::table('promo_codes')->insert([
                [
                    'code' => 'WELCOME10',
                    'discount_type' => 'percent',
                    'discount_value' => 10.00,
                    'min_order' => 5.00,
                    'is_active' => true,
                    'expires_at' => now()->addMonths(6),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'code' => 'NHAM2',
                    'discount_type' => 'fixed',
                    'discount_value' => 2.00,
                    'min_order' => 10.00,
                    'is_active' => true,
                    'expires_at' => now()->addMonths(6),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        if (! Schema::hasTable('favorites')) {
            Schema::create('favorites', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['user_id', 'product_id']);
            });
        }

        if (! Schema::hasTable('disputes')) {
            Schema::create('disputes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('subject');
                $table->text('description');
                $table->string('status')->default('open'); // open, investigating, resolved
                $table->text('resolution_notes')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('audit_logs')) {
            Schema::create('audit_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('action');
                $table->string('target_type')->nullable();
                $table->unsignedBigInteger('target_id')->nullable();
                $table->text('details')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('disputes');
        Schema::dropIfExists('favorites');
        Schema::dropIfExists('promo_codes');
        Schema::dropIfExists('order_reviews');
    }
};
