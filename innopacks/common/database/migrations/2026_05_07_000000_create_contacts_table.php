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
        if (! Schema::hasTable('contacts')) {
            Schema::create('contacts', function (Blueprint $table) {
                $table->comment('Contacts');
                $table->bigIncrements('id')->comment('ID');
                $table->string('name', 100)->nullable()->comment('Contact Name');
                $table->string('email', 100)->comment('Email');
                $table->string('phone', 30)->nullable()->comment('Phone');
                $table->string('company', 200)->nullable()->comment('Company');
                $table->text('content')->comment('Message Content');
                $table->boolean('status')->default(false)->comment('Read Status: 0=unread, 1=read');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
