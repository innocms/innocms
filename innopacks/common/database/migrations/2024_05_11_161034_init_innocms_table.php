<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 *
 * https://github.com/kitloong/laravel-migrations-generator
 * php artisan migrate:generate --squash
 * php artisan migrate:generate --tables="table1,table2"
 * php artisan migrate:generate --ignore="table3,table4,table5"
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
    public function up()
    {
        Schema::create('admin_tokens', function (Blueprint $table) {
            $table->comment('Admin User Token');
            $table->bigIncrements('id');
            $table->integer('admin_id')->index('at_admin_id')->comment('Admin User ID');
            $table->string('token', 64)->comment('API token');
            $table->timestamps();
        });

        Schema::create('admins', function (Blueprint $table) {
            $table->comment('Admin User');
            $table->bigIncrements('id')->comment('ID');
            $table->string('name')->comment('Name');
            $table->string('email', 64)->unique()->comment('Email');
            $table->string('password')->comment('Password');
            $table->string('locale')->default('')->comment('Locale Code');
            $table->boolean('active')->comment('Active');
            $table->timestamps();
        });

        Schema::create('article_tags', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('article_id')->comment('Article ID');
            $table->integer('tag_id')->comment('Tag ID');
            $table->timestamps();
        });

        Schema::create('article_translations', function (Blueprint $table) {
            $table->comment('Article Translations');
            $table->bigIncrements('id')->comment('ID');
            $table->integer('article_id')->index('at_article_id')->comment('Article ID');
            $table->string('locale')->comment('Locale Code');
            $table->string('title')->comment('Title');
            $table->string('summary')->nullable()->comment('Summary');
            $table->string('image')->nullable()->comment('Article Image');
            $table->text('content')->nullable()->comment('Content');
            $table->string('meta_title')->nullable()->comment('Meta Title');
            $table->string('meta_description')->nullable()->comment('Meta description');
            $table->string('meta_keywords')->nullable()->comment('Meta keywords');
            $table->timestamps();
        });

        Schema::create('articles', function (Blueprint $table) {
            $table->comment('Article');
            $table->bigIncrements('id')->comment('ID');
            $table->integer('catalog_id')->nullable()->default(0)->index('a_catalog_id')->comment('Catalog ID');
            $table->string('slug', 128)->nullable()->unique()->comment('URL Slug');
            $table->integer('position')->default(0)->comment('Sort order');
            $table->integer('viewed')->default(0)->comment('Viewed');
            $table->string('author')->nullable()->comment('Author');
            $table->boolean('active')->comment('Active');
            $table->timestamps();
        });

        Schema::create('cache', function (Blueprint $table) {
            $table->string('key', 128)->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key', 128)->primary();
            $table->string('owner');
            $table->integer('expiration');
        });

        Schema::create('catalog_translations', function (Blueprint $table) {
            $table->comment('Article Category Translation');
            $table->bigIncrements('id')->comment('ID');
            $table->integer('catalog_id')->index('ct_catalog_id')->comment('Category ID');
            $table->string('locale')->comment('Locale Code');
            $table->string('title')->comment('Title');
            $table->text('summary')->comment('Category Summary');
            $table->string('meta_title')->comment('Meta Title');
            $table->string('meta_description')->comment('Meta Translation');
            $table->string('meta_keywords')->comment('Meta Keywords');
            $table->timestamps();
        });

        Schema::create('catalogs', function (Blueprint $table) {
            $table->comment('Article Category');
            $table->bigIncrements('id')->comment('ID');
            $table->integer('parent_id')->default(0)->index('c_parent_id')->comment('Parent ID');
            $table->string('slug', 128)->nullable()->unique()->comment('URL Slug');
            $table->integer('position')->default(0)->comment('Sort order');
            $table->boolean('active')->comment('Active');
            $table->timestamps();
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('uuid', 128)->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id', 128)->primary();
            $table->string('name');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->longText('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });

        Schema::create('jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('queue', 128)->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('locales', function (Blueprint $table) {
            $table->comment('Locales');
            $table->bigIncrements('id')->comment('ID');
            $table->string('name', 64)->comment('Name');
            $table->string('code', 16)->comment('Code');
            $table->string('image')->comment('Country Icon');
            $table->integer('position')->default(0)->comment('Sort order');
            $table->tinyInteger('active')->comment('Active');
            $table->timestamps();
        });

        Schema::create('page_modules', function (Blueprint $table) {
            $table->comment('DIY Modules');
            $table->bigIncrements('id')->comment('ID');
            $table->string('name')->comment('Module Name');
            $table->string('code')->comment('Module Code');
            $table->json('data')->comment('Data');
            $table->timestamps();
        });

        Schema::create('page_translations', function (Blueprint $table) {
            $table->comment('Article Page');
            $table->bigIncrements('id')->comment('ID');
            $table->integer('page_id')->index('pt_page_id')->comment('Article ID');
            $table->string('locale')->comment('Locale Code');
            $table->string('title')->comment('Title');
            $table->text('content')->nullable()->comment('Content');
            $table->text('template')->nullable()->comment('Content');
            $table->string('meta_title')->nullable()->comment('Meta Title');
            $table->string('meta_description')->nullable()->comment('Meta description');
            $table->string('meta_keywords')->nullable()->comment('Meta keywords');
            $table->timestamps();
        });

        Schema::create('pages', function (Blueprint $table) {
            $table->comment('Page');
            $table->bigIncrements('id')->comment('ID');
            $table->string('slug', 128)->nullable()->unique()->comment('URL Slug');
            $table->integer('position')->default(0)->comment('Sort order');
            $table->integer('viewed')->default(0)->comment('Viewed');
            $table->boolean('active')->comment('Active');
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email', 128)->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id', 128)->primary();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->comment('Setting');
            $table->bigIncrements('id')->comment('ID');
            $table->string('space')->comment('Group, Like: system, stripe, paypal');
            $table->string('name')->comment('Field Name');
            $table->text('value')->comment('Field Value');
            $table->boolean('json')->default(false)->comment('JSON Or Not');
            $table->timestamps();
        });

        Schema::create('tag_translations', function (Blueprint $table) {
            $table->comment('Article Tag Translation');
            $table->bigIncrements('id')->comment('ID');
            $table->integer('tag_id')->nullable();
            $table->string('locale')->comment('Locale Code');
            $table->string('name')->comment('Name');
            $table->timestamps();
        });

        Schema::create('tags', function (Blueprint $table) {
            $table->comment('Article Tag');
            $table->bigIncrements('id')->comment('ID');
            $table->string('slug', 128)->nullable()->unique()->comment('URL Slug');
            $table->integer('position')->default(0)->comment('Sort order');
            $table->boolean('active')->comment('Active');
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email', 128)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');

        Schema::dropIfExists('tags');

        Schema::dropIfExists('tag_translations');

        Schema::dropIfExists('settings');

        Schema::dropIfExists('sessions');

        Schema::dropIfExists('plugins');

        Schema::dropIfExists('password_reset_tokens');

        Schema::dropIfExists('pages');

        Schema::dropIfExists('page_translations');

        Schema::dropIfExists('page_modules');

        Schema::dropIfExists('locales');

        Schema::dropIfExists('jobs');

        Schema::dropIfExists('job_batches');

        Schema::dropIfExists('failed_jobs');

        Schema::dropIfExists('catalogs');

        Schema::dropIfExists('catalog_translations');

        Schema::dropIfExists('cache_locks');

        Schema::dropIfExists('cache');

        Schema::dropIfExists('articles');

        Schema::dropIfExists('article_translations');

        Schema::dropIfExists('article_tags');

        Schema::dropIfExists('admins');

        Schema::dropIfExists('admin_tokens');
    }
};
