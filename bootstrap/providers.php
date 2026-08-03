<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServiceProvider::class,

    InnoCMS\Common\CommonServiceProvider::class,
    InnoCMS\Aicore\AicoreServiceProvider::class,
    InnoCMS\Devtools\DevtoolsServiceProvider::class,
    InnoCMS\Restapi\RestapiServiceProvider::class,
    InnoCMS\Panel\PanelServiceProvider::class,
    InnoCMS\Front\FrontServiceProvider::class,
    InnoCMS\Install\InstallServiceProvider::class,
    InnoCMS\Plugin\PluginServiceProvider::class
];
