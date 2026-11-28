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
use InnoCMS\Panel\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\Traits\CreatesAdmin;

/**
 * The settings page must follow the panel locale: no raw translation key, no hardcoded
 * Chinese left in the other languages, and the Chinese wording unchanged.
 */
class SettingsI18nTest extends TestCase
{
    use CreatesAdmin;
    use RefreshDatabase;

    /**
     * Locales of lang/.
     */
    private const LOCALES = ['zh-cn', 'en', 'de', 'es', 'fr', 'it', 'ja', 'ko', 'pt', 'ru'];

    /**
     * Views that make up the settings page.
     */
    private const VIEWS = [
        'innopacks/panel/resources/views/settings/index.blade.php',
        'innopacks/panel/resources/views/settings/_storage_setting.blade.php',
        'innopacks/panel/resources/views/settings/_tools_setting.blade.php',
        'innopacks/aicore/resources/views/settings/_tools.blade.php',
        'innopacks/mcp/resources/views/settings/_tools.blade.php',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(LocaleSeeder::class);
    }

    /**
     * Every locale the panel ships with.
     *
     * @return array
     */
    public static function panel_locales(): array
    {
        return array_map(fn ($locale) => [$locale], array_combine(self::LOCALES, self::LOCALES));
    }

    #[Test]
    #[DataProvider('panel_locales')]
    public function it_prints_translations_instead_of_raw_keys(string $locale): void
    {
        $html = $this->renderSettingsPage($locale);

        foreach ($this->keysUsedByThePage() as $key) {
            $this->assertStringNotContainsString($key, $html, "[$locale] raw translation key leaked: $key");
        }
    }

    #[Test]
    #[DataProvider('panel_locales')]
    public function it_has_no_hardcoded_chinese_in_the_other_languages(string $locale): void
    {
        if (in_array($locale, ['zh-cn', 'ja'], true)) {
            $this->markTestSkipped("$locale is written in Chinese characters.");
        }

        // The language tabs show every language under its native name.
        $nativeNames = array_map(fn ($code) => trans('common/base.language_name', [], $code), array_column(self::panel_locales(), 0));
        $html        = $this->renderSettingsPage($locale);

        preg_match_all('/\p{sc=Han}+/u', str_replace($nativeNames, '', $this->visibleText($html)), $matches);

        $this->assertSame([], $matches[0], "[$locale] hardcoded Chinese text: ".implode('、', array_unique($matches[0])));
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

    #[Test]
    public function it_keeps_the_chinese_wording(): void
    {
        $html = $this->renderSettingsPage('zh-cn');

        foreach (['系统设置', '基本设置', '前台 Logo', '后台 Logo', '缺省图', '浏览器小图标', '调试模式', '启用 html 后缀',
            '隐藏 URL 语言标识', 'ICP备案号', '第三方JS代码'] as $text) {
            $this->assertStringContainsString($text, $html, "[zh-cn] missing text: $text");
        }
    }

    #[Test]
    public function it_translates_the_english_wording(): void
    {
        $html = $this->renderSettingsPage('en');

        foreach (['System Settings', 'Basic Settings', 'Debug Mode'] as $text) {
            $this->assertStringContainsString($text, $html, "[en] missing text: $text");
        }
    }

    private function renderSettingsPage(string $locale): string
    {
        $admin = $this->createAdmin();
        $admin->forceFill(['locale' => $locale])->save();

        return $this->actingAs($admin, 'admin')->get(panel_route('settings.index'))->assertOk()->getContent();
    }

    /**
     * Translation keys referenced by the views of the page.
     *
     * @return array
     */
    private function keysUsedByThePage(): array
    {
        $keys = [];

        foreach (self::VIEWS as $view) {
            preg_match_all('/(?:__|trans)\(\s*([\x27"])([a-zA-Z0-9_\/:.]+)\1/', file_get_contents(base_path($view)), $matches);
            $keys = array_merge($keys, $matches[2]);
        }

        return array_values(array_unique($keys));
    }
}
