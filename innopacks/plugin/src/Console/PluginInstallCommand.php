<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Plugin\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use InnoCMS\Common\Repositories\SettingRepo;
use InnoCMS\Plugin\Core\Plugin;
use InnoCMS\Plugin\Services\PluginService;
use Throwable;

class PluginInstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'innocms:plugin-install
        {codes* : Plugin code(s) to install and enable (case-insensitive)}';

    /**
     * The console command description.
     */
    protected $description = 'Install plugin(s), run their migrations and enable them';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $codes   = (array) $this->argument('codes');
        $plugins = app('plugin')->getPlugins();
        $failed  = false;

        foreach ($codes as $code) {
            $plugin = $this->resolvePlugin($plugins, $code);

            if (! $plugin) {
                $available = $plugins->map(fn (Plugin $p) => $p->getCode())->implode(', ');
                $this->error("Plugin [$code] not found. Available: {$available}");
                $failed = true;

                continue;
            }

            try {
                PluginService::getInstance()->installPlugin($plugin);
                // Persisted under the plugin's own code as space — always use
                // the canonical code from config.json, not the CLI input.
                SettingRepo::getInstance()->updatePluginValue($plugin->getCode(), 'active', 1);

                $this->info("Plugin [{$plugin->getCode()}] {$this->pluginName($plugin)} installed and enabled.");
            } catch (Throwable $e) {
                $this->error("Plugin [$code] failed: {$e->getMessage()}");
                $failed = true;
            }
        }

        return $failed ? self::FAILURE : self::SUCCESS;
    }

    /**
     * Resolve a plugin by code, tolerating case and separator differences
     * (e.g. "PAGEBUILDER" matches "page_builder").
     *
     * @param  Collection  $plugins
     * @param  string  $code
     * @return Plugin|null
     */
    private function resolvePlugin($plugins, string $code): ?Plugin
    {
        $normalize = fn (string $value) => str_replace(['-', '_'], '', strtolower($value));
        $needle    = $normalize($code);

        return $plugins->first(function (Plugin $p) use ($normalize, $needle) {
            $pluginCode = $normalize($p->getCode());

            return $pluginCode === $needle;
        });
    }

    /**
     * Format the plugin name for display (config names may be localized arrays).
     *
     * @param  Plugin  $plugin
     * @return string
     */
    private function pluginName(Plugin $plugin): string
    {
        $name = $plugin->getName();
        if (is_string($name)) {
            return $name;
        }

        return (string) ($name[locale_code()] ?? $name[array_key_first($name)] ?? '');
    }
}
