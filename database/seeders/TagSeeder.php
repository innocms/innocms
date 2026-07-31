<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Database\Seeders;

use InnoCMS\Common\Models\Tag;
use InnoCMS\Common\Models\Tag\Translation;

class TagSeeder extends BaseSeeder
{
    public function run(): void
    {
        $items = $this->getTags();
        if ($items) {
            $this->safeTruncate(Tag::class);
            foreach ($items as $item) {
                Tag::query()->create($item);
            }
        }

        $items = $this->getTagTranslations();
        if ($items) {
            $this->safeTruncate(Translation::class);
            foreach ($items as $item) {
                Translation::query()->create($item);
            }
        }
    }

    /**
     * @return array[]
     */
    private function getTags(): array
    {
        return [
            ['id' => 1, 'slug' => 'open-source', 'position' => 1, 'active' => 1],
            ['id' => 2, 'slug' => 'cms', 'position' => 2, 'active' => 1],
            ['id' => 3, 'slug' => 'plugin', 'position' => 3, 'active' => 1],
            ['id' => 4, 'slug' => 'theme', 'position' => 4, 'active' => 1],
            ['id' => 5, 'slug' => 'multilingual', 'position' => 5, 'active' => 1],
            ['id' => 6, 'slug' => 'b2b', 'position' => 6, 'active' => 1],
        ];
    }

    /**
     * @return array[]
     */
    private function getTagTranslations(): array
    {
        $rows = [
            [1, '开源', 'Open Source'],
            [2, 'CMS', 'CMS'],
            [3, '插件', 'Plugin'],
            [4, '主题', 'Theme'],
            [5, '多语言', 'Multilingual'],
            [6, 'B2B', 'B2B'],
        ];

        $translations = [];
        foreach ($rows as [$tagId, $zhName, $enName]) {
            $translations[] = ['tag_id' => $tagId, 'locale' => 'zh-cn', 'name' => $zhName];
            $translations[] = ['tag_id' => $tagId, 'locale' => 'en', 'name' => $enName];
        }

        return $translations;
    }
}
