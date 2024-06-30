<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carousels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('page_id')->default(0)->comment('0 -> homepage');
            $table->string('position')->default('top')->comment('top;bottom');
            $table->string('style')->default('container-fluid')->comment('container; container-fluid');
            $table->string('height')->default(600)->comment('the height of container');
            $table->integer('order_index')->default(0);
            $table->boolean('active')->default(true);
            $table->boolean('auto_play')->default(true);
            $table->boolean('with_controls')->default(true)->comment('Add the previous and next controls button');
            $table->boolean('with_indicators')->default(true)->comment('Add the indicators');
            $table->boolean('with_captions')->default(false)->comment('add captions');
            $table->boolean('cross_fade')->default(false)->comment('cross fade');
            $table->boolean('dark_variant')->default(false);
            $table->boolean('touch_swiping')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carousels');
    }
};
