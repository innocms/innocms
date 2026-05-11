<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Panel\Repositories;

class ThemeRepo
{
    /**
     * @return self
     */
    public static function getInstance(): ThemeRepo
    {
        return new self;
    }

    /**
     * Absolute paths of installed theme directories.
     *
     * @return array<int, string>
     */
    public function getThemeDirs(): array
    {
        $path = base_path('themes');

        return glob($path.'/*', GLOB_ONLYDIR) ?: [];
    }

    /**
     * Absolute path for an installed theme folder (directory name must equal code).
     */
    public function getThemeDirectory(string $code): ?string
    {
        $path = base_path('themes/'.$code);

        return is_dir($path) ? realpath($path) : null;
    }

    /**
     * Read theme package metadata.
     *
     * @return array<string, mixed>
     *
     * @throws \Exception
     */
    public function readConfig(string $dir): array
    {
        $configFile = $dir.'/config.json';
        if (! file_exists($configFile)) {
            throw new \Exception(trans('panel/themes.error_config_not_found', ['file' => $configFile]));
        }
        $config = json_decode((string) file_get_contents($configFile), true);
        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($config)) {
            throw new \Exception(trans('panel/themes.error_config_invalid', ['file' => $configFile]));
        }

        return $config;
    }
}
