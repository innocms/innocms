<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Devtools\Tests\Services;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\FileLoader;
use InnoCMS\Devtools\Services\TranslationCheckService;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for TranslationCheckService.
 * Runs against a throwaway tree of views and language files: English is the fallback
 * locale, German a second one that lacks some keys.
 */
class TranslationCheckServiceTest extends TestCase
{
    private string $root;

    private Filesystem $files;

    private TranslationCheckService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->files = new Filesystem;
        $this->root  = sys_get_temp_dir().'/innocms-i18n-'.bin2hex(random_bytes(6));
        $this->files->ensureDirectoryExists($this->app());

        $this->write('lang/en/common.php', "<?php\n\nreturn [\n    'hello'    => 'Hello',\n    'nested'   => ['deep' => 'Deep'],\n    'empty'    => '',\n    'only_en'  => 'English only',\n];\n");
        $this->write('lang/de/common.php', "<?php\n\nreturn [\n    'hello'    => 'Hallo',\n    'nested'   => ['deep' => 'Tief'],\n    'only_de'  => 'Nur Deutsch',\n];\n");
        $this->write('lang/de.json', '{"Saving": "Speichern"}');
        $this->write('packs/demo/lang/en/widget.php', "<?php\n\nreturn [\n    'title' => 'Title',\n];\n");

        $loader = new FileLoader($this->files, [$this->app('lang')]);
        $loader->addNamespace('demo', $this->app('packs/demo/lang'));

        $this->service = new TranslationCheckService($loader, $this->app('lang'), 'en', $this->app());
    }

    protected function tearDown(): void
    {
        $this->files->deleteDirectory($this->root);
        parent::tearDown();
    }

    #[Test]
    public function test_collects_the_keys_the_views_use_and_where(): void
    {
        $this->write('views/keys.blade.php', implode("\n", [
            '{{-- \'__(\'common/ignored\')\' --}}',
            "<span>{{ __('common.hello') }}</span>",
            '@lang("common.nested.deep")',
            "<b>{{ trans('demo::widget.title') }}</b>",
            '<i>{{ $on ? __(\'common.a\') : __(\'common.b\') }}</i>',
            "{{ __('common.'.\$field) }}",
        ])."\n");

        $usages = $this->service->collectKeyUsages([$this->app('views')]);

        $this->assertSame([
            'common.hello'       => ['views/keys.blade.php:2'],
            'common.nested.deep' => ['views/keys.blade.php:3'],
            'demo::widget.title' => ['views/keys.blade.php:4'],
            'common.a'           => ['views/keys.blade.php:5'],
            'common.b'           => ['views/keys.blade.php:5'],
        ], $usages);
    }

    #[Test]
    public function test_reports_keys_the_fallback_locale_lacks_as_raw(): void
    {
        $this->write('views/raw.blade.php', "<p>{{ __('common.absent') }}</p>\n<p>{{ __('common.empty') }}</p>\n<p>{{ __('common.only_de') }}</p>\n");

        $result = $this->service->check([$this->app('views')]);

        $this->assertSame([
            'common.absent'  => ['views/raw.blade.php:1'],
            'common.empty'   => ['views/raw.blade.php:2'],
            'common.only_de' => ['views/raw.blade.php:3'],
        ], $result['raw']);
        $this->assertSame([], $result['fallback']);
        $this->assertSame(1, $result['files']);
        $this->assertSame(3, $result['keys']);
    }

    #[Test]
    public function test_reports_keys_that_only_some_locales_have_as_fallback_gaps(): void
    {
        $this->write('views/gap.blade.php', "<p>{{ __('demo::widget.title') }}</p>\n<p>{{ __('common.only_en') }}</p>\n<p>{{ __('common.hello') }}</p>\n");

        $result = $this->service->check([$this->app('views')]);

        $this->assertSame([], $result['raw']);
        $this->assertSame([
            'demo::widget.title' => ['de'],
            'common.only_en'     => ['de'],
        ], $result['fallback']);
    }

    #[Test]
    public function test_resolves_nested_array_and_json_translations_per_locale(): void
    {
        $this->assertTrue($this->service->has('common.nested.deep', 'en'));
        $this->assertTrue($this->service->has('common.nested.deep', 'de'));
        $this->assertFalse($this->service->has('common.nested.absent', 'en'));

        $this->assertTrue($this->service->has('Saving', 'de'));
        $this->assertFalse($this->service->has('Saving', 'en'));
        $this->assertFalse($this->service->has('Absent', 'de'));
    }

    #[Test]
    public function test_skips_the_framework_views_under_vendor(): void
    {
        $this->write('views/vendor/pagination/tailwind.blade.php', "{{ __('Showing') }}\n");

        $result = $this->service->check([$this->app('views')]);

        $this->assertSame([], $result['raw']);
        $this->assertSame(0, $result['files']);
    }

    #[Test]
    public function test_lists_hardcoded_chinese_text_but_not_comments(): void
    {
        $this->write('views/cjk.blade.php', implode("\n", [
            '{{-- 模板注释 --}}',
            '{{-- 跨行',
            '    注释 --}}',
            "<div>写死的中文 {{ __('common.hello') }}</div>",
            '  // 整行 JS 注释',
            "  const url = 'https://example.com'; // 行尾中文注释",
            "<span>{{ __('common.hello') }}</span>",
            '<!-- HTML 注释 -->',
            '<!-- 跨行 HTML',
            '     注释 -->',
            '<span class="text-secondary">·</span>',
        ])."\n");

        $this->assertSame([], $this->service->check([$this->app('views')])['hardcoded'], 'Hardcoded text is only reported on request.');
        $this->assertSame(
            ['views/cjk.blade.php' => [4 => "<div>写死的中文 {{ __('common.hello') }}</div>"]],
            $this->service->hardcodedCjk([$this->app('views')])
        );
    }

    #[Test]
    public function test_lists_the_locales_that_ship_with_the_application(): void
    {
        $this->assertSame(['de', 'en'], $this->service->locales());
    }

    private function app(string $path = ''): string
    {
        return $this->root.'/app'.($path ? '/'.$path : '');
    }

    private function write(string $path, string $content): void
    {
        $this->files->ensureDirectoryExists(dirname($this->app($path)));
        $this->files->put($this->app($path), $content);
    }
}
