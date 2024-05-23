<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoShop\Plugin\Repositories;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Artisan;
use InnoShop\Plugin\Core\Plugin as CPlugin;
use InnoShop\Plugin\Models\Plugin;

class PluginRepo
{
    public static Collection $installedPlugins;

    public function __construct()
    {
        self::$installedPlugins = new Collection();
    }

    /**
     * @return self
     */
    public static function getInstance(): PluginRepo
    {
        return new self;
    }

    /**
     * 获取所有已安装插件列表
     *
     * @return Collection
     */
    public function allPlugins(): Collection
    {
        if (self::$installedPlugins->count() > 0) {
            return self::$installedPlugins;
        }

        return self::$installedPlugins = Plugin::all();
    }

    /**
     * @param  array  $filters
     * @return Builder
     */
    public function getBuilder(array $filters = []): Builder
    {
        $builder = Plugin::query();
        $type    = $filters['type'] ?? '';
        if ($type) {
            $builder->where('type', $type);
        }

        $code = $filters['code'] ?? '';
        if ($code) {
            $builder->where('code', $code);
        }

        return $builder;
    }

    /**
     * Group plugins by code.
     *
     * @return Collection
     */
    public function getPluginsGroupCode(): Collection
    {
        $allPlugins = $this->allPlugins();

        return $allPlugins->keyBy('code');
    }

    /**
     * @param  $code
     * @return mixed
     */
    public function getPluginByCode($code): mixed
    {
        return $this->getPluginsGroupCode()->get($code);
    }

    /**
     * Check plugin installed or not.
     * @param  $code
     * @return bool
     */
    public function installed($code): bool
    {
        return $this->getPluginsGroupCode()->has($code);
    }

    /**
     * Get plugin active
     *
     * @param  $pluginCode
     * @return bool
     */
    public function checkActive($pluginCode): bool
    {
        return (bool) setting("{$pluginCode}.active");
    }

    /**
     * Get plugin priority
     *
     * @param  $pluginCode
     * @return int
     */
    public function getPriority($pluginCode): int
    {
        $plugin = $this->getPluginByCode($pluginCode);
        if (empty($plugin)) {
            return 0;
        }

        return (int) $plugin->priority;
    }

    /**
     * Install plugin.
     *
     * @param  CPlugin  $CPlugin
     * @throws Exception
     */
    public function installPlugin(CPlugin $CPlugin): void
    {
        $this->migrateDatabase($CPlugin);
        $type = $CPlugin->getType();
        $code = $CPlugin->getCode();

        $params = [
            'type'     => $type,
            'code'     => $code,
            'priority' => 0,
        ];
        $plugin = $this->getBuilder($params)->first();
        if (empty($plugin)) {
            Plugin::query()->create($params);
        }
    }

    /**
     * Migrate plugin database.
     *
     * @param  CPlugin  $CPlugin
     * @return void
     */
    public function migrateDatabase(CPlugin $CPlugin): void
    {
        $migrationPath = "{$CPlugin->getPath()}/Migrations";
        if (is_dir($migrationPath)) {
            $files = glob($migrationPath.'/*');
            asort($files);

            foreach ($files as $file) {
                $file = str_replace(base_path(), '', $file);
                Artisan::call('migrate', [
                    '--force' => true,
                    '--step'  => 1,
                    '--path'  => $file,
                ]);
            }
        }
    }

    /**
     * Uninstall plugin
     *
     * @param  CPlugin  $CPlugin
     * @return void
     */
    public function uninstallPlugin(CPlugin $CPlugin): void
    {
        $this->rollbackDatabase($CPlugin);
        $filters = [
            'type' => $CPlugin->getType(),
            'code' => $CPlugin->getCode(),
        ];
        $this->getBuilder($filters)->delete();
    }

    /**
     * @param  CPlugin  $CPlugin
     * @return void
     */
    public function rollbackDatabase(CPlugin $CPlugin): void
    {
        $migrationPath = "{$CPlugin->getPath()}/Migrations";
        if (! is_dir($migrationPath)) {
            return;
        }

        $files = glob($migrationPath.'/*');
        arsort($files);
        foreach ($files as $file) {
            $file = str_replace(base_path(), '', $file);
            Artisan::call('migrate:rollback', [
                '--force' => true,
                '--step'  => 1,
                '--path'  => $file,
            ]);
        }
    }
}
