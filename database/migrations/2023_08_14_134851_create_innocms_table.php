<?php
/**
 * Copyright (c) Since 2023 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <innocms@foxmail.com>
 * @license    https://opensource.org/license/mit
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
        Schema::create('article_tags', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('article_id')->index('article_id');
            $table->integer('tag_id')->index('tag_id');
        });

        Schema::create('articles', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('title', 200);
            $table->text('content');
            $table->integer('author_id')->index('author_id');
            $table->integer('category_id')->index('category_id');
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->timestamps();
        });

        Schema::create('attachmentable', function (Blueprint $table) {
            $table->increments('id');
            $table->string('attachmentable_type');
            $table->unsignedInteger('attachmentable_id');
            $table->unsignedInteger('attachment_id')->index('ic_attachmentable_attachment_id_foreign');

            $table->index(['attachmentable_type', 'attachmentable_id']);
        });

        Schema::create('attachments', function (Blueprint $table) {
            $table->increments('id');
            $table->text('name');
            $table->text('original_name');
            $table->string('mime');
            $table->string('extension')->nullable();
            $table->bigInteger('size')->default(0);
            $table->integer('sort')->default(0);
            $table->text('path');
            $table->text('description')->nullable();
            $table->text('alt')->nullable();
            $table->text('hash')->nullable();
            $table->string('disk')->default('public');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('group')->nullable();
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name', 50);
            $table->timestamps();
        });

        Schema::create('comments', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('article_id')->index('article_id');
            $table->integer('user_id');
            $table->text('content');
            $table->timestamps();
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        Schema::create('images', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('article_id')->nullable();
            $table->string('path')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->string('type');
            $table->string('notifiable_type');
            $table->unsignedBigInteger('notifiable_id');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['notifiable_type', 'notifiable_id']);
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('role_users', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->unsignedInteger('role_id')->index('ic_role_users_role_id_foreign');

            $table->primary(['user_id', 'role_id']);
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug')->unique();
            $table->string('name');
            $table->json('permissions')->nullable();
            $table->timestamps();
        });

        Schema::create('tags', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name', 50);
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
            $table->json('permissions')->nullable();
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

        Schema::dropIfExists('roles');

        Schema::dropIfExists('role_users');

        Schema::dropIfExists('personal_access_tokens');

        Schema::dropIfExists('password_reset_tokens');

        Schema::dropIfExists('notifications');

        Schema::dropIfExists('images');

        Schema::dropIfExists('failed_jobs');

        Schema::dropIfExists('comments');

        Schema::dropIfExists('categories');

        Schema::dropIfExists('attachments');

        Schema::dropIfExists('attachmentable');

        Schema::dropIfExists('articles');

        Schema::dropIfExists('article_tags');
    }
};
