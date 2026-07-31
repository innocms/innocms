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
     * Default (official) theme pages: about / get-started / features / contact.
     *
     * @return array[]
     */
    private function getPages(): array
    {
        return [
            ['id' => 1, 'slug' => 'about', 'position' => 1, 'viewed' => 720, 'active' => 1],
            ['id' => 2, 'slug' => 'get-started', 'position' => 2, 'viewed' => 680, 'active' => 1],
            ['id' => 3, 'slug' => 'features', 'position' => 3, 'viewed' => 540, 'active' => 1],
            ['id' => 4, 'slug' => 'contact', 'position' => 4, 'viewed' => 610, 'active' => 1],
        ];
    }

    /**
     * @return array[]
     */
    private function getPageTranslations(): array
    {
        return [
            // ---------- about ----------
            [
                'page_id'    => 1, 'locale' => 'zh-cn', 'title' => '关于 InnoCMS',
                'content'    => '<p><strong>InnoCMS</strong> 是一款专为企业官网快速建站而设计的轻量级内容管理系统（CMS），基于 Laravel 构建，采用模块化的 innopacks 架构与基于 Hook 的插件系统。</p><h3>设计理念</h3><ul><li><strong>轻量</strong>：只保留建站真正需要的功能，安装即用、加载飞快</li><li><strong>可扩展</strong>：通过插件与主题扩展能力，不改动核心代码</li><li><strong>面向 B2B</strong>：默认即可承载品牌官网与外贸独立站的内容结构</li></ul><h3>开源协议</h3><p>InnoCMS 采用 OSL-3.0 开源协议，可免费使用、修改与商用。</p>',
                'meta_title' => '关于 InnoCMS｜轻量级企业官网建站系统', 'meta_description' => 'InnoCMS 是一款专为企业官网快速建站而设计的轻量级 CMS，模块化架构、Hook 插件、主题与多语言内置。', 'meta_keywords' => 'InnoCMS,关于,CMS,开源',
            ],
            [
                'page_id'    => 1, 'locale' => 'en', 'title' => 'About InnoCMS',
                'content'    => '<p><strong>InnoCMS</strong> is a lightweight content management system built for fast corporate websites on Laravel, with a modular innopacks architecture and a hook-based plugin system.</p><h3>Design principles</h3><ul><li><strong>Lightweight</strong>: only what a website truly needs — install and go, fast to load</li><li><strong>Extensible</strong>: grow capability through plugins and themes without touching core code</li><li><strong>B2B-ready</strong>: the default content structure fits brand sites and export landing pages</li></ul><h3>License</h3><p>InnoCMS is released under OSL-3.0 — free to use, modify and use commercially.</p>',
                'meta_title' => 'About InnoCMS | Lightweight Corporate Website Builder', 'meta_description' => 'InnoCMS is a lightweight CMS for fast corporate websites: modular architecture, hook plugins, themes and built-in multilingual.', 'meta_keywords' => 'InnoCMS, about, CMS, open source',
            ],
            // ---------- get-started ----------
            [
                'page_id'    => 2, 'locale' => 'zh-cn', 'title' => '快速开始',
                'content'    => '<p>几步即可在本地或服务器跑起 InnoCMS。</p><h3>环境要求</h3><ul><li>PHP 8.2+、Composer、Node.js</li><li>MySQL 8.0+ / MariaDB 10.6+（或 SQLite）</li></ul><h3>安装</h3><pre>composer install<br>npm install<br>php artisan key:generate<br>php artisan migrate --seed<br>php artisan storage:link<br>npm run dev</pre><h3>下一步</h3><ul><li>访问 <code>/install</code> 完成安装向导，或直接用 seed 的演示数据</li><li>登录后台 <code>/panel</code> 管理内容、切换主题、安装插件</li><li>阅读「开发文档」分类下的主题与插件开发指南</li></ul>',
                'meta_title' => '快速开始｜InnoCMS', 'meta_description' => 'InnoCMS 安装与上手指南：环境要求、安装命令与下一步。', 'meta_keywords' => 'InnoCMS,安装,快速开始,文档',
            ],
            [
                'page_id'    => 2, 'locale' => 'en', 'title' => 'Get Started',
                'content'    => '<p>Get InnoCMS running locally or on a server in a few steps.</p><h3>Requirements</h3><ul><li>PHP 8.2+, Composer, Node.js</li><li>MySQL 8.0+ / MariaDB 10.6+ (or SQLite)</li></ul><h3>Install</h3><pre>composer install<br>npm install<br>php artisan key:generate<br>php artisan migrate --seed<br>php artisan storage:link<br>npm run dev</pre><h3>Next</h3><ul><li>Visit <code>/install</code> for the wizard, or use the seeded demo data directly</li><li>Log in to <code>/panel</code> to manage content, switch themes and install plugins</li><li>Read the theme and plugin guides under the Documentation catalog</li></ul>',
                'meta_title' => 'Get Started | InnoCMS', 'meta_description' => 'InnoCMS install and first-steps guide: requirements, commands and what to do next.', 'meta_keywords' => 'InnoCMS, install, get started, docs',
            ],
            // ---------- features ----------
            [
                'page_id'    => 3, 'locale' => 'zh-cn', 'title' => '产品特性',
                'content'    => '<h3>模块化 innopacks 架构</h3><p>common / panel / front / install / plugin 分包组织，边界清晰，便于维护与独立演进。</p><h3>基于 Hook 的插件系统</h3><p>动作钩子、过滤钩子与 Blade 钩子，零侵入扩展后台菜单、前台区块与业务流程。</p><h3>主题一键切换</h3><p>默认官方主题 + 行业垂直主题，每个主题可自带视图、样式、语言包与演示数据。</p><h3>内容级多语言</h3><p>分类、文章、页面均支持多语言翻译，URL 自动带语言前缀，SEO 友好。</p><h3>为 B2B 而生</h3><p>产品 / 行业 / 资质 / 询盘的内容结构开箱即用，配合垂直主题快速搭建外贸独立站。</p>',
                'meta_title' => '产品特性｜InnoCMS', 'meta_description' => 'InnoCMS 核心特性：模块化架构、Hook 插件、主题系统、内容级多语言、B2B 内容结构。', 'meta_keywords' => 'InnoCMS,特性,插件,主题,多语言',
            ],
            [
                'page_id'    => 3, 'locale' => 'en', 'title' => 'Features',
                'content'    => '<h3>Modular innopacks architecture</h3><p>common / panel / front / install / plugin are organised as separate packages with clear boundaries — easy to maintain and evolve.</p><h3>Hook-based plugin system</h3><p>Action hooks, filter hooks and Blade hooks let you extend the admin menu, frontend blocks and business flow without touching core code.</p><h3>One-click themes</h3><p>An official default theme plus vertical industry themes; each theme can carry its own views, styles, lang packs and demo data.</p><h3>Content-level multilingual</h3><p>Catalogs, articles and pages all support translations, with locale-aware URLs that stay SEO-friendly.</p><h3>Built for B2B</h3><p>A products / industries / certifications / inquiry content structure works out of the box; pair it with a vertical theme to launch an export site fast.</p>',
                'meta_title' => 'Features | InnoCMS', 'meta_description' => 'InnoCMS core features: modular architecture, hook plugins, theme system, content-level i18n, B2B content structure.', 'meta_keywords' => 'InnoCMS, features, plugins, themes, multilingual',
            ],
            // ---------- contact ----------
            [
                'page_id'    => 4, 'locale' => 'zh-cn', 'title' => '联系我们',
                'content'    => '<p>需要技术咨询、合作洽谈或 SaaS 托管服务？欢迎通过以下方式联系我们。</p><h3>联系方式</h3><table class="table table-bordered"><tbody><tr><th>官网</th><td><a href="https://www.innocms.com">www.innocms.com</a></td></tr><tr><th>邮箱</th><td><a href="mailto:team@innoshop.com">team@innoshop.com</a></td></tr><tr><th>GitHub</th><td><a href="https://github.com/innocms/innocms">innocms/innocms</a></td></tr><tr><th>QQ 交流群</th><td>960062283</td></tr></tbody></table><h3>SaaS 托管</h3><p>不想自己动手？我们提供独立云服务器、专人部署、备案/域名/SSL/CDN 全托管与 7×12 小时服务。</p>',
                'meta_title' => '联系我们｜InnoCMS', 'meta_description' => '联系 InnoCMS：技术咨询、合作洽谈与 SaaS 托管服务。', 'meta_keywords' => 'InnoCMS,联系,支持,托管',
            ],
            [
                'page_id'    => 4, 'locale' => 'en', 'title' => 'Contact Us',
                'content'    => '<p>Need technical advice, a partnership or managed hosting? Reach us through the channels below.</p><h3>Channels</h3><table class="table table-bordered"><tbody><tr><th>Website</th><td><a href="https://www.innocms.com">www.innocms.com</a></td></tr><tr><th>Email</th><td><a href="mailto:team@innoshop.com">team@innoshop.com</a></td></tr><tr><th>GitHub</th><td><a href="https://github.com/innocms/innocms">innocms/innocms</a></td></tr><tr><th>QQ group</th><td>960062283</td></tr></tbody></table><h3>Managed hosting</h3><p>Hands-off? We offer dedicated cloud servers, managed deployment, filing / domain / SSL / CDN and 7x12 support.</p>',
                'meta_title' => 'Contact Us | InnoCMS', 'meta_description' => 'Contact InnoCMS for technical advice, partnerships and managed hosting.', 'meta_keywords' => 'InnoCMS, contact, support, hosting',
            ],
        ];
    }
}
