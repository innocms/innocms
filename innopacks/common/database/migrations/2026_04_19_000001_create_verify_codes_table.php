<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('verify_codes', function (Blueprint $table) {
            $table->id();
            $table->string('account');
            $table->string('code');
            $table->string('type')->default('register');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['account', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verify_codes');
    }
};
