<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Front\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use InnoCMS\Common\Repositories\LocaleRepo;
use InnoCMS\Front\Controllers\CatalogController;
use InnoCMS\Front\Controllers\SitemapController;
use InnoCMS\Front\Controllers\TagController;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * The catalog and tag lists only redirect to the article list. Their controllers called
 * route('articles.index'), a name InnoCMS never registers because front routes carry a
 * locale prefix, so /{locale}/catalogs and /{locale}/tags answered 500. Both were also
 * listed in the sitemap, although a sitemap should only contain pages that render.
 */
class ListRedirectTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        // LocaleRepo caches the enabled locales in a static property. Another test in the same
        // process may have filled it, and the front provider reads it while it registers the
        // routes, which would give the front routes locale-prefixed names. Clear it before the
        // application boots.
        LocaleRepo::$enabledLocales = null;

        parent::setUp();
    }

    #[Test]
    public function the_catalog_and_tag_lists_redirect_to_the_article_list(): void
    {
        $this->assertSame(front_route('articles.index'), (new CatalogController)->index()->getTargetUrl());
        $this->assertSame(front_route('articles.index'), (new TagController)->index()->getTargetUrl());
    }

    #[Test]
    public function the_sitemap_does_not_list_redirects(): void
    {
        $xml = (new SitemapController)->index(Request::create('/sitemap.xml'))->getContent();

        $this->assertStringContainsString(front_route('articles.index'), $xml);
        $this->assertStringNotContainsString(front_route('catalogs.index'), $xml);
        $this->assertStringNotContainsString(front_route('tags.index'), $xml);
    }
}
