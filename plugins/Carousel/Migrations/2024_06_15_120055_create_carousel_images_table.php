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
        Schema::create('carousel_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('carousel_id');
            $table->string('title')->default('null');
            $table->string('description')->default('');
            $table->string('image_url');
            $table->string('target_url')->nullable();
            $table->string('position')->default(0);
            $table->string('active')->default(true);
            $table->integer('item_interval')->default(5000);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carousel_images');
    }
};
