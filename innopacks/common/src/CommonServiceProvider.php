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
    private string $basePath = __DIR__.'/../';

    /**
     * Boot front service provider.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerConfig();
        $this->registerMigrations();
        $this->loadViewComponents();
    }

    /**
     * Register config.
     *
     * @return void
     */
    private function registerConfig(): void
    {
        $this->mergeConfigFrom($this->basePath.'config/innocms.php', 'innocms');
    }

    /**
     * Register migrations.
     *
     * @return void
     */
    private function registerMigrations(): void
    {
        $this->loadMigrationsFrom($this->basePath.'database/migrations');
    }

    /**
     * Load view components.
     *
     * @return void
     */
    protected function loadViewComponents(): void
    {
        $this->loadViewComponentsAs('common', [
            'alert'             => Components\Alert::class,
            'form-input'        => Components\Forms\Input::class,
            'form-image'        => Components\Forms\Image::class,
            'form-images'       => Components\Forms\Images::class,
            'form-rich-text'    => Components\Forms\RichText::class,
            'form-select'       => Components\Forms\Select::class,
            'form-switch-radio' => Components\Forms\SwitchRadio::class,
            'form-textarea'     => Components\Forms\Textarea::class,
            'no-data'           => Components\NoData::class,
        ]);
    }
}
