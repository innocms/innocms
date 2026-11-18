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
use InnoCMS\Common\Services\ThemeDemoService;
use Throwable;

class ThemeDemoCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'innocms:theme-demo
        {code? : Theme code to import demo for (defaults to the active theme)}
        {--keep : Keep existing content instead of clearing it first}';

    /**
     * The console command description.
     */
    protected $description = 'Import the demo seeder bundled with a theme (themes/{code}/demo/seeder.php)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $code = (string) ($this->argument('code') ?: system_setting('theme', 'default'));
        if ($code === '' || $code === 'default') {
            $this->error('No theme code given and no non-default theme is active.');

            return self::FAILURE;
        }

        $dir = theme_path($code);
        if (! is_dir($dir)) {
            $this->error("Theme [$code] not found under themes/. Run innocms:theme --list to see available themes.");

            return self::FAILURE;
        }

        $service = app(ThemeDemoService::class);
        if (! $service->hasDemo($dir)) {
            $this->error("Theme [$code] ships no demo seeder (themes/{$code}/demo/seeder.php missing).");

            return self::FAILURE;
        }

        $clear = ! (bool) $this->option('keep');

        try {
            $service->importDemo($code, $dir, $clear);
        } catch (Throwable $e) {
            $this->error('Demo import failed: '.$e->getMessage());

            return self::FAILURE;
        }

        $this->info($clear
            ? "Demo content imported for [$code] (existing articles/catalogs/pages/tags cleared)."
            : "Demo content imported for [$code] (existing content kept).");

        return self::SUCCESS;
    }
}
