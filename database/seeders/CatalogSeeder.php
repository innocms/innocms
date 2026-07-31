<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Database\Seeders;

use InnoCMS\Common\Models\Catalog;
use InnoCMS\Common\Models\Catalog\Translation;

class CatalogSeeder extends BaseSeeder
{
    public function run(): void
    {
        $items = $this->getCatalogs();
        if ($items) {
            $this->safeTruncate(Catalog::class);
            foreach ($items as $item) {
                Catalog::query()->create($item);
            }
        }

        $items = $this->getCatalogTranslations();
        if ($items) {
            $this->safeTruncate(Translation::class);
            foreach ($items as $item) {
                Translation::query()->create($item);
            }
        }
    }

    /**
     * Default (official) theme catalog tree:
     *   resources -> docs / news
     *   solutions (flat)
     *
     * @return array[]
     */
    private function getCatalogs(): array
    {
        return [
            ['id' => 1, 'parent_id' => 0, 'slug' => 'resources', 'position' => 1, 'active' => 1],
            ['id' => 2, 'parent_id' => 1, 'slug' => 'docs', 'position' => 1, 'active' => 1],
            ['id' => 3, 'parent_id' => 1, 'slug' => 'news', 'position' => 2, 'active' => 1],
            ['id' => 4, 'parent_id' => 0, 'slug' => 'solutions', 'position' => 2, 'active' => 1],
        ];
    }

    /**
     * @return array[]
     */
    private function getCatalogTranslations(): array
    {
        $rows = [
            [1, 'resources', '资源中心', 'Resources', 'InnoCMS 的使用文档、开发指南与产品动态。', 'InnoCMS documentation, developer guides and product updates.'],
            [2, 'docs', '开发文档', 'Documentation', '安装配置、主题开发、插件开发与多语言配置教程。', 'Installation, theme development, plugin development and multilingual setup.'],
            [3, 'news', '产品动态', 'Product News', '版本发布、生态合作与托管服务进展。', 'Releases, ecosystem partnerships and hosting updates.'],
            [4, 'solutions', '解决方案', 'Solutions', '面向 B2B 外贸独立站与品牌企业官网的建站方案。', 'Website solutions for B2B export sites and brand corporate portals.'],
        ];

        $translations = [];
        foreach ($rows as [$catalogId, $slug, $zhTitle, $enTitle, $zhSummary, $enSummary]) {
            $translations[] = [
                'catalog_id'       => $catalogId,
                'locale'           => 'zh-cn',
                'title'            => $zhTitle,
                'summary'          => $zhSummary,
                'meta_title'       => "{$zhTitle}｜InnoCMS",
                'meta_description' => $zhSummary,
                'meta_keywords'    => "{$zhTitle},InnoCMS,CMS",
            ];
            $translations[] = [
                'catalog_id'       => $catalogId,
                'locale'           => 'en',
                'title'            => $enTitle,
                'summary'          => $enSummary,
                'meta_title'       => "{$enTitle} | InnoCMS",
                'meta_description' => $enSummary,
                'meta_keywords'    => "{$enTitle}, InnoCMS, CMS",
            ];
        }

        return $translations;
    }
}
