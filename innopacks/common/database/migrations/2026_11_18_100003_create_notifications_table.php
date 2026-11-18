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
     * notifications table (ported down from the platform package, with the tenant column removed).
     *
     * The system value of channel is used for in-site alerts (new IP / new device login in the panel); sending is consumed by the downstream event bus.
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('admin_user_id')->nullable();
            $table->unsignedBigInteger('worker_id')->nullable();
            $table->string('channel', 32);
            $table->string('event', 64);
            $table->string('title', 255)->default('');
            $table->text('content')->nullable();
            $table->json('payload')->nullable();
            $table->string('status', 32)->default('pending');
            $table->dateTime('sent_at')->nullable();
            $table->dateTime('read_at')->nullable();
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            $table->index(['user_id', 'admin_user_id', 'worker_id', 'status', 'created_at'], 'idx_notifications_receiver');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
