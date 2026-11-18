<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Front\Console;

use Illuminate\Console\Command;
use InnoCMS\Common\Repositories\SettingRepo;
use Throwable;

class ThemeCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'innocms:theme
        {code? : Theme code to activate (pass "default" to fall back to the default theme)}
        {--list : List themes available under themes/}';

    /**
     * The console command description.
     */
    protected $description = 'Switch the active front theme';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        if ($this->option('list')) {
            $current = system_setting('theme', 'default');

            foreach ($this->discoverThemes() as $theme) {
                $marker = $theme['code'] === $current ? ' <fg=green>(active)</>' : '';
                $this->line(sprintf('%s%s%s', $theme['code'], $theme['name'] ? " — {$theme['name']}" : '', $marker));
            }

            return self::SUCCESS;
        }

        $code = (string) $this->argument('code');
        if ($code === '') {
            $this->error('Provide a theme code, or use --list to see available themes.');

            return self::FAILURE;
        }

        // "default" always works; anything else must exist on disk with a valid config.
        if ($code !== 'default' && ! is_file(theme_path($code.'/config.json'))) {
            $this->error("Theme [$code] not found under themes/. Run with --list to see available themes.");

            return self::FAILURE;
        }

        try {
            SettingRepo::getInstance()->updateSystemValue('theme', $code);
        } catch (Throwable $e) {
            $this->error('Failed to switch theme: '.$e->getMessage());

            return self::FAILURE;
        }

        $this->info("Theme switched to [$code].");

        return self::SUCCESS;
    }

    /**
     * Scan themes/ and collect code/name from each config.json.
     *
     * @return array
     */
    private function discoverThemes(): array
    {
        $themes = [];
        $locale = locale_code();

        foreach (glob(theme_path('*/config.json')) ?: [] as $file) {
            try {
                $config = json_decode((string) file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);
            } catch (Throwable) {
                continue;
            }

            $code     = $config['code'] ?? basename(dirname($file));
            $name     = $config['name'][$locale] ?? $config['name'][array_key_first($config['name'] ?? [])] ?? '';
            $themes[] = ['code' => $code, 'name' => $name];
        }

        return $themes;
    }
}
