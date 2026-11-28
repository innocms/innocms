<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Devtools\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Lang;
use InnoCMS\Devtools\Services\TranslationCheckService;

class CheckTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dev:i18n-check
        {--dir=* : View directory to scan, relative to the project root. Repeatable. Default: the views of every pack}
        {--fallback : List the keys that are missing in some locales and fall back to the fallback locale}
        {--cjk : Also report hardcoded Chinese text in the views}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the translation keys used by Blade views against the language files';

    /**
     * Fails when a view prints a translation key as-is, or with --cjk when it contains hardcoded Chinese text.
     *
     * @return int
     */
    public function handle(): int
    {
        $withCjk  = (bool) $this->option('cjk');
        $dirs     = $this->option('dir') ?: TranslationCheckService::defaultViewDirs(base_path());
        $fallback = (string) config('app.fallback_locale', 'en');

        $service = new TranslationCheckService(Lang::getLoader(), app()->langPath(), $fallback, base_path());
        $result  = $service->check(array_map(fn ($dir) => $this->absolutePath($dir), $dirs), $withCjk);

        $this->info(sprintf(
            'Checked %d translation keys in %d view files across %d locales: %s',
            $result['keys'], $result['files'], count($result['locales']), implode(', ', $result['locales'])
        ));

        $this->newLine();
        $this->reportRawKeys($result['raw'], $fallback);

        $this->newLine();
        $this->reportFallbackGaps($result, $fallback);

        if ($withCjk) {
            $this->newLine();
            $this->reportHardcoded($result['hardcoded']);
        }

        return $result['raw'] || ($withCjk && $result['hardcoded']) ? Command::FAILURE : Command::SUCCESS;
    }

    /**
     * Keys that every locale prints as-is.
     *
     * @param  array  $raw  key => places
     * @param  string  $fallback
     * @return void
     */
    private function reportRawKeys(array $raw, string $fallback): void
    {
        $this->line(sprintf(
            'Printed as raw key (also missing in the fallback locale "%s"): <fg=%s>%d</>',
            $fallback, $raw ? 'red' : 'green', count($raw)
        ));

        foreach ($raw as $key => $places) {
            $this->error('  '.$key);
            foreach ($places as $place) {
                $this->line('    used in '.$place);
            }
        }
    }

    /**
     * Keys that some locales only find in the fallback locale.
     *
     * @param  array{fallback: array, locales: array}  $result
     * @param  string  $fallback
     * @return void
     */
    private function reportFallbackGaps(array $result, string $fallback): void
    {
        $gaps = $result['fallback'];

        $this->line(sprintf('Falling back to "%s" in some locales: <fg=yellow>%d</>', $fallback, count($gaps)));

        foreach ($result['locales'] as $locale) {
            $count = count(array_filter($gaps, fn ($missing) => in_array($locale, $missing, true)));
            $this->line(sprintf('  %-8s %d keys', $locale, $count));
        }

        if ($this->option('fallback')) {
            $this->newLine();

            foreach ($gaps as $key => $missing) {
                $this->line(sprintf('  %-58s missing in %s', $key, implode(',', $missing)));
            }

            return;
        }

        if ($gaps) {
            $this->comment('  Use --fallback to list them.');
        }
    }

    /**
     * @param  array  $hardcoded  file => [line => text]
     * @return void
     */
    private function reportHardcoded(array $hardcoded): void
    {
        $lines = array_sum(array_map('count', $hardcoded));

        $this->line(sprintf(
            'Hardcoded Chinese text: <fg=%s>%d</> lines in %d files',
            $hardcoded ? 'red' : 'green', $lines, count($hardcoded)
        ));

        foreach ($hardcoded as $file => $fileLines) {
            foreach ($fileLines as $line => $text) {
                $this->line(sprintf('  %s:%d: %s', $file, $line, mb_strimwidth($text, 0, 150, '…')));
            }
        }
    }

    /**
     * @param  string  $dir
     * @return string
     */
    private function absolutePath(string $dir): string
    {
        return str_starts_with($dir, '/') ? $dir : base_path($dir);
    }
}
