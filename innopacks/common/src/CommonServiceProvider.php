<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Common;

use Illuminate\Support\ServiceProvider;

class CommonServiceProvider extends ServiceProvider
{
    /**
     * config path.
     */
    private string $configPath = __DIR__.'/../config/innocms.php';

    /**
     * Boot front service provider.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerConfig();
    }

    /**
     * @return void
     */
    private function registerConfig(): void
    {
        $this->mergeConfigFrom($this->configPath, 'innocms');
    }
}
