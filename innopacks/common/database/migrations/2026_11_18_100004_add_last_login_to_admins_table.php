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
     * Track the last admin login time (set by the panel login flow).
     */
    public function up(): void
    {
        if (Schema::hasTable('admins') && ! Schema::hasColumn('admins', 'last_login_at')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->dateTime('last_login_at')->nullable()->comment('Last login time');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('admins', 'last_login_at')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->dropColumn('last_login_at');
            });
        }
    }
};
