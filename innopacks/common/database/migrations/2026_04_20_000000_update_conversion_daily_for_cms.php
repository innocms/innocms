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
        if (Schema::hasTable('conversion_daily')) {
            Schema::table('conversion_daily', function (Blueprint $table) {
                if (! Schema::hasColumn('conversion_daily', 'home_views')) {
                    $table->unsignedInteger('home_views')->default(0)->after('date')->comment('Home Page Views');
                }
                if (! Schema::hasColumn('conversion_daily', 'catalog_views')) {
                    $table->unsignedInteger('catalog_views')->default(0)->after('home_views')->comment('Catalog Views');
                }
                if (! Schema::hasColumn('conversion_daily', 'article_views')) {
                    $table->unsignedInteger('article_views')->default(0)->after('catalog_views')->comment('Article Views');
                }
                if (! Schema::hasColumn('conversion_daily', 'searches')) {
                    $table->unsignedInteger('searches')->default(0)->after('article_views')->comment('Search Events');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('conversion_daily')) {
            Schema::table('conversion_daily', function (Blueprint $table) {
                $columns = ['home_views', 'catalog_views', 'article_views', 'searches'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('conversion_daily', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
