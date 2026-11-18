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
     * audit_logs: audit log (ported down from the platform package, with the tenant column removed).
     *
     * PII decrypted views / key operations / sensitive operations are all recorded. Writes are performed explicitly by AuditLogger,
     * no global scope is attached; append only (the model layer disables update).
     */
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->comment('审计日志（PII 解密查看/密钥操作/敏感操作全量留痕）');
            $table->bigIncrements('id');
            $table->string('actor_type', 32)->comment('admin/user/worker/system/platform');
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->string('action', 128);
            $table->string('resource_type', 64);
            $table->string('resource_id', 64);
            $table->json('before_json')->nullable();
            $table->json('after_json')->nullable();
            $table->string('ip', 45)->default('');
            $table->string('user_agent', 500)->default('');
            $table->timestamp('created_at')->nullable();

            $table->index(['actor_type', 'actor_id', 'created_at'], 'idx_audit_actor');
            $table->index(['action', 'created_at'], 'idx_audit_action');
            $table->index(['resource_type', 'resource_id', 'created_at'], 'idx_audit_resource');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
