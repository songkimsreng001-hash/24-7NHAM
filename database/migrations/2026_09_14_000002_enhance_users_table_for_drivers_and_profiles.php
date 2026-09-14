<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->unique()->after('email');
            }
            if (! Schema::hasColumn('users', 'status')) {
                $table->string('status')->default('active')->after('role'); // active, suspended, pending_approval
            }
            if (! Schema::hasColumn('users', 'vehicle_type')) {
                $table->string('vehicle_type')->nullable()->after('status'); // motorbike, bicycle, car
            }
            if (! Schema::hasColumn('users', 'license_plate')) {
                $table->string('license_plate')->nullable()->after('vehicle_type');
            }
            if (! Schema::hasColumn('users', 'driver_verified_at')) {
                $table->timestamp('driver_verified_at')->nullable()->after('license_plate');
            }
            if (! Schema::hasColumn('users', 'is_online')) {
                $table->boolean('is_online')->default(true)->after('driver_verified_at');
            }
            if (! Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('is_online');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $cols = ['phone', 'status', 'vehicle_type', 'license_plate', 'driver_verified_at', 'is_online', 'avatar'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
