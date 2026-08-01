<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use InnoCMS\Common\Models\Category;
use InnoCMS\Common\Models\Category\Translation as CategoryTranslation;
use InnoCMS\Common\Models\Product;
use InnoCMS\Common\Models\Product\Translation as ProductTranslation;

class ProductSeeder extends BaseSeeder
{
    public function run(): void
    {
        // Create "软件产品" category (id = 5)
        $this->seedCategory();

        $items = $this->getProducts();
        if ($items) {
            $this->safeTruncate(Product::class);
            foreach ($items as $item) {
                Product::query()->create($item);
            }
        }

        $items = $this->getProductTranslations();
        if ($items) {
            $this->safeTruncate(ProductTranslation::class);
            foreach ($items as $item) {
                ProductTranslation::query()->create($item);
            }
        }

        // Link products to the software-products category
        $this->seedProductCategories();
    }

    private function seedCategory(): void
    {
        if (! Category::find(5)) {
            Category::query()->create([
                'id'        => 5,
                'parent_id' => 0,
                'slug'      => 'software-products',
                'position'  => 1,
                'active'    => 1,
            ]);
            CategoryTranslation::query()->create([
                'category_id'      => 5,
                'locale'           => 'zh-cn',
                'name'             => '软件产品',
                'summary'          => '帆连科技自研软件产品体系',
                'meta_title'       => '软件产品｜帆连科技',
                'meta_description' => 'InnoCMS、InnoShop、InnoCRM、InnoCard 等企业级软件产品',
            ]);
            CategoryTranslation::query()->create([
                'category_id'      => 5,
                'locale'           => 'en',
                'name'             => 'Software Products',
                'summary'          => 'FunnLink self-developed software product suite',
                'meta_title'       => 'Software Products | FunnLink',
                'meta_description' => 'Enterprise software products: InnoCMS, InnoShop, InnoCRM, InnoCard',
            ]);
        }
    }

    private function seedProductCategories(): void
    {
        DB::table('product_categories')->delete();
        $productIds = Product::query()->pluck('id')->toArray();
        $rows       = [];
        foreach ($productIds as $pid) {
            $rows[] = ['product_id' => $pid, 'category_id' => 5];
        }
        if ($rows) {
            DB::table('product_categories')->insert($rows);
        }
    }

    /**
     * @return array[]
     */
    private function getProducts(): array
    {
        return [
            [
                'id'       => 1,
                'slug'     => 'innocms',
                'images'   => ['https://picsum.photos/seed/innocms-cover/800/500'],
                'spu_code' => 'INNO-CMS',
                'position' => 1,
                'active'   => 1,
                'link'     => 'https://www.innocms.com',
            ],
            [
                'id'       => 2,
                'slug'     => 'innoshop',
                'images'   => ['https://picsum.photos/seed/innoshop-cover/800/500'],
                'spu_code' => 'INNO-SHOP',
                'position' => 2,
                'active'   => 1,
                'link'     => 'https://www.innoshop.cn',
            ],
            [
                'id'       => 3,
                'slug'     => 'innocrm',
                'images'   => ['https://picsum.photos/seed/innocrm-cover/800/500'],
                'spu_code' => 'INNO-CRM',
                'position' => 3,
                'active'   => 1,
                'link'     => 'https://www.innocrm.com.cn',
            ],
            [
                'id'       => 4,
                'slug'     => 'innocard',
                'images'   => ['https://picsum.photos/seed/innocard-cover/800/500'],
                'spu_code' => 'INNO-CARD',
                'position' => 4,
                'active'   => 1,
                'link'     => 'https://www.innocard.cn',
            ],
            [
                'id'       => 5,
                'slug'     => 'tianfutrade',
                'images'   => ['https://picsum.photos/seed/tianfutrade-cover/800/500'],
                'spu_code' => 'TF-TRADE',
                'position' => 5,
                'active'   => 1,
                'link'     => 'https://www.tianfutrade.com',
            ],
        ];
    }

    /**
     * @return array[]
     */
    private function getProductTranslations(): array
    {
        $rows = [
            // InnoCMS
            [1, 'zh-cn', 'InnoCMS', '轻量化企业内容管理系统，模块化架构 + 插件扩展，适合企业官网与多语言内容运营。',
                '基于 Laravel 构建，采用模块化 innopacks 架构与 Hook 插件系统，安装即用、加载飞快。支持多语言、主题定制和 SEO 优化。', '轻量·可扩展·面向 B2B', 'InnoCMS 下载使用 | 帆连科技', 'InnoCMS 是一款专为企业官网快速建站而设计的轻量级 CMS。', 'InnoCMS,CMS,企业官网,开源CMS,Laravel'],
            [1, 'en', 'InnoCMS', 'Lightweight enterprise CMS with modular architecture and plugin ecosystem for corporate websites and multilingual content.',
                'Built on Laravel with modular innopacks architecture and a Hook-based plugin system. Ready out of the box, blazing fast. Supports multilingual, theme customization and SEO.', 'Lightweight·Extensible·B2B-ready', 'InnoCMS Download | FunnLink', 'InnoCMS is a lightweight CMS designed for rapid corporate website building.', 'InnoCMS,CMS,enterprise,open source,Laravel'],

            // InnoShop
            [2, 'zh-cn', 'InnoShop', '开源跨境电商独立站系统，支持多版本、多语言、多币种，Laravel + Vue 技术栈。',
                '旗舰级开源电商系统，提供 Community / Enterprise / Factory / B2B 四个版本。功能强大、生态丰富，20K+ 下载量验证。', '开源电商·多版本·生态丰富', 'InnoShop 开源电商系统 | 帆连科技', 'InnoShop 是帆连科技旗舰开源跨境电商独立站系统。', 'InnoShop,电商,开源,跨境电商,独立站,Laravel'],
            [2, 'en', 'InnoShop', 'Open-source cross-border ecommerce platform with multi-version, multilingual, multi-currency support. Laravel + Vue stack.',
                'Flagship open-source ecommerce system with Community / Enterprise / Factory / B2B editions. Powerful, rich ecosystem, 20K+ downloads.', 'Open Source·Multi-Version·Rich Ecosystem', 'InnoShop Open Source Ecommerce | FunnLink', 'InnoShop is FunnLink\'s flagship open-source ecommerce platform.', 'InnoShop,ecommerce,open source,cross-border,Laravel'],

            // InnoCRM
            [3, 'zh-cn', 'InnoCRM', '智能化客户关系管理系统，提升销售转化率和客户满意度，助力企业高效增长。',
                '覆盖线索管理、客户画像、销售漏斗、自动化营销全流程。帮助企业建立完整的客户生命周期管理体系。', '智能销售·客户画像·自动化', 'InnoCRM 客户关系管理 | 帆连科技', 'InnoCRM 是智能化客户关系管理系统。', 'InnoCRM,CRM,客户管理,销售自动化,客户画像'],
            [3, 'en', 'InnoCRM', 'Intelligent CRM system that improves sales conversion and customer satisfaction for business growth.',
                'Covers lead management, customer profiles, sales funnel, and marketing automation. Build a complete customer lifecycle management system.', 'Smart Sales·Customer Profiles·Automation', 'InnoCRM Customer Relationship | FunnLink', 'InnoCRM is an intelligent customer relationship management system.', 'InnoCRM,CRM,customer management,sales automation'],

            // InnoCard
            [4, 'zh-cn', 'InnoCard', '服务行业会员管理解决方案，提升客户粘性，打造高效会员运营体系。',
                '积分管理、等级权益、精准触达，帮助服务行业建立完整的会员运营体系，提升复购率和客户忠诚度。', '会员积分·等级权益·精准触达', 'InnoCard 会员管理系统 | 帆连科技', 'InnoCard 是服务行业会员管理解决方案。', 'InnoCard,会员管理,积分,会员运营,服务行业'],
            [4, 'en', 'InnoCard', 'Membership management solution for service industries, improving customer loyalty and operational efficiency.',
                'Points management, tier benefits, precision outreach. Help service industries build complete membership systems and boost repeat purchases.', 'Member Points·Tier Benefits·Precision', 'InnoCard Membership System | FunnLink', 'InnoCard is a membership management solution for service industries.', 'InnoCard,membership,points,loyalty,service'],

            // TianfuTrade
            [5, 'zh-cn', 'TianfuTrade', '多供应商 B2B 采购平台，连接供需双方，优化采购流程，降低企业成本。',
                '面向大宗商品贸易与供应链协同，支持多供应商入驻、智能询价、订单跟踪和数据分析，助力企业数字化采购转型。', '多供应商·智能采购·供应链协同', 'TianfuTrade B2B 采购平台 | 帆连科技', 'TianfuTrade 是多供应商 B2B 采购平台。', 'TianfuTrade,B2B,采购平台,供应链,多供应商'],
            [5, 'en', 'TianfuTrade', 'Multi-supplier B2B procurement platform connecting buyers and suppliers, optimizing purchasing workflows.',
                'For commodity trading and supply chain collaboration. Supports multi-vendor onboarding, smart RFQ, order tracking, and data analytics.', 'Multi-Vendor·Smart Procurement·Supply Chain', 'TianfuTrade B2B Platform | FunnLink', 'TianfuTrade is a multi-supplier B2B procurement platform.', 'TianfuTrade,B2B,procurement,supply chain'],
        ];

        $translations = [];
        foreach ($rows as $row) {
            [$id, $locale, $name, $summary, $content, $sellingPoint, $metaTitle, $metaDesc, $metaKeywords] = $row;
            $translations[]                                                                                = [
                'product_id'       => $id,
                'locale'           => $locale,
                'name'             => $name,
                'summary'          => $summary,
                'content'          => $content,
                'selling_point'    => $sellingPoint,
                'meta_title'       => $metaTitle,
                'meta_description' => $metaDesc,
                'meta_keywords'    => $metaKeywords,
            ];
        }

        return $translations;
    }
}
