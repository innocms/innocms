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
            ['id' => 1, 'slug' => 'cnc-machining', 'position' => 1, 'active' => 1],
            ['id' => 2, 'slug' => 'precision-parts', 'position' => 2, 'active' => 1],
            ['id' => 3, 'slug' => 'oem-odm', 'position' => 3, 'active' => 1],
            ['id' => 4, 'slug' => 'iso-9001', 'position' => 4, 'active' => 1],
            ['id' => 5, 'slug' => 'aluminum', 'position' => 5, 'active' => 1],
            ['id' => 6, 'slug' => 'stainless-steel', 'position' => 6, 'active' => 1],
            ['id' => 7, 'slug' => 'five-axis', 'position' => 7, 'active' => 1],
            ['id' => 8, 'slug' => 'rapid-prototyping', 'position' => 8, 'active' => 1],
        ];
    }

    /**
     * @return array[]
     */
    private function getTagTranslations(): array
    {
        $rows = [
            [1, 'CNC加工', 'CNC Machining'],
            [2, '精密零件', 'Precision Parts'],
            [3, 'OEM/ODM', 'OEM/ODM'],
            [4, 'ISO 9001', 'ISO 9001'],
            [5, '铝合金', 'Aluminum'],
            [6, '不锈钢', 'Stainless Steel'],
            [7, '五轴加工', '5-Axis'],
            [8, '快速打样', 'Rapid Prototyping'],
        ];

        $translations = [];
        foreach ($rows as [$tagId, $zhName, $enName]) {
            $translations[] = ['tag_id' => $tagId, 'locale' => 'zh-cn', 'name' => $zhName];
            $translations[] = ['tag_id' => $tagId, 'locale' => 'en', 'name' => $enName];
        }

        return $translations;
    }
}
