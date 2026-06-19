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
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('catalog_translations', function (Blueprint $table) {
            $table->text('summary')->nullable()->comment('Category Summary')->change();
            $table->string('meta_title')->nullable()->comment('Meta Title')->change();
            $table->string('meta_description')->nullable()->comment('Meta Description')->change();
            $table->string('meta_keywords')->nullable()->comment('Meta Keywords')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('catalog_translations', function (Blueprint $table) {
            $table->text('summary')->comment('Category Summary')->change();
            $table->string('meta_title')->comment('Meta Title')->change();
            $table->string('meta_description')->comment('Meta Description')->change();
            $table->string('meta_keywords')->comment('Meta Keywords')->change();
        });
    }
};
