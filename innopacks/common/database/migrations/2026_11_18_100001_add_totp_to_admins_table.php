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
     * Panel 2FA (TOTP) support: admins.totp_secret_enc.
     *
     * Idempotency guard (for compatibility when existing products are upgraded, e.g. existing products may already ship the same column); raw SQL must build the table prefix manually.
     */
    public function up(): void
    {
        if (Schema::hasColumn('admins', 'totp_secret_enc')) {
            return;
        }

        Schema::table('admins', function (Blueprint $table) {
            $table->binary('totp_secret_enc')->nullable()->comment('TOTP 密钥（Crypt AES 加密存，未启用为 NULL）');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('admins', 'totp_secret_enc')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->dropColumn('totp_secret_enc');
            });
        }
    }
};
