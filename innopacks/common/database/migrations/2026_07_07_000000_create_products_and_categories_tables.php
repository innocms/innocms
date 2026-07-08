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
    public function up(): void
    {
        // 产品分类（树形 parent_id）
        Schema::create('categories', function (Blueprint $table) {
            $table->comment('Product Category');
            $table->bigIncrements('id')->comment('ID');
            $table->unsignedBigInteger('parent_id')->default(0)->index('parent_id')->comment('Parent Category ID');
            $table->string('slug', 128)->nullable()->unique()->comment('URL Slug');
            $table->string('image')->nullable()->comment('Category Image');
            $table->integer('position')->default(0)->comment('Sort order');
            $table->boolean('active')->default(true)->comment('Active');
            $table->timestamps();
        });

        // 产品分类多语言
        Schema::create('category_translations', function (Blueprint $table) {
            $table->comment('Product Category Translation');
            $table->bigIncrements('id')->comment('ID');
            $table->unsignedBigInteger('category_id')->index('category_id')->comment('Category ID');
            $table->string('locale')->comment('Locale Code');
            $table->string('name')->comment('Name');
            $table->text('summary')->nullable()->comment('Category Summary');
            $table->longText('content')->nullable()->comment('Content');
            $table->string('meta_title', 500)->nullable()->comment('Meta Title');
            $table->string('meta_description', 1000)->nullable()->comment('Meta Description');
            $table->string('meta_keywords', 500)->nullable()->comment('Meta Keywords');
            $table->unique(['category_id', 'locale'], 'category_translations_unique');
            $table->timestamps();
        });

        // 产品主表（纯展示型：价格回主表，无 SKU/库存/规格）
        Schema::create('products', function (Blueprint $table) {
            $table->comment('Product');
            $table->bigIncrements('id')->comment('ID');
            $table->string('slug', 128)->nullable()->unique()->comment('URL Slug');
            $table->json('images')->nullable()->comment('Product Images, images[0] is cover');
            $table->json('video')->nullable()->comment('Product Video');
            $table->decimal('price', 15, 4)->default(0)->comment('Display Price (reference only)');
            $table->string('link', 500)->nullable()->comment('External purchase link (mall URL)');
            $table->string('spu_code', 128)->nullable()->comment('Product Model Code');
            $table->integer('position')->default(0)->comment('Sort order');
            $table->integer('viewed')->default(0)->comment('Viewed');
            $table->boolean('active')->default(true)->comment('Active');
            $table->softDeletes()->comment('Deleted At');
            $table->timestamps();
        });

        // 产品多语言
        Schema::create('product_translations', function (Blueprint $table) {
            $table->comment('Product Translation');
            $table->bigIncrements('id')->comment('ID');
            $table->unsignedBigInteger('product_id')->index('product_id')->comment('Product ID');
            $table->string('locale')->comment('Locale Code');
            $table->string('name')->comment('Name');
            $table->text('summary')->nullable()->comment('Summary');
            $table->longText('content')->nullable()->comment('Content');
            $table->text('selling_point')->nullable()->comment('Selling Point');
            $table->string('meta_title', 500)->nullable()->comment('Meta Title');
            $table->string('meta_description', 1000)->nullable()->comment('Meta Description');
            $table->string('meta_keywords', 500)->nullable()->comment('Meta Keywords');
            $table->unique(['product_id', 'locale'], 'product_translations_unique');
            $table->timestamps();
        });

        // 产品 ↔ 分类 pivot
        Schema::create('product_categories', function (Blueprint $table) {
            $table->comment('Product Category Pivot');
            $table->unsignedBigInteger('product_id')->index('pc_product_id')->comment('Product ID');
            $table->unsignedBigInteger('category_id')->index('pc_category_id')->comment('Category ID');
            $table->primary(['product_id', 'category_id']);
        });

        // 相关产品 pivot
        Schema::create('product_relations', function (Blueprint $table) {
            $table->comment('Product Relation Pivot');
            $table->unsignedBigInteger('product_id')->index('pr_product_id')->comment('Product ID');
            $table->unsignedBigInteger('relation_id')->index('pr_relation_id')->comment('Related Product ID');
            $table->nullableTimestamps();
            $table->primary(['product_id', 'relation_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_relations');
        Schema::dropIfExists('product_categories');
        Schema::dropIfExists('product_translations');
        Schema::dropIfExists('products');
        Schema::dropIfExists('category_translations');
        Schema::dropIfExists('categories');
    }
};
