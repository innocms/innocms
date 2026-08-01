<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Database\Seeders;

use InnoCMS\Common\Models\Page;
use InnoCMS\Common\Models\Page\Translation;

class PageSeeder extends BaseSeeder
{
    public function run(): void
    {
        $items = $this->getPages();
        if ($items) {
            $this->safeTruncate(Page::class);
            foreach ($items as $item) {
                Page::query()->create($item);
            }
        }

        $items = $this->getPageTranslations();
        if ($items) {
            $this->safeTruncate(Translation::class);
            foreach ($items as $item) {
                Translation::query()->create($item);
            }
        }
    }

    /**
     * Default (official) theme pages: about (company) + contact.
     * InnoCMS-specific intro / get-started / features live in articles + product detail.
     *
     * @return array[]
     */
    private function getPages(): array
    {
        return [
            ['id' => 1, 'slug' => 'about', 'position' => 1, 'viewed' => 720, 'active' => 1],
            ['id' => 2, 'slug' => 'contact', 'position' => 2, 'viewed' => 610, 'active' => 1],
        ];
    }

    /**
     * @return array[]
     */
    private function getPageTranslations(): array
    {
        return [
            // ---------- about (company) ----------
            [
                'page_id'    => 1, 'locale' => 'zh-cn', 'title' => '关于我们',
                'content'    => '<p><strong>成都帆连科技有限公司（FunnLink）</strong>专注于为跨境电商与外贸企业提供全栈数字化产品与实施服务，围绕独立站、内容管理、客户运营与会员体系构建了一整套开源产品矩阵。</p><h3>我们做什么</h3><ul><li><strong>InnoCMS</strong>：轻量级企业官网 CMS，模块化架构 + Hook 插件，几分钟搭建品牌官网</li><li><strong>InnoShop</strong>：开源跨境电商独立站，支持 Community / Enterprise / Factory / B2B 多版本</li><li><strong>InnoCRM</strong>：智能化客户关系管理，从线索到复购全生命周期运营</li><li><strong>InnoCard</strong>：服务行业会员管理，积分、等级权益与精准触达</li><li><strong>TianfuTrade</strong>：多供应商 B2B 采购平台，连接供需、协同供应链</li></ul><h3>我们的理念</h3><p>以可执行的产品与专业服务，让企业优质内容用更短路径连接目标客户。围绕开源与开放生态，与开发者、服务商与企业客户共建长期价值。</p><h3>联系方式</h3><p>邮箱：<a href="mailto:team@innoshop.com">team@innoshop.com</a>　电话：+86 136-4808-9236</p>',
                'meta_title' => '关于我们｜成都帆连科技 FunnLink', 'meta_description' => '帆连科技（FunnLink）为跨境电商与外贸企业提供 InnoCMS、InnoShop、InnoCRM、InnoCard、TianfuTrade 等全栈数字化产品。', 'meta_keywords' => '帆连科技,FunnLink,关于我们,InnoCMS,InnoShop,InnoCRM',
            ],
            [
                'page_id'    => 1, 'locale' => 'en', 'title' => 'About Us',
                'content'    => '<p><strong>Chengdu FunnLink Technology Co., Ltd.</strong> builds full-stack digital products for cross-border ecommerce and export businesses, spanning independent sites, content management, customer operations and membership systems.</p><h3>What we build</h3><ul><li><strong>InnoCMS</strong>: a lightweight corporate CMS — modular architecture, hook plugins, launch a brand site in minutes</li><li><strong>InnoShop</strong>: open-source cross-border ecommerce with Community / Enterprise / Factory / B2B editions</li><li><strong>InnoCRM</strong>: intelligent customer relationship management, from lead to repeat purchase</li><li><strong>InnoCard</strong>: membership management for service industries — points, tier benefits and precision outreach</li><li><strong>TianfuTrade</strong>: a multi-supplier B2B procurement platform connecting buyers and suppliers</li></ul><h3>Our philosophy</h3><p>Executable products and professional services that connect great content to target customers through shorter paths. Open source and an open ecosystem, building long-term value together with developers, agencies and businesses.</p><h3>Contact</h3><p>Email: <a href="mailto:team@innoshop.com">team@innoshop.com</a>　Phone: +86 136-4808-9236</p>',
                'meta_title' => 'About Us | FunnLink', 'meta_description' => 'FunnLink builds full-stack digital products for cross-border ecommerce: InnoCMS, InnoShop, InnoCRM, InnoCard and TianfuTrade.', 'meta_keywords' => 'FunnLink, about, InnoCMS, InnoShop, InnoCRM',
            ],
            // ---------- contact ----------
            [
                'page_id'    => 2, 'locale' => 'zh-cn', 'title' => '联系我们',
                'content'    => '<p>需要技术咨询、合作洽谈或 SaaS 托管服务？欢迎通过以下方式联系我们。</p><h3>联系方式</h3><table class="table table-bordered"><tbody><tr><th>官网</th><td><a href="https://www.innocms.com">www.innocms.com</a></td></tr><tr><th>邮箱</th><td><a href="mailto:team@innoshop.com">team@innoshop.com</a></td></tr><tr><th>电话</th><td>+86 136-4808-9236</td></tr><tr><th>GitHub</th><td><a href="https://github.com/innocms/innocms">innocms/innocms</a></td></tr></tbody></table><h3>SaaS 托管</h3><p>不想自己动手？我们提供独立云服务器、专人部署、备案/域名/SSL/CDN 全托管与 7×12 小时服务。</p>',
                'meta_title' => '联系我们｜FunnLink', 'meta_description' => '联系帆连科技：技术咨询、合作洽谈与 SaaS 托管服务。', 'meta_keywords' => 'FunnLink,联系,支持,托管',
            ],
            [
                'page_id'    => 2, 'locale' => 'en', 'title' => 'Contact Us',
                'content'    => '<p>Need technical advice, a partnership or managed hosting? Reach us through the channels below.</p><h3>Channels</h3><table class="table table-bordered"><tbody><tr><th>Website</th><td><a href="https://www.innocms.com">www.innocms.com</a></td></tr><tr><th>Email</th><td><a href="mailto:team@innoshop.com">team@innoshop.com</a></td></tr><tr><th>Phone</th><td>+86 136-4808-9236</td></tr><tr><th>GitHub</th><td><a href="https://github.com/innocms/innocms">innocms/innocms</a></td></tr></tbody></table><h3>Managed hosting</h3><p>Hands-off? We offer dedicated cloud servers, managed deployment, filing / domain / SSL / CDN and 7x12 support.</p>',
                'meta_title' => 'Contact Us | FunnLink', 'meta_description' => 'Contact FunnLink for technical advice, partnerships and managed hosting.', 'meta_keywords' => 'FunnLink, contact, support, hosting',
            ],
        ];
    }
}
