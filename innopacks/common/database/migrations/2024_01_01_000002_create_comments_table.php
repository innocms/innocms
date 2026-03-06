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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->morphs('commentable');
            $table->string('author_name');
            $table->string('author_email');
            $table->text('content');
            $table->unsignedTinyInteger('rating')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('status')->default('pending');
            $table->ipAddress('ip_address');
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('comments')->onDelete('cascade');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
