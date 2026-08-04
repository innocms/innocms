<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServiceProvider::class,

    InnoCMS\Common\CommonServiceProvider::class,
    InnoCMS\Aicore\AicoreServiceProvider::class,
    InnoCMS\Mcp\McpServiceProvider::class,
    InnoCMS\Devtools\DevToolsServiceProvider::class,
    InnoCMS\Restapi\RestAPIServiceProvider::class,
    InnoCMS\Panel\PanelServiceProvider::class,
    InnoCMS\Front\FrontServiceProvider::class,
    InnoCMS\Install\InstallServiceProvider::class,
    InnoCMS\Plugin\PluginServiceProvider::class,
];
