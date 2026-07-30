<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Mcp;

use Illuminate\Support\ServiceProvider;

class McpServiceProvider extends ServiceProvider
{
    /**
     * config path.
     */
    private string $basePath = __DIR__.'/../';

    public function register(): void
    {
        $this->loadViewsFrom($this->basePath.'resources/views', 'mcp');
        $this->loadTranslationsFrom($this->basePath.'lang', 'mcp');
    }

    public function boot(): void
    {
        if (! has_install_lock()) {
            return;
        }

        $this->loadRoutesFrom($this->basePath.'routes/mcp.php');
    }
}
