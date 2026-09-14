<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('personal_access_tokens')) {
            Schema::create('personal_access_tokens', function (Blueprint $table) {
                $table->id();
                $table->morphs('tokenable');
                $table->text('name');
                $table->string('token', 64)->unique();
                $table->text('abilities')->nullable();
                $table->timestamp('last_used_at')->nullable();
                $table->timestamp('expires_at')->nullable()->index();
                $table->timestamps();
            });

            return;
        }

        if (! Schema::hasColumn('personal_access_tokens', 'tokenable_type')) {
            Schema::table('personal_access_tokens', function (Blueprint $table) {
                $table->morphs('tokenable');
            });
        }

        if (! Schema::hasColumn('personal_access_tokens', 'token')) {
            Schema::table('personal_access_tokens', function (Blueprint $table) {
                $table->string('token', 64)->unique();
            });
        }

        if (! Schema::hasColumn('personal_access_tokens', 'abilities')) {
            Schema::table('personal_access_tokens', function (Blueprint $table) {
                $table->text('abilities')->nullable();
            });
        }

        if (! Schema::hasColumn('personal_access_tokens', 'last_used_at')) {
            Schema::table('personal_access_tokens', function (Blueprint $table) {
                $table->timestamp('last_used_at')->nullable();
            });
        }

        if (! Schema::hasColumn('personal_access_tokens', 'expires_at')) {
            Schema::table('personal_access_tokens', function (Blueprint $table) {
                $table->timestamp('expires_at')->nullable()->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_access_tokens');
    }
};
