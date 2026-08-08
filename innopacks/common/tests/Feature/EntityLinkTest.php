<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Common\Tests\Feature;

use Database\Seeders\LocaleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InnoCMS\Common\Models\Article;
use InnoCMS\Common\Models\Catalog;
use InnoCMS\Common\Models\Category;
use InnoCMS\Common\Models\Page;
use InnoCMS\Common\Models\Product;
use InnoCMS\Common\Repositories\ArticleRepo;
use InnoCMS\Common\Repositories\CatalogRepo;
use InnoCMS\Common\Repositories\CategoryRepo;
use InnoCMS\Common\Repositories\PageRepo;
use InnoCMS\Common\Repositories\ProductRepo;
use InnoCMS\Common\Resources\ArticleName;
use InnoCMS\Common\Resources\CatalogName;
use InnoCMS\Common\Resources\CategoryName;
use InnoCMS\Common\Resources\PageName;
use InnoCMS\Common\Resources\ProductName;
use InnoCMS\Common\Support\EntityLinkEnricher;
use InnoCMS\Common\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class EntityLinkTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(LocaleSeeder::class);
    }

    private function createArticle(string $title, string $image = ''): Article
    {
        $article = Article::query()->create([
            'catalog_id' => 0,
            'slug'       => strtolower(str_replace(' ', '-', $title)),
            'image'      => $image,
            'position'   => 0,
            'viewed'     => 0,
            'active'     => true,
        ]);
        $article->translations()->create([
            'locale'  => locale_code(),
            'title'   => $title,
            'summary' => '',
            'content' => '',
        ]);

        return $article->refresh();
    }

    private function createPage(string $title): Page
    {
        $page = Page::query()->create(['slug' => strtolower(str_replace(' ', '-', $title)), 'viewed' => 0, 'active' => true]);
        $page->translations()->create(['locale' => locale_code(), 'title' => $title, 'content' => '']);

        return $page->refresh();
    }

    private function createCatalog(string $title): Catalog
    {
        $catalog = Catalog::query()->create(['slug' => strtolower(str_replace(' ', '-', $title)), 'position' => 0, 'active' => true]);
        $catalog->translations()->create(['locale' => locale_code(), 'title' => $title]);

        return $catalog->refresh();
    }

    private function createProduct(string $name, string $image = '', $price = '9.99'): Product
    {
        $product = Product::query()->create([
            'slug'     => strtolower(str_replace(' ', '-', $name)),
            'images'   => $image ? [$image] : [],
            'price'    => $price,
            'spu_code' => 'SPU-'.$name,
            'position' => 0,
            'viewed'   => 0,
            'active'   => true,
        ]);
        $product->translations()->create(['locale' => locale_code(), 'name' => $name]);

        return $product->refresh();
    }

    private function createCategory(string $name, string $image = ''): Category
    {
        $category = Category::query()->create([
            'parent_id' => 0,
            'slug'      => strtolower(str_replace(' ', '-', $name)),
            'image'     => $image,
            'position'  => 0,
            'active'    => true,
        ]);
        $category->translations()->create(['locale' => locale_code(), 'name' => $name]);

        return $category->refresh();
    }

    #[Test]
    public function test_article_repo_autocomplete_matches_by_title(): void
    {
        $this->createArticle('Laravel Tips');
        $this->createArticle('Vue News');

        $result = ArticleRepo::getInstance()->autocomplete('Laravel');

        $this->assertCount(1, $result);
        $this->assertSame('Laravel Tips', $result->first()->fallbackName('title'));
    }

    #[Test]
    public function test_page_repo_autocomplete_matches_by_title(): void
    {
        $this->createPage('About Us');
        $this->createPage('Contact');

        $result = PageRepo::getInstance()->autocomplete('about');

        $this->assertCount(1, $result);
        $this->assertSame('About Us', $result->first()->fallbackName('title'));
    }

    #[Test]
    public function test_catalog_repo_autocomplete_matches_by_title(): void
    {
        $this->createCatalog('News');
        $this->createCatalog('Devs');

        $result = CatalogRepo::getInstance()->autocomplete('news');

        $this->assertCount(1, $result);
        $this->assertSame('News', $result->first()->fallbackName('title'));
    }

    #[Test]
    public function test_product_and_category_repos_have_autocomplete(): void
    {
        $this->createProduct('Phone X');
        $this->createCategory('Gadgets');

        $products = ProductRepo::getInstance()->autocomplete('Phone');
        $this->assertCount(1, $products);

        $categories = CategoryRepo::getInstance()->autocomplete('Gadgets');
        $this->assertCount(1, $categories);
    }

    #[Test]
    public function test_name_resources_return_expected_shape(): void
    {
        $article  = $this->createArticle('Article Title', 'foo.jpg');
        $page     = $this->createPage('Page Title');
        $catalog  = $this->createCatalog('Catalog Title');
        $product  = $this->createProduct('Product Name', 'bar.jpg', '12.34');
        $category = $this->createCategory('Category Name', 'cat.jpg');

        $articleArr = (new ArticleName($article))->toArray(request());
        $this->assertSame('Article Title', $articleArr['name']);
        $this->assertNotEmpty($articleArr['image']);
        $this->assertSame($article->slug, $articleArr['slug']);

        $pageArr = (new PageName($page))->toArray(request());
        $this->assertSame('Page Title', $pageArr['name']);
        $this->assertSame($page->slug, $pageArr['slug']);

        $catalogArr = (new CatalogName($catalog))->toArray(request());
        $this->assertSame('Catalog Title', $catalogArr['name']);

        $productArr = (new ProductName($product))->toArray(request());
        $this->assertSame('Product Name', $productArr['name']);
        $this->assertSame('12.34', $productArr['price_label']);
        $this->assertSame('SPU-Product Name', $productArr['sku_code']);
        $this->assertNotEmpty($productArr['image']);

        $categoryArr = (new CategoryName($category))->toArray(request());
        $this->assertSame('Category Name', $categoryArr['name']);
    }

    #[Test]
    public function test_entity_link_helpers_normalize_stored_values(): void
    {
        // Plain URL string -> custom
        $row = entity_link_normalize('https://example.com/path');
        $this->assertSame('custom', $row['type']);
        $this->assertSame('https://example.com/path', $row['link']);

        // JSON object
        $json = json_encode(['type' => 'page', 'value' => '5', 'entity_label' => '', 'link' => '', 'entity_image' => '', 'entity_price' => '']);
        $row  = entity_link_normalize($json);
        $this->assertSame('page', $row['type']);
        $this->assertSame('5', $row['value']);

        // Empty value
        $row = entity_link_normalize(null);
        $this->assertSame('page', $row['type']);
        $this->assertSame('', $row['value']);
    }

    #[Test]
    public function test_entity_link_enricher_fills_page_article_catalog(): void
    {
        $page    = $this->createPage('Page Title');
        $article = $this->createArticle('Article Title', 'a.jpg');
        $catalog = $this->createCatalog('Catalog Title');

        $row = entity_link_enrich(['type' => 'page', 'value' => (string) $page->id, 'entity_label' => '', 'link' => '', 'entity_image' => '', 'entity_price' => '']);
        $this->assertSame('Page Title', $row['entity_label']);

        $row = entity_link_enrich(['type' => 'article', 'value' => (string) $article->id, 'entity_label' => '', 'link' => '', 'entity_image' => '', 'entity_price' => '']);
        $this->assertSame('Article Title', $row['entity_label']);
        $this->assertNotEmpty($row['entity_image']);

        $row = entity_link_enrich(['type' => 'catalog', 'value' => (string) $catalog->id, 'entity_label' => '', 'link' => '', 'entity_image' => '', 'entity_price' => '']);
        $this->assertSame('Catalog Title', $row['entity_label']);
    }

    #[Test]
    public function test_entity_link_enricher_fills_product_and_category(): void
    {
        $product  = $this->createProduct('Phone', 'p.jpg', '12.34');
        $category = $this->createCategory('Gadgets', 'c.jpg');

        $row = entity_link_enrich(['type' => 'product', 'value' => (string) $product->id, 'entity_label' => '', 'link' => '', 'entity_image' => '', 'entity_price' => '']);
        $this->assertSame('Phone', $row['entity_label']);
        $this->assertNotEmpty($row['entity_image']);
        $this->assertSame('12.34', $row['entity_price']);

        $row = entity_link_enrich(['type' => 'category', 'value' => (string) $category->id, 'entity_label' => '', 'link' => '', 'entity_image' => '', 'entity_price' => '']);
        $this->assertSame('Gadgets', $row['entity_label']);
        $this->assertNotEmpty($row['entity_image']);
    }

    #[Test]
    public function test_entity_link_url_resolves_to_storefront_or_hash(): void
    {
        $page = $this->createPage('About');

        $url = entity_link_url(json_encode(['type' => 'page', 'value' => (string) $page->id, 'entity_label' => '', 'link' => '', 'entity_image' => '', 'entity_price' => '']));
        $this->assertNotSame('#', $url);

        // Custom URL wins over type
        $url = entity_link_url(json_encode(['type' => 'page', 'value' => '99999', 'entity_label' => '', 'link' => 'https://custom.example.com', 'entity_image' => '', 'entity_price' => '']));
        $this->assertSame('https://custom.example.com', $url);

        // Plain string stays as-is
        $this->assertSame('https://example.com', entity_link_url('https://example.com'));

        // Product without slug falls back to the ID-based products.show route
        $product = $this->createProduct('Phone');
        // createProduct sets slug; remove it to exercise the ID fallback
        $product->update(['slug' => null]);
        $url = entity_link_url(json_encode(['type' => 'product', 'value' => (string) $product->id, 'entity_label' => '', 'link' => '', 'entity_image' => '', 'entity_price' => '']));
        $this->assertStringContainsString('products/'.$product->id, $url);
    }

    #[Test]
    public function test_category_entity_link_url_does_not_throw(): void
    {
        // Storefront category page resolves via Category::getUrlAttribute() without
        // throwing, regardless of whether front routes are loaded in the test env.
        $category = $this->createCategory('Gadgets');
        $url      = entity_link_url(json_encode(['type' => 'category', 'value' => (string) $category->id, 'entity_label' => '', 'link' => '', 'entity_image' => '', 'entity_price' => '']));
        $this->assertIsString($url);
    }

    #[Test]
    public function test_category_url_falls_back_to_id_route_when_slug_missing(): void
    {
        // A persisted slug-less category resolves via the id-based categories.show
        // route (enterprise pattern — no '#' fallback).
        $category = $this->createCategory('No Slug');
        $category->update(['slug' => null]);

        $url = $category->fresh()->url;

        $this->assertStringContainsString('categories/'.$category->id, $url);
    }

    #[Test]
    public function test_category_url_uses_category_slug_route(): void
    {
        // Category storefront uses the categories.slug_show route (/category-<slug>),
        // aligned with enterprise Category::getUrlAttribute() — never /products?cat=.
        $category = $this->createCategory('Three Dee Wall');

        $url = $category->url;

        $this->assertStringContainsString('category-three-dee-wall', $url);
        $this->assertStringNotContainsString('products?cat=', $url);
    }

    #[Test]
    public function test_panel_link_parse_round_trip(): void
    {
        $page = $this->createPage('About');

        $parsed = panel_link_parse(json_encode(['type' => 'page', 'value' => (string) $page->id, 'entity_label' => '', 'link' => '', 'entity_image' => '', 'entity_price' => '']));
        $this->assertSame('page', $parsed['type']);
        $this->assertSame((string) $page->id, $parsed['value']);
        $this->assertSame('About', $parsed['entity_label']);

        // Plain URL stays custom
        $parsed = panel_link_parse('https://example.com');
        $this->assertSame('custom', $parsed['type']);
    }

    #[Test]
    public function test_enricher_resolve_by_id_or_slug(): void
    {
        $article = $this->createArticle('Slug Lookup');

        $byId   = EntityLinkEnricher::resolveByIdOrSlug(Article::class, (string) $article->id);
        $bySlug = EntityLinkEnricher::resolveByIdOrSlug(Article::class, $article->slug);

        $this->assertNotNull($byId);
        $this->assertNotNull($bySlug);
        $this->assertSame($article->id, $bySlug->id);
    }

    #[Test]
    public function test_catalog_repo_drops_empty_title_translation_rows(): void
    {
        // Reproduces the "Column 'title' cannot be null" bug: an optional locale
        // left blank by the editor is dropped instead of inserted with a null title.
        $catalog = CatalogRepo::getInstance()->create([
            'parent_id'    => 0,
            'slug'         => 'test-catalog-'.uniqid(),
            'position'     => 0,
            'active'       => true,
            'translations' => [
                ['locale' => 'en', 'title' => null, 'summary' => null, 'meta_title' => '', 'meta_keywords' => '', 'meta_description' => ''],
                ['locale' => 'zh-cn', 'title' => '测试分类', 'summary' => '', 'meta_title' => '', 'meta_keywords' => '', 'meta_description' => ''],
            ],
        ]);

        $locales = $catalog->translations->pluck('locale')->all();
        $this->assertNotContains('en', $locales);
        $this->assertContains('zh-cn', $locales);
        $this->assertSame('测试分类', $catalog->translate('zh-cn', 'title'));
    }

    #[Test]
    public function test_catalog_repo_update_without_translations_keeps_existing_rows(): void
    {
        // update() without a translations key must NOT wipe existing rows (regression
        // guard for the unguarded delete-then-rebuild that silently destroyed data).
        $catalog = CatalogRepo::getInstance()->create([
            'parent_id'    => 0,
            'slug'         => 'test-keep-'.uniqid(),
            'position'     => 0,
            'active'       => true,
            'translations' => [
                ['locale' => 'zh-cn', 'title' => '保留', 'summary' => '', 'meta_title' => '', 'meta_keywords' => '', 'meta_description' => ''],
            ],
        ]);

        CatalogRepo::getInstance()->update($catalog, [
            'parent_id' => 0,
            'slug'      => $catalog->slug,
            'position'  => 0,
            'active'    => true,
        ]);

        $this->assertCount(1, $catalog->fresh()->translations);
        $this->assertSame('保留', $catalog->fresh()->translate('zh-cn', 'title'));
    }
}
