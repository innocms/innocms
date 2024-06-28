<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServiceProvider::class,

    InnoCMS\Common\CommonServiceProvider::class,
    InnoCMS\Panel\PanelServiceProvider::class,
    InnoCMS\Front\FrontServiceProvider::class,
    InnoShop\Install\InstallServiceProvider::class,
    InnoShop\Plugin\PluginServiceProvider::class,
];
