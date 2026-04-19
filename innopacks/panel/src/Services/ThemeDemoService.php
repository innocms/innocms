<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Panel\Services;

use Exception;
use Illuminate\Support\Facades\File;

/**
 * Theme demo package: resolves themes/{code}/demo/Seeder.php (callable), copies images to public/static/themes/{code}/images.
 *
 * Mirrors InnoShop Factory behaviour adapted for InnoCMS CMS tables only.
 */
class ThemeDemoService
{
    public function __construct(
        protected ThemeDemoCmsResetService $cmsResetService
    ) {}

    /**
     * Whether the theme exposes a runnable demo PHP seeder.
     */
    public function hasDemo(string $themeDirectory): bool
    {
        return $this->resolveDemoSeederPath($themeDirectory) !== null;
    }

    /**
     * @throws Exception
     */
    public function importDemo(string $themeCode, string $themeDirectory, bool $clearExistingContent): void
    {
        $seederFile = $this->resolveDemoSeederPath($themeDirectory);
        if ($seederFile === null) {
            throw new Exception(trans('panel::themes.error_demo_not_found'));
        }

        smart_log('info', 'Starting theme demo import', [
            'theme'  => $themeCode,
            'seeder' => $seederFile,
            'clear'  => $clearExistingContent,
        ]);

        if ($clearExistingContent) {
            $this->cmsResetService->truncateCmsContent();
        }

        $this->publishThemeImages($themeCode, $themeDirectory);

        $loaded = require $seederFile;
        if (! is_callable($loaded)) {
            throw new Exception(trans('panel::themes.error_demo_invalid_seeder'));
        }

        $loaded($themeDirectory);

        smart_log('info', 'Theme demo import completed', ['theme' => $themeCode]);
    }

    /**
     * Copy theme packaged images used by seeded content URLs under /static/themes/{code}/images.
     */
    public function publishThemeImages(string $themeCode, string $themeDirectory): void
    {
        $relativeDest = 'static/themes/'.$themeCode.'/images';
        $dest         = public_path($relativeDest);

        $sources = [
            $themeDirectory.'/public/images',
            $themeDirectory.'/demo/images',
        ];

        foreach ($sources as $src) {
            if (! is_dir($src)) {
                continue;
            }

            File::ensureDirectoryExists($dest);
            File::copyDirectory($src, $dest);
            smart_log('debug', 'Theme demo images published', ['from' => $src, 'to' => $dest]);
        }
    }

    public function resolveDemoSeederPath(string $themeDirectory): ?string
    {
        $demoDir = $themeDirectory.'/demo';
        if (! is_dir($demoDir)) {
            return null;
        }

        $preferred = $demoDir.'/Seeder.php';
        if (is_file($preferred)) {
            return $preferred;
        }

        $candidates = glob($demoDir.'/*.php');
        if (! $candidates) {
            return null;
        }
        sort($candidates);

        return $candidates[0] ?? null;
    }
}
