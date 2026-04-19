<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServiceProvider::class,

    InnoCMS\Common\CommonServiceProvider::class,
    InnoCMS\DevTools\DevToolsServiceProvider::class,
    InnoCMS\RestAPI\RestAPIServiceProvider::class,
    InnoCMS\Panel\PanelServiceProvider::class,
    InnoCMS\Front\FrontServiceProvider::class,
    InnoCMS\Install\InstallServiceProvider::class,
    InnoCMS\Plugin\PluginServiceProvider::class,
];
