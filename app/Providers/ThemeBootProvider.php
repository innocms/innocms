<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * 主题 boot loader — 站点级 provider。
 *
 * 框架的 FrontServiceProvider 只加载主题的视图/翻译,不会 require
 * `themes/{theme}/setup/boot.php`。本 provider 在每次请求启动时:
 *   1) 读取 system_setting('theme') 得到当前激活主题
 *   2) 若 themes/{theme}/setup/boot.php 存在,require 并调用返回的闭包
 *
 * 这样主题里的 helper(revesti_home*)、Panel 路由、侧边栏 hook 才会生效;
 * 切换/关闭主题后旧 boot.php 自然不再被加载,无需清理。
 *
 * 注意:provider 必须注册在 bootstrap/providers.php 末尾,等 Common/Panel/Front
 * 的 hook 系统都 boot 完,主题注册 hook 时才找得到 listen_hook_filter()。
 */
class ThemeBootProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            $theme = (string) setting('system.theme');
        } catch (\Throwable $e) {
            // 数据库未就绪(安装阶段 / 迁移未跑)时直接放过,避免阻塞 boot。
            return;
        }
        if ($theme === '') {
            return;
        }

        $bootFile = base_path('themes/'.$theme.'/setup/boot.php');
        if (! is_file($bootFile)) {
            return;
        }

        $closure = require $bootFile;
        if (is_callable($closure)) {
            $closure();
        }
    }
}