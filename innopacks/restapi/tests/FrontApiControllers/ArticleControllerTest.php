<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Restapi\Tests\FrontApiControllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use InnoCMS\Common\Models\Article;
use InnoCMS\Restapi\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * The front article API used to point at an empty controller, so /api/articles answered
 * 500. It may only expose published articles.
 */
class ArticleControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The locale the articles are written in: the one the application resolves to, so the
     * translation relation of the articles finds them.
     */
    private const LOCALE = 'zh-cn';

    protected function setUp(): void
    {
        parent::setUp();
    }

    #[Test]
    public function it_lists_the_published_articles(): void
    {
        $published = $this->createArticle('Published', active: true);
        $this->createArticle('Hidden', active: false);

        $response = $this->getJson('/api/articles');

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.data.0.id', $published->id);
        $this->assertCount(1, $response->json('data.data'));
    }

    #[Test]
    public function it_does_not_list_unpublished_articles(): void
    {
        $this->createArticle('Hidden', active: false);

        $this->getJson('/api/articles')->assertOk()->assertJsonCount(0, 'data.data');
    }

    #[Test]
    public function it_returns_one_published_article(): void
    {
        $article = $this->createArticle('Published', active: true);

        $response = $this->getJson('/api/articles/'.$article->id);

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.id', $article->id);
    }

    #[Test]
    public function it_hides_an_unpublished_article(): void
    {
        $article = $this->createArticle('Hidden', active: false);

        // The application renders every API exception through json_fail(), so a missing
        // resource answers 422 with success=false rather than 404.
        $response = $this->getJson('/api/articles/'.$article->id);

        $response->assertStatus(422);
        $response->assertJsonPath('success', false);
        $this->assertStringNotContainsString('Hidden', $response->getContent());
    }

    #[Test]
    public function it_filters_by_keyword(): void
    {
        $wanted = $this->createArticle('Wanted title', active: true);
        $this->createArticle('Other title', active: true);

        $response = $this->getJson('/api/articles?keyword=Wanted');

        $response->assertOk();
        $this->assertCount(1, $response->json('data.data'));
        $response->assertJsonPath('data.data.0.id', $wanted->id);
    }

    private function createArticle(string $title, bool $active): Article
    {
        $article = Article::query()->create([
            'catalog_id' => 0,
            'image'      => '',
            'slug'       => 'article-'.uniqid(),
            'position'   => 0,
            'viewed'     => 0,
            'author'     => '',
            'active'     => $active,
        ]);

        $article->translations()->create([
            'locale'  => self::LOCALE,
            'title'   => $title,
            'summary' => '',
            'content' => '',
            'image'   => '',
        ]);

        return $article;
    }
}
