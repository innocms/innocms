<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Panel\Tests\Feature;

use Database\Seeders\LocaleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use InnoCMS\Panel\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\Traits\CreatesAdmin;

/**
 * The main panel pages must follow the panel locale in both shipped-complete languages:
 * no raw translation key, and no hardcoded Chinese left in English.
 */
class PanelPagesI18nTest extends TestCase
{
    use CreatesAdmin;
    use RefreshDatabase;

    /**
     * Pages that render without any record in the database.
     *
     * @return array
     */
    public static function panel_pages(): array
    {
        $routes = [
            'dashboard.index', 'analytics.index', 'visits.index',
            'articles.index', 'articles.create', 'catalogs.index', 'catalogs.create',
            'pages.index', 'pages.create', 'tags.index', 'tags.create',
            'products.index', 'products.create', 'categories.index', 'categories.create',
            'contacts.index', 'media.index', 'locales.index',
            'themes.index', 'themes_settings.index',
            'account.index', 'settings.index', 'admins.index', 'admins.create', 'roles.index', 'roles.create',
        ];

        return array_map(fn ($route) => [$route], array_combine($routes, $routes));
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(LocaleSeeder::class);
    }

    #[Test]
    #[DataProvider('panel_pages')]
    public function it_renders_in_the_panel_locale(string $route): void
    {
        $admin = $this->createAdmin();

        foreach (['zh-cn', 'en'] as $locale) {
            $admin->forceFill(['locale' => $locale])->save();
            $html = $this->actingAs($admin, 'admin')->get(panel_route($route))->assertOk()->getContent();

            foreach ($this->keysUsedByTheViews() as $key) {
                $this->assertStringNotContainsString($key, $html, "[$locale] $route printed a raw translation key: $key");
            }

            if ($locale === 'en') {
                // The language tabs show every language under its native name.
                $nativeNames = array_map(fn ($code) => trans('common/base.language_name', [], $code), ['zh-cn', 'en', 'de', 'es', 'fr', 'it', 'ja', 'ko', 'pt', 'ru']);
                preg_match_all('/\p{sc=Han}+/u', str_replace($nativeNames, '', $this->visibleText($html)), $matches);

                $this->assertSame([], $matches[0], "[en] $route contains hardcoded Chinese text: ".implode('、', array_unique($matches[0])));
            }
        }
    }

    /**
     * The list pagination only shows up with more than one page of records, and its view lives under
     * views/vendor/, which dev:i18n-check leaves out on purpose.
     */
    #[Test]
    public function it_translates_the_list_pagination(): void
    {
        $paginator = new LengthAwarePaginator(range(1, 45), 45, 15, 1, ['path' => 'https://example.com/panel/visits']);

        foreach (['zh-cn' => '共3页，共45项', 'en' => '3 pages, 45 items'] as $locale => $expected) {
            app()->setLocale($locale);

            $html = $paginator->links('panel::vendor.pagination.bootstrap-4')->toHtml();

            $this->assertStringContainsString($expected, $html);
        }

        preg_match_all('/\p{sc=Han}+/u', strip_tags($html), $matches);

        $this->assertSame([], $matches[0], 'The English pagination still contains Chinese text.');
    }

    /**
     * What the page shows to a user: scripts and comments are code, not text.
     *
     * @param  string  $html
     * @return string
     */
    private function visibleText(string $html): string
    {
        $html = preg_replace('/<script\b[^>]*>.*?<\/script>/s', '', $html) ?? $html;
        $html = preg_replace('/<!--.*?-->/s', '', $html) ?? $html;

        return trim(preg_replace('/\s+/u', ' ', strip_tags($html)) ?? '');
    }

    /**
     * Translation keys referenced by the views of every pack: the panel page includes cards of other packs.
     *
     * @return array
     */
    private function keysUsedByTheViews(): array
    {
        static $keys = [];

        if ($keys) {
            return $keys;
        }

        foreach (glob(base_path('innopacks/*/resources/views')) as $dir) {
            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS));

            foreach ($iterator as $file) {
                if (str_ends_with($file->getFilename(), '.blade.php') && ! str_contains($file->getPathname(), '/views/vendor/')) {
                    preg_match_all('/(?:__|trans|@lang)\(\s*([\x27"])([a-zA-Z0-9_\/:.]+)\1/', file_get_contents($file->getPathname()), $matches);
                    $keys = array_merge($keys, $matches[2]);
                }
            }
        }

        return $keys = array_values(array_unique($keys));
    }
}
