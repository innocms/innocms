<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Devtools\Tests\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use InnoCMS\Devtools\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * Tests for the dev:i18n-check command.
 * The fixtures live in tests/fixtures/i18n/{ok,broken}.
 */
class CheckTranslationsTest extends TestCase
{
    private const OK = 'innopacks/devtools/tests/fixtures/i18n/ok';

    private const BROKEN = 'innopacks/devtools/tests/fixtures/i18n/broken';

    private const NO_RAWS = 'Printed as raw key (also missing in the fallback locale "en"): 0';

    #[Test]
    public function test_passes_when_every_key_is_translated(): void
    {
        $this->assertSame(Command::SUCCESS, Artisan::call('dev:i18n-check', ['--dir' => [self::OK]]));

        $output = Artisan::output();

        $this->assertStringContainsString(self::NO_RAWS, $output);
        $this->assertStringNotContainsString('Hardcoded Chinese text', $output);
    }

    #[Test]
    public function test_fails_and_points_to_the_view_using_an_untranslated_key(): void
    {
        $this->assertSame(Command::FAILURE, Artisan::call('dev:i18n-check', ['--dir' => [self::BROKEN]]));

        $output = Artisan::output();

        $this->assertStringContainsString('Printed as raw key (also missing in the fallback locale "en"): 1', $output);
        $this->assertStringContainsString('panel/__i18n_check.absent_key', $output);
        $this->assertStringContainsString('used in '.self::BROKEN.'/sample.blade.php:3', $output);
    }

    #[Test]
    public function test_only_reports_hardcoded_chinese_text_on_request(): void
    {
        Artisan::call('dev:i18n-check', ['--dir' => [self::BROKEN]]);
        $this->assertStringNotContainsString('写死的中文', Artisan::output());

        $this->assertSame(Command::FAILURE, Artisan::call('dev:i18n-check', ['--dir' => [self::BROKEN], '--cjk' => true]));
        $output = Artisan::output();
        $this->assertStringContainsString('Hardcoded Chinese text: 1 lines in 1 files', $output);
        $this->assertStringContainsString(self::BROKEN.'/sample.blade.php:4: <div>写死的中文</div>', $output);

        $this->assertSame(Command::SUCCESS, Artisan::call('dev:i18n-check', ['--dir' => [self::OK], '--cjk' => true]));
    }

    #[Test]
    public function test_is_available_under_the_devtools_prefix_too(): void
    {
        $this->assertSame(Command::SUCCESS, Artisan::call('devtools:i18n-check', ['--dir' => [self::OK]]));
    }

    /**
     * The core packs must not ship a view that prints a translation key as-is.
     */
    #[Test]
    public function test_the_core_packs_have_no_untranslated_keys(): void
    {
        $this->assertSame(Command::SUCCESS, Artisan::call('dev:i18n-check'));

        $output = Artisan::output();

        $this->assertMatchesRegularExpression('/^Checked [1-9]\d* translation keys in [1-9]\d* view files across \d+ locales:/m', $output);
        $this->assertStringContainsString(self::NO_RAWS, $output);
    }
}
