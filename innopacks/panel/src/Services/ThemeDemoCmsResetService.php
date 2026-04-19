<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Panel\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Clears CMS demo-related content (articles, catalogs, pages, tags).
 * Order matches FK dependencies; does not touch admins, settings, or locales.
 */
class ThemeDemoCmsResetService
{
    /**
     * Tables to truncate when "clear existing content before import" is enabled.
     *
     * @var array<int, string>
     */
    protected array $tablesInOrder = [
        'article_tags',
        'article_translations',
        'articles',
        'catalog_translations',
        'catalogs',
        'page_translations',
        'pages',
        'page_modules',
        'tag_translations',
        'tags',
    ];

    public function truncateCmsContent(): void
    {
        // Do not wrap in DB::transaction(): on MySQL/MariaDB, TRUNCATE issues an implicit
        // commit and ends the outer transaction, so Laravel's COMMIT afterward raises
        // "There is no active transaction".
        Schema::disableForeignKeyConstraints();
        try {
            foreach ($this->tablesInOrder as $table) {
                if (Schema::hasTable($table)) {
                    DB::table($table)->truncate();
                }
            }
        } finally {
            Schema::enableForeignKeyConstraints();
        }
    }
}
