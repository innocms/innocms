<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServiceProvider::class,

    InnoCMS\Common\CommonServiceProvider::class,
    InnoCMS\Aicore\AicoreServiceProvider::class,
    InnoCMS\Mcp\McpServiceProvider::class,
    InnoCMS\DevTools\DevToolsServiceProvider::class,
    InnoCMS\RestAPI\RestAPIServiceProvider::class,
    InnoCMS\Panel\PanelServiceProvider::class,
    InnoCMS\Front\FrontServiceProvider::class,
    InnoCMS\Install\InstallServiceProvider::class,
    InnoCMS\Plugin\PluginServiceProvider::class,

    // 主题 boot loader(必须放最后):加载 themes/{system.theme}/setup/boot.php。
    App\Providers\ThemeBootProvider::class,
];
