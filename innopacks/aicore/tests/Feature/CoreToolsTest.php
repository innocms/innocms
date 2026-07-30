<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Aicore\Tests\Feature;

use Database\Seeders\LocaleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InnoCMS\Aicore\Tools\ArticleDetailTool;
use InnoCMS\Aicore\Tools\ArticleListTool;
use InnoCMS\Aicore\Tools\CatalogListTool;
use InnoCMS\Aicore\Tools\LocaleListTool;
use InnoCMS\Aicore\Tools\PageListTool;
use InnoCMS\Aicore\Tools\TagListTool;
use InnoCMS\Common\Models\Article;
use InnoCMS\Common\Models\Catalog;
use InnoCMS\Common\Models\Locale;
use InnoCMS\Common\Models\Page;
use InnoCMS\Common\Models\Tag;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CoreToolsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(LocaleSeeder::class);
    }

    private function createCatalog(string $title = 'News'): Catalog
    {
        $catalog = Catalog::query()->create(['slug' => 'news', 'position' => 0, 'active' => true]);
        $catalog->translations()->create(['locale' => locale_code(), 'title' => $title]);

        return $catalog;
    }

    private function createArticle(string $title, ?Catalog $catalog = null): Article
    {
        $article = Article::query()->create([
            'catalog_id' => $catalog?->id ?? 0,
            'slug'       => strtolower(str_replace(' ', '-', $title)),
            'position'   => 0,
            'viewed'     => 0,
            'active'     => true,
        ]);
        $article->translations()->create([
            'locale'  => locale_code(),
            'title'   => $title,
            'summary' => 'Summary of '.$title,
            'content' => 'Content of '.$title,
        ]);

        return $article->refresh();
    }

    #[Test]
    public function test_article_list_returns_articles(): void
    {
        $catalog = $this->createCatalog();
        $this->createArticle('Unique MCP Keyword Article', $catalog);

        $result = (new ArticleListTool)->execute(['keyword' => 'Unique MCP Keyword']);

        $this->assertGreaterThanOrEqual(1, $result['total']);
        $this->assertSame('Unique MCP Keyword Article', $result['items'][0]['title']);
        $this->assertSame('News', $result['items'][0]['catalog_name']);
    }

    #[Test]
    public function test_article_list_active_filter(): void
    {
        $article = $this->createArticle('Inactive Filter Article');
        $article->update(['active' => false]);

        $inactive = (new ArticleListTool)->execute(['keyword' => 'Inactive Filter', 'active' => false]);
        $this->assertGreaterThanOrEqual(1, $inactive['total']);

        $active = (new ArticleListTool)->execute(['keyword' => 'Inactive Filter', 'active' => true]);
        $this->assertSame(0, $active['total']);
    }

    #[Test]
    public function test_article_detail_returns_content(): void
    {
        $article = $this->createArticle('Detail Article', $this->createCatalog());

        $result = (new ArticleDetailTool)->execute(['id' => $article->id]);

        $this->assertSame('Detail Article', $result['title']);
        $this->assertSame('Content of Detail Article', $result['content']);
        $this->assertSame('News', $result['catalog_name']);
    }

    #[Test]
    public function test_article_detail_throws_for_missing_id(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new ArticleDetailTool)->execute(['id' => 999999]);
    }

    #[Test]
    public function test_catalog_list_returns_catalogs(): void
    {
        $this->createCatalog('Company News');

        $result = (new CatalogListTool)->execute(['keyword' => 'Company']);

        $this->assertGreaterThanOrEqual(1, $result['total']);
        $this->assertSame('Company News', $result['items'][0]['title']);
    }

    #[Test]
    public function test_page_list_returns_pages(): void
    {
        $page = Page::query()->create(['slug' => 'about-us', 'viewed' => 0, 'active' => true]);
        $page->translations()->create(['locale' => locale_code(), 'title' => 'About Us', 'content' => 'About content']);

        $result = (new PageListTool)->execute(['keyword' => 'about-us']);

        $this->assertGreaterThanOrEqual(1, $result['total']);
        $this->assertSame('About Us', $result['items'][0]['title']);
    }

    #[Test]
    public function test_tag_list_returns_tags(): void
    {
        $tag = Tag::query()->create(['slug' => 'laravel', 'position' => 0, 'active' => true]);
        $tag->translations()->create(['locale' => locale_code(), 'name' => 'Laravel']);

        $result = (new TagListTool)->execute(['keyword' => 'Laravel']);

        $this->assertGreaterThanOrEqual(1, $result['total']);
        $this->assertSame('Laravel', $result['items'][0]['name']);
    }

    #[Test]
    public function test_locale_list_returns_locales(): void
    {
        $result = (new LocaleListTool)->execute([]);

        $this->assertSame(Locale::query()->count(), $result['total']);
        $this->assertNotEmpty($result['items'][0]['code']);
    }

    #[Test]
    public function test_tools_have_permission_and_schema(): void
    {
        $tools = [
            new ArticleListTool, new ArticleDetailTool,
            new CatalogListTool, new PageListTool,
            new TagListTool, new LocaleListTool,
        ];

        foreach ($tools as $tool) {
            $this->assertNotEmpty($tool->name());
            $this->assertNotEmpty($tool->description());
            $this->assertNotEmpty($tool->requiredPermission(), $tool->name().' must require a permission');
            $this->assertSame('object', $tool->inputSchema()['type']);
        }
    }
}
