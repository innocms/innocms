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
     * B2B demo site (Apex Precision) catalog tree:
     *   products   -> cnc-machining / sheet-metal / die-casting / surface-finishing
     *   industries -> automotive / medical / aerospace / electronics
     *   news       -> company-news / industry-news
     *
     * @return array[]
     */
    private function getCatalogs(): array
    {
        return [
            ['id' => 1, 'parent_id' => 0, 'slug' => 'products', 'position' => 1, 'active' => 1],

            ['id' => 2, 'parent_id' => 1, 'slug' => 'cnc-machining', 'position' => 1, 'active' => 1],
            ['id' => 3, 'parent_id' => 1, 'slug' => 'sheet-metal', 'position' => 2, 'active' => 1],
            ['id' => 4, 'parent_id' => 1, 'slug' => 'die-casting', 'position' => 3, 'active' => 1],
            ['id' => 5, 'parent_id' => 1, 'slug' => 'surface-finishing', 'position' => 4, 'active' => 1],

            ['id' => 6, 'parent_id' => 0, 'slug' => 'industries', 'position' => 2, 'active' => 1],

            ['id' => 7, 'parent_id' => 6, 'slug' => 'automotive', 'position' => 1, 'active' => 1],
            ['id' => 8, 'parent_id' => 6, 'slug' => 'medical', 'position' => 2, 'active' => 1],
            ['id' => 9, 'parent_id' => 6, 'slug' => 'aerospace', 'position' => 3, 'active' => 1],
            ['id' => 10, 'parent_id' => 6, 'slug' => 'electronics', 'position' => 4, 'active' => 1],

            ['id' => 11, 'parent_id' => 0, 'slug' => 'news', 'position' => 3, 'active' => 1],

            ['id' => 12, 'parent_id' => 11, 'slug' => 'company-news', 'position' => 1, 'active' => 1],
            ['id' => 13, 'parent_id' => 11, 'slug' => 'industry-news', 'position' => 2, 'active' => 1],
        ];
    }

    /**
     * @return array[]
     */
    private function getCatalogTranslations(): array
    {
        $rows = [
            [1, 'products', '产品中心', 'Products', '从 CNC 加工、钣金、压铸到表面处理的一站式精密零部件制造服务。', 'One-stop precision parts manufacturing: CNC machining, sheet metal, die casting and surface finishing.'],
            [2, 'cnc-machining', 'CNC加工', 'CNC Machining', '3轴/5轴 CNC 铣削与车削，公差可达 ±0.005mm，支持单件打样到批量生产。', '3-axis and 5-axis CNC milling and turning with tolerances down to ±0.005mm, from single prototypes to volume production.'],
            [3, 'sheet-metal', '钣金加工', 'Sheet Metal', '激光切割、折弯、焊接与铆接，定制机箱机柜与结构件。', 'Laser cutting, bending, welding and riveting for custom enclosures and structural parts.'],
            [4, 'die-casting', '压铸成型', 'Die Casting', '80–800T 压铸机，铝合金与锌合金压铸件，适合大批量轻量化结构件。', '80–800T die casting machines for aluminum and zinc alloy parts, ideal for high-volume lightweight structures.'],
            [5, 'surface-finishing', '表面处理', 'Surface Finishing', '阳极氧化、喷粉、电镀、抛光与钝化，提升零件性能与外观。', 'Anodizing, powder coating, electroplating, polishing and passivation to enhance performance and appearance.'],
            [6, 'industries', '行业应用', 'Industries', '为汽车、医疗、航空航天与消费电子等行业提供合规、可靠的精密零部件。', 'Compliant, reliable precision components for automotive, medical, aerospace and consumer electronics.'],
            [7, 'automotive', '汽车制造', 'Automotive', 'IATF 16949 体系下的汽车精密零部件与新能源三电系统结构件。', 'IATF 16949 certified automotive components and structural parts for new-energy EV systems.'],
            [8, 'medical', '医疗器械', 'Medical', 'ISO 13485 受控流程，医疗仪器外壳、手术器械零件与植入类试制件。', 'ISO 13485 controlled processes for medical housings, surgical instrument parts and implant prototypes.'],
            [9, 'aerospace', '航空航天', 'Aerospace', '钛合金、高温合金等难加工材料的航空航天零部件加工。', 'Aerospace machining of difficult materials including titanium and superalloys.'],
            [10, 'electronics', '消费电子', 'Electronics', '精密电子外壳、散热器、连接器与屏蔽罩，兼顾外观与精度。', 'Precision electronic housings, heat sinks, connectors and shielding with cosmetic-grade finishes.'],
            [11, 'news', '新闻资讯', 'News', '公司新闻与行业动态，了解傲锋精密的最新进展与制造技术趋势。', 'Company announcements and industry insights from Apex Precision.'],
            [12, 'company-news', '公司新闻', 'Company News', '傲锋精密的企业动态、认证更新与产能扩张进展。', 'Corporate updates, certifications and capacity expansion at Apex Precision.'],
            [13, 'industry-news', '行业动态', 'Industry News', '精密加工行业的技术趋势、供应链与市场观察。', 'Technology trends, supply chain and market observations in precision manufacturing.'],
        ];

        $translations = [];
        foreach ($rows as [$catalogId, $slug, $zhTitle, $enTitle, $zhSummary, $enSummary]) {
            $translations[] = [
                'catalog_id'       => $catalogId,
                'locale'           => 'zh-cn',
                'title'            => $zhTitle,
                'summary'          => $zhSummary,
                'meta_title'       => "{$zhTitle}｜傲锋精密",
                'meta_description' => $zhSummary,
                'meta_keywords'    => "{$zhTitle},傲锋精密,Apex Precision",
            ];
            $translations[] = [
                'catalog_id'       => $catalogId,
                'locale'           => 'en',
                'title'            => $enTitle,
                'summary'          => $enSummary,
                'meta_title'       => "{$enTitle} | Apex Precision",
                'meta_description' => $enSummary,
                'meta_keywords'    => "{$enTitle}, Apex Precision, precision manufacturing",
            ];
        }

        return $translations;
    }
}
