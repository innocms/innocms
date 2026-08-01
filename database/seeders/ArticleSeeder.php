<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Database\Seeders;

use InnoCMS\Common\Models\Article;
use InnoCMS\Common\Models\Article\Translation;
use InnoCMS\Common\Models\ArticleTag;

class ArticleSeeder extends BaseSeeder
{
    public function run(): void
    {
        $items = $this->getArticles();
        if ($items) {
            $this->safeTruncate(Article::class);
            foreach ($items as $item) {
                Article::query()->create($item);
            }
        }

        $items = $this->getArticleTranslations();
        if ($items) {
            $this->safeTruncate(Translation::class);
            foreach ($items as $item) {
                Translation::query()->create($item);
            }
        }

        $items = $this->getArticleTags();
        if ($items) {
            $this->safeTruncate(ArticleTag::class);
            foreach ($items as $item) {
                ArticleTag::query()->create($item);
            }
        }
    }

    /**
     * @return array[]
     */
    private function getArticles(): array
    {
        $articles = [];
        foreach ($this->data() as $row) {
            $articles[] = [
                'id'         => $row['id'],
                'catalog_id' => $row['catalog_id'],
                'slug'       => $row['slug'],
                'position'   => $row['id'],
                'viewed'     => $row['viewed'],
                'author'     => 'InnoCMS',
                'active'     => 1,
            ];
        }

        return $articles;
    }

    /**
     * @return array[]
     */
    private function getArticleTranslations(): array
    {
        $translations = [];
        foreach ($this->data() as $row) {
            foreach (['zh-cn' => 'zh', 'en' => 'en'] as $locale => $key) {
                $def            = $row[$key];
                $translations[] = [
                    'article_id'       => $row['id'],
                    'locale'           => $locale,
                    'title'            => $def['title'],
                    'summary'          => $def['summary'],
                    'image'            => $row['image'],
                    'content'          => $def['content'],
                    'meta_title'       => $locale === 'zh-cn' ? $def['title'].'｜InnoCMS' : $def['title'].' | InnoCMS',
                    'meta_description' => $def['summary'],
                    'meta_keywords'    => $locale === 'zh-cn' ? 'InnoCMS,CMS,企业官网,开源' : 'InnoCMS, CMS, open source, website builder',
                ];
            }
        }

        return $translations;
    }

    /**
     * @return array[]
     */
    private function getArticleTags(): array
    {
        $links = [];
        $id    = 1;
        foreach ($this->data() as $row) {
            foreach ($row['tags'] as $tagId) {
                $links[] = ['id' => $id++, 'article_id' => $row['id'], 'tag_id' => $tagId];
            }
        }

        return $links;
    }

    /**
     * InnoCMS product-promo sample content for the default (official) theme.
     * Article images reference bundled demo images under public/images/demo/news/.
     *
     * @return array[]
     */
    private function data(): array
    {
        $pic = static fn (string $name): string => 'images/demo/news/'.$name.'.webp';

        return [
            [
                'id' => 1, 'catalog_id' => 3, 'slug' => 'innocms-v1-released', 'image' => $pic('innocms-v1-released'), 'viewed' => 612, 'tags' => [2, 1],
                'zh' => ['title' => 'InnoCMS v1.0 正式发布：为 B2B 企业官网而生的轻量 CMS', 'summary' => '模块化 innopacks 架构、Hook 插件系统、主题一键切换、内置多语言——InnoCMS v1.0 让企业官网从安装到上线只需几分钟。', 'content' => '<p>InnoCMS v1.0 正式发布。它是一款专为企业官网快速建站而设计的轻量级内容管理系统，基于 Laravel 构建，面向 B2B 品牌展示与外贸独立站场景。</p><h3>核心特性</h3><ul><li><strong>模块化架构</strong>：innopacks 将 common / panel / front / install / plugin 拆分为独立包，清晰可维护</li><li><strong>Hook 插件系统</strong>：通过 <code>listen_hook_action</code> / <code>listen_blade_insert</code> 等函数零侵入扩展</li><li><strong>主题系统</strong>：默认官方主题 + 行业垂直主题，后台一键切换</li><li><strong>内置多语言</strong>：内容级中英双语，URL 自动带语言前缀</li></ul><p>从安装到上线只需几分钟，帮助团队把精力放在内容本身，而不是建站的技术细节。</p>'],
                'en' => ['title' => 'InnoCMS v1.0 Released: A Lightweight CMS Built for B2B Corporate Sites', 'summary' => 'Modular innopacks architecture, a hook-based plugin system, one-click themes and built-in multilingual content — go from install to launch in minutes.', 'content' => '<p>InnoCMS v1.0 is officially released. It is a lightweight CMS purpose-built for fast corporate websites on Laravel, targeting B2B brand showcases and export landing sites.</p><h3>Highlights</h3><ul><li><strong>Modular architecture</strong>: innopacks splits common / panel / front / install / plugin into independent packages</li><li><strong>Hook-based plugins</strong>: extend anything with <code>listen_hook_action</code> / <code>listen_blade_insert</code> and friends</li><li><strong>Theme system</strong>: an official default theme plus vertical industry themes, switchable in the admin</li><li><strong>Built-in multilingual</strong>: content-level zh/en with locale-aware URLs</li></ul><p>From install to launch in minutes, so your team focuses on content instead of plumbing.</p>'],
            ],
            [
                'id' => 2, 'catalog_id' => 2, 'slug' => 'plugin-development-quickstart', 'image' => $pic('plugin-development'), 'viewed' => 438, 'tags' => [3, 1],
                'zh' => ['title' => '插件开发入门：用 Hook 给 InnoCMS 加一个功能', 'summary' => '一个插件只需要 config.json 与 Boot.php。本篇带你 10 分钟写出第一个插件：加侧栏菜单、注入页脚内容、注册后台路由。', 'content' => '<p>InnoCMS 的扩展能力几乎全部来自 Hook 插件系统。一个最小插件只需要两个文件：<code>config.json</code> 与 <code>Boot.php</code>。</p><h3>目录结构</h3><pre>plugins/MyPlugin/<br>├── config.json<br>├── Boot.php<br>├── Routes/panel.php<br>└── Views/</pre><h3>三步加一个功能</h3><ul><li>在 <code>init()</code> 里用 <code>listen_hook_filter(\'component.sidebar.plugin.routes\', ...)</code> 加侧栏入口</li><li>用 <code>listen_blade_insert(\'layouts.footer.top\', ...)</code> 向页脚注入内容</li><li>在 <code>Routes/panel.php</code> 注册后台路由与控制器</li></ul><p>无需修改任何核心文件，卸载插件即恢复原状。</p>'],
                'en' => ['title' => 'Plugin Quickstart: Add a Feature to InnoCMS with Hooks', 'summary' => 'A plugin only needs config.json and Boot.php. In 10 minutes you will add a sidebar item, inject footer content and register a panel route.', 'content' => '<p>Almost all of InnoCMS\'s extensibility comes from its hook-based plugin system. A minimal plugin needs just two files: <code>config.json</code> and <code>Boot.php</code>.</p><h3>Layout</h3><pre>plugins/MyPlugin/<br>├── config.json<br>├── Boot.php<br>├── Routes/panel.php<br>└── Views/</pre><h3>Three steps to a feature</h3><ul><li>In <code>init()</code>, call <code>listen_hook_filter(\'component.sidebar.plugin.routes\', ...)</code> to add a sidebar entry</li><li>Use <code>listen_blade_insert(\'layouts.footer.top\', ...)</code> to inject footer content</li><li>Register panel routes and controllers in <code>Routes/panel.php</code></li></ul><p>No core files are touched, and removing the plugin restores the original behaviour.</p>'],
            ],
            [
                'id' => 3, 'catalog_id' => 2, 'slug' => 'theme-development-guide', 'image' => $pic('theme-development'), 'viewed' => 356, 'tags' => [4, 1],
                'zh' => ['title' => '主题开发指南：从目录结构到模板编写', 'summary' => '主题位于 themes/ 目录，是一个自带视图、样式、语言包与演示数据的独立文件夹。本篇讲清必备模板与资源编译。', 'content' => '<p>InnoCMS 主题位于 <code>themes/</code> 目录，每个主题是一个自包含文件夹，可随产品发布、也可单独分发。</p><h3>目录结构</h3><ul><li><code>config.json</code> — 主题元信息（code、name、version）</li><li><code>views/</code> — Blade 模板，按同名覆盖包视图</li><li><code>assets/scss</code> + <code>public/css</code> — 样式源码与编译产物</li><li><code>lang/</code> — 主题语言包</li><li><code>demo/Seeder.php</code> — 一键导入的演示数据</li></ul><h3>必备模板</h3><ul><li><code>layouts/app.blade.php</code>、<code>layouts/header.blade.php</code>、<code>layouts/footer.blade.php</code></li><li><code>home.blade.php</code></li></ul><p>未提供的模板（如文章详情）会自动回落到默认主题，因此主题只需覆盖它想定制的部分。</p>'],
                'en' => ['title' => 'Theme Development Guide: From Structure to Templates', 'summary' => 'A theme lives under themes/ as a self-contained folder with views, styles, lang packs and demo data. Here are the required templates and the asset build.', 'content' => '<p>InnoCMS themes live in the <code>themes/</code> directory. Each theme is a self-contained folder that can ship with a release or be distributed on its own.</p><h3>Structure</h3><ul><li><code>config.json</code> — theme metadata (code, name, version)</li><li><code>views/</code> — Blade templates that override package views by name</li><li><code>assets/scss</code> + <code>public/css</code> — style sources and compiled output</li><li><code>lang/</code> — theme translations</li><li><code>demo/Seeder.php</code> — one-click demo data</li></ul><h3>Required templates</h3><ul><li><code>layouts/app.blade.php</code>, <code>layouts/header.blade.php</code>, <code>layouts/footer.blade.php</code></li><li><code>home.blade.php</code></li></ul><p>Any template you omit (e.g. the article detail) falls back to the default theme, so a theme only overrides what it wants to customise.</p>'],
            ],
            [
                'id' => 4, 'catalog_id' => 2, 'slug' => 'multilingual-content-setup', 'image' => $pic('multilingual-content'), 'viewed' => 289, 'tags' => [5, 1],
                'zh' => ['title' => '多语言内容配置：让官网覆盖全球市场', 'summary' => 'InnoCMS 的内容级多语言：每个分类、文章、页面都有独立翻译，前台按 URL 前缀切换，SEO 友好。', 'content' => '<p>对外贸独立站而言，多语言不是可选项。InnoCMS 在内容层面内置多语言：每个分类、文章、页面都保存各自语言的标题、正文与 SEO 字段。</p><h3>工作机制</h3><ul><li>后台为每条内容填写各语言翻译</li><li>前台 URL 自动带语言前缀，如 <code>/en/articles</code></li><li>语言切换器一键跳转对应译文，搜索引擎可独立索引各语言版本</li></ul><p>默认启用简体中文与英文，可在后台按需增删语言。</p>'],
                'en' => ['title' => 'Multilingual Content: Reach Global Markets', 'summary' => 'Content-level i18n in InnoCMS: every catalog, article and page carries its own translations, switched by URL prefix and SEO-friendly.', 'content' => '<p>For an export site, multilingual is not optional. InnoCMS provides content-level i18n: each catalog, article and page stores per-language titles, body copy and SEO fields.</p><h3>How it works</h3><ul><li>Fill in translations per language in the admin</li><li>Frontend URLs carry a locale prefix automatically, e.g. <code>/en/articles</code></li><li>The language switcher jumps to the matching translation; each locale is indexable on its own</li></ul><p>Simplified Chinese and English are enabled by default; add or remove languages in the admin as needed.</p>'],
            ],
            [
                'id' => 5, 'catalog_id' => 3, 'slug' => 'saas-managed-hosting', 'image' => $pic('saas-hosting'), 'viewed' => 374, 'tags' => [2],
                'zh' => ['title' => '不想自己动手？InnoCMS 提供一站式 SaaS 托管', 'summary' => '独立云服务器、专人部署、备案/域名/SSL/CDN 全托管，7×12 小时服务，让你专注业务而非运维。', 'content' => '<p>如果你不懂技术或不想折腾服务器，InnoCMS 提供一站式托管服务。</p><h3>托管包含</h3><ul><li>独立云服务器，资源隔离</li><li>专人部署，全程无忧</li><li>备案、域名解析、SSL、CDN 一并处理</li><li>7×12 小时运维响应</li><li>可按需定制功能开发</li></ul><p>从建站到上线，专业的事交给专业的人。</p>'],
                'en' => ['title' => 'Hands-off? InnoCMS Offers Turnkey SaaS Hosting', 'summary' => 'Dedicated cloud servers, managed deployment, filing, domain, SSL and CDN handled for you, with 7x12 support — focus on your business, not ops.', 'content' => '<p>If you are not technical or simply do not want to babysit a server, InnoCMS offers fully managed hosting.</p><h3>What is included</h3><ul><li>Dedicated cloud server, isolated resources</li><li>Managed deployment, end to end</li><li>Filing, DNS, SSL and CDN all handled</li><li>7x12 operations support</li><li>Custom feature development on request</li></ul><p>From build to launch, leave the plumbing to the experts.</p>'],
            ],
            [
                'id' => 6, 'catalog_id' => 3, 'slug' => 'innocms-partner-ecosystem', 'image' => $pic('partner-ecosystem'), 'viewed' => 245, 'tags' => [1],
                'zh' => ['title' => 'InnoCMS 生态共建：与开发者和服务商一起成长', 'summary' => '主题市场、插件市场与托管服务构成 InnoCMS 生态。我们欢迎开发者提交主题与插件，共享收益。', 'content' => '<p>InnoCMS 不只是一个产品，更是一个生态。主题市场、插件市场与托管服务，让开发者、服务商与企业客户各取所需。</p><h3>共建方式</h3><ul><li>开发者提交主题与插件，进入官方市场</li><li>服务商提供建站、托管与定制服务</li><li>企业客户按需选购，快速上线</li></ul><p>开源协议 OSL-3.0，欢迎 Fork、贡献与商用。</p>'],
                'en' => ['title' => 'Building the InnoCMS Ecosystem Together', 'summary' => 'A theme marketplace, a plugin marketplace and managed hosting form the InnoCMS ecosystem. Developers are welcome to ship themes and plugins and share the upside.', 'content' => '<p>InnoCMS is more than a product — it is an ecosystem. A theme marketplace, a plugin marketplace and managed hosting let developers, agencies and businesses each get what they need.</p><h3>Ways to contribute</h3><ul><li>Developers publish themes and plugins to the official marketplace</li><li>Agencies offer build, hosting and customisation services</li><li>Businesses pick what they need and launch fast</li></ul><p>Released under OSL-3.0 — fork, contribute and use commercially.</p>'],
            ],
            [
                'id' => 7, 'catalog_id' => 4, 'slug' => 'solution-b2b-export-site', 'image' => $pic('b2b-export-site'), 'viewed' => 318, 'tags' => [6, 2],
                'zh' => ['title' => '方案：用 InnoCMS 搭建 B2B 外贸独立站', 'summary' => '产品中心、行业应用、资质认证、询盘入口——B2B 独立站的标准结构，InnoCMS 用分类 + 文章 + 页面即可搭好。', 'content' => '<p>B2B 外贸独立站有一套被验证过的信息架构：产品中心、行业应用、资质与质量、OEM/ODM 流程、新闻与联系询盘。</p><h3>用 InnoCMS 落地</h3><ul><li><strong>产品中心</strong>：以分类组织产品系列，每个产品即一篇文章，带规格表与图片</li><li><strong>行业应用</strong>：按行业建子分类，呈现合规与案例</li><li><strong>页面</strong>：关于我们、制造能力、质量保证、联系我们等单页</li><li><strong>询盘</strong>：联系页 + 顶栏询价按钮，引导转化</li></ul><p>配合精密制造等垂直主题，安装即可得到一个完整的 B2B 样板站。</p>'],
                'en' => ['title' => 'Solution: Build a B2B Export Site with InnoCMS', 'summary' => 'Products, industries, certifications, inquiry entry points — the standard B2B structure maps cleanly onto InnoCMS catalogs, articles and pages.', 'content' => '<p>A B2B export site follows a proven information architecture: products, industry applications, quality and certifications, OEM/ODM process, news and a contact/inquiry flow.</p><h3>Mapping it to InnoCMS</h3><ul><li><strong>Products</strong>: organise series with catalogs; each product is an article with a spec table and images</li><li><strong>Industries</strong>: child catalogs per vertical, showing compliance and use cases</li><li><strong>Pages</strong>: About, Capabilities, Quality, Contact as static pages</li><li><strong>Inquiries</strong>: a contact page plus a header quote button to drive conversion</li></ul><p>Paired with a vertical theme such as Precision Manufacturing, you get a complete B2B sample site on install.</p>'],
            ],
            [
                'id' => 8, 'catalog_id' => 4, 'slug' => 'solution-brand-corporate-site', 'image' => $pic('brand-corporate-site'), 'viewed' => 271, 'tags' => [6, 4],
                'zh' => ['title' => '方案：用默认主题快速上线品牌企业官网', 'summary' => '不需要垂直行业内容？默认官方主题提供干净专业的 SaaS 风落地页，适合品牌展示与企业门户。', 'content' => '<p>并非每个企业都需要行业垂直内容。对品牌展示与企业门户而言，默认官方主题提供了干净、专业的落地页结构。</p><h3>包含的板块</h3><ul><li>Hero 主视觉 + 价值主张</li><li>核心特性三/四宫格</li><li>解决方案与资源预览</li><li>行动号召 + 联系方式</li></ul><p>所有内容均可在后台直接编辑，或通过主题进一步定制视觉。</p>'],
                'en' => ['title' => 'Solution: Launch a Brand Corporate Site with the Default Theme', 'summary' => 'No vertical content needed? The default official theme ships a clean, professional SaaS-style landing page for brand showcases and corporate portals.', 'content' => '<p>Not every business needs vertical industry content. For brand showcases and corporate portals, the default official theme provides a clean, professional landing structure.</p><h3>Sections included</h3><ul><li>Hero with value proposition</li><li>Feature grid</li><li>Solutions and resources preview</li><li>Call-to-action and contact</li></ul><p>Everything is editable in the admin, or restyle it further through a theme.</p>'],
            ],
        ];
    }
}
