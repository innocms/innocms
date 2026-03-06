<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

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
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->string('session_id', 64)->unique();
            $table->string('ip_address', 45);
            $table->text('user_agent')->nullable();
            $table->string('country_code', 2)->nullable();
            $table->string('country_name', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('referrer', 1000)->nullable();
            $table->string('device_type', 20)->nullable();
            $table->string('browser', 50)->nullable();
            $table->string('os', 50)->nullable();
            $table->string('locale', 10)->nullable();
            $table->timestamp('first_visited_at');
            $table->timestamp('last_visited_at');
            $table->timestamps();

            $table->index('session_id');
            $table->index('ip_address');
            $table->index('first_visited_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};
