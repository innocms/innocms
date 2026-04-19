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
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use InnoCMS\Panel\Repositories\ThemeRepo;

/**
 * Mirrors InnoShop Factory theme discovery: validates config.json, localized names,
 * preview path resolution, and surfaces per-theme errors instead of crashing the UI.
 */
class ThemeService
{
    /**
     * @return array{themes: Collection, errors: array<int, array{name: string, folder: string, error: string}>}
     */
    public function getListFromPath(): array
    {
        $current = system_setting('theme');
        $dirs    = ThemeRepo::getInstance()->getThemeDirs();
        $errors  = [];

        $themes = collect($dirs)->map(function (string $dir) use ($current, &$errors) {
            $folderName = basename($dir);
            $themeName  = $folderName;
            $themeCode  = strtolower($folderName);
            $localeCode = locale_code();

            try {
                $config = ThemeRepo::getInstance()->readConfig($dir);

                if (isset($config['name'])) {
                    $themeName = is_array($config['name'])
                        ? ($config['name'][$localeCode] ?? $config['name']['zh-cn'] ?? $config['name']['en'] ?? $folderName)
                        : (string) $config['name'];
                }

                $themeDescription = '';
                if (isset($config['description'])) {
                    $themeDescription = is_array($config['description'])
                        ? ($config['description'][$localeCode] ?? $config['description']['zh-cn'] ?? $config['description']['en'] ?? '')
                        : (string) $config['description'];
                }

                $this->validateConfig($config);
                $this->validateCode($config, $themeCode);

                return [
                    'code'        => $themeCode,
                    'name'        => $themeName,
                    'description' => $themeDescription,
                    'selected'    => $current === $themeCode,
                    'preview'     => $this->getPreviewPath($dir),
                    'version'     => $config['version'] ?? '',
                    'author'      => $config['author'] ?? [],
                    'has_demo'    => app(ThemeDemoService::class)->hasDemo($dir),
                ];
            } catch (Exception $e) {
                Log::warning("Theme validation failed: {$e->getMessage()}", [
                    'directory' => $folderName,
                    'path'      => $dir,
                ]);
                $errors[] = [
                    'name'   => $themeName,
                    'folder' => $folderName,
                    'error'  => $e->getMessage(),
                ];

                return null;
            }
        })->filter()->values();

        return [
            'themes' => $themes,
            'errors' => $errors,
        ];
    }

    /**
     * Standard preview relative to themes/{code}/public/ (images/preview.*).
     */
    public function getPreviewPath(string $dir): string
    {
        foreach (['png', 'jpg', 'jpeg', 'webp', 'gif'] as $ext) {
            $previewImage = $dir.'/public/images/preview.'.$ext;
            if (file_exists($previewImage)) {
                return 'images/preview.'.$ext;
            }
        }

        return '';
    }

    protected function validateConfig(array $config): void
    {
        $required = ['code', 'name', 'version'];
        foreach ($required as $field) {
            if (! isset($config[$field])) {
                throw new Exception(trans('panel::themes.error_config_missing', ['field' => $field]));
            }
        }
    }

    protected function validateCode(array $config, string $folderCode): void
    {
        if (($config['code'] ?? '') !== $folderCode) {
            throw new Exception(trans('panel::themes.error_code_mismatch', [
                'folder' => $folderCode,
                'code'   => $config['code'] ?? '',
            ]));
        }

        if (($config['code'] ?? '') !== strtolower((string) $config['code'])) {
            throw new Exception(trans('panel::themes.error_code_not_lowercase', [
                'code' => $config['code'],
            ]));
        }
    }
}
