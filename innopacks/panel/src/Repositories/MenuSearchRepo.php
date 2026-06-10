<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Panel\Repositories;

use Illuminate\Support\Facades\Cache;
use InnoCMS\Plugin\Core\Plugin;

class MenuSearchRepo
{
    /**
     * @return static
     */
    public static function getInstance(): static
    {
        return new static;
    }

    /**
     * Search menus by keyword.
     *
     * @param  string  $keyword
     * @return array
     */
    public function search(string $keyword = ''): array
    {
        $admin = current_admin();
        if (! $admin) {
            return [];
        }

        $cacheKey = 'menu_search:'.$admin->getAuthIdentifier().':'.app()->getLocale();
        $items    = Cache::remember($cacheKey, 60, fn () => $this->getSearchableMenus());

        if ($keyword === '') {
            return $items;
        }

        $keyword = mb_strtolower($keyword);

        return array_values(array_filter($items, function ($item) use ($keyword) {
            $haystack = mb_strtolower($item['title'].' '.($item['keywords'] ?? ''));

            return str_contains($haystack, $keyword);
        }));
    }

    /**
     * Get all searchable menus.
     *
     * @return array
     */
    public function getSearchableMenus(): array
    {
        $admin = current_admin();
        if (! $admin) {
            return [];
        }

        $items = [];
        $seen  = [];

        $menuTree = $this->getMenuTree();

        foreach ($menuTree as $group) {
            $groupTitle = $group['title'] ?? '';
            $children   = $group['children'] ?? [];
            $topRoute   = $group['route'] ?? '';

            if ($topRoute !== '' && $children === []) {
                $routeCode = str_replace('.', '_', $topRoute);
                if (! $admin->can($routeCode)) {
                    continue;
                }
                $seen[]  = $topRoute;
                $items[] = [
                    'title'    => $group['title'] ?? '',
                    'url'      => $this->resolveUrl($topRoute, $group['url'] ?? ''),
                    'keywords' => $groupTitle,
                ];

                continue;
            }

            foreach ($children as $child) {
                $route = $child['route'] ?? '';
                if (empty($route)) {
                    continue;
                }

                // Permission check
                $routeCode = str_replace('.', '_', $route);
                if (! $admin->can($routeCode)) {
                    continue;
                }

                $seen[] = $route;

                $items[] = [
                    'title'    => $child['title'] ?? '',
                    'url'      => $this->resolveUrl($route, $child['url'] ?? ''),
                    'keywords' => $groupTitle,
                ];
            }
        }

        // Supplement with enabled plugins
        $pluginItems = $this->getSupplementPluginRoutes($seen, $admin);

        return array_merge($items, $pluginItems);
    }

    /**
     * Build the menu tree with translations, same structure as Sidebar::getMenus().
     */
    private function getMenuTree(): array
    {
        return fire_hook_filter('component.sidebar.menus', [
            [
                'route' => 'dashboard.index',
                'title' => __('panel/menu.dashboard'),
                'icon'  => 'bi-house',
            ],
            [
                'title'    => __('panel/menu.top_content'),
                'icon'     => 'bi-sticky',
                'prefixes' => ['articles', 'catalogs', 'tags', 'pages', 'file_manager'],
                'children' => fire_hook_filter('component.sidebar.content.routes', [
                    ['route' => 'articles.index', 'title' => __('panel/menu.articles')],
                    ['route' => 'catalogs.index', 'title' => __('panel/menu.catalogs')],
                    ['route' => 'tags.index', 'title' => __('panel/menu.tags')],
                    ['route' => 'pages.index', 'title' => __('panel/menu.pages')],
                    ['route' => 'file_manager.index', 'title' => __('panel/menu.file_manager')],
                ]),
            ],
            [
                'title'    => __('panel/menu.top_analytic'),
                'icon'     => 'bi-bar-chart',
                'prefixes' => ['analytics'],
                'children' => fire_hook_filter('component.sidebar.analytic.routes', [
                    ['route' => 'analytics.index', 'title' => __('panel/menu.analytics')],
                ]),
            ],
            [
                'title'    => __('panel/menu.top_design'),
                'icon'     => 'bi-palette',
                'children' => fire_hook_filter('component.sidebar.design.routes', [
                    ['route' => 'themes_settings.index', 'title' => __('panel/menu.themes_settings')],
                    ['route' => 'themes.index', 'title' => __('panel/menu.themes')],
                ]),
            ],
            [
                'title'    => __('panel/menu.top_plugin'),
                'icon'     => 'bi-puzzle',
                'children' => fire_hook_filter('component.sidebar.plugin.routes', [
                    ['route' => 'plugins.index', 'title' => __('panel/menu.plugins')],
                ]),
            ],
            [
                'title'    => __('panel/menu.top_setting'),
                'icon'     => 'bi-gear',
                'children' => fire_hook_filter('component.sidebar.setting.routes', [
                    ['route' => 'settings.index', 'title' => __('panel/menu.settings')],
                    ['route' => 'account.index', 'title' => __('panel/menu.account')],
                    ['route' => 'admins.index', 'title' => __('panel/menu.admins')],
                    ['route' => 'roles.index', 'title' => __('panel/menu.roles')],
                    ['route' => 'locales.index', 'title' => __('panel/menu.locales')],
                ]),
            ],
        ]);
    }

    /**
     * Resolve URL from route name.
     */
    private function resolveUrl(string $route, string $fallbackUrl = ''): string
    {
        try {
            return panel_route($route);
        } catch (\Exception $e) {
            return $fallbackUrl;
        }
    }

    /**
     * Supplement with enabled plugins that have panel_route.
     *
     * @param  array  $seenRoutes
     * @param  mixed  $admin
     * @return array
     */
    private function getSupplementPluginRoutes(array $seenRoutes, $admin): array
    {
        $items = [];

        if (! $admin->can('plugins_edit')) {
            return [];
        }

        try {
            $plugins = app('plugin')->getPlugins();
        } catch (\Exception $e) {
            return [];
        }

        foreach ($plugins as $plugin) {
            $dirname     = $plugin->getDirname();
            $pluginTitle = $this->getPluginName($plugin);
            $enabled     = $plugin->checkInstalled() && $plugin->getEnabled();

            // Enabled plugin with panel_route: link to its own page
            $panelRoute = $enabled ? $this->getPluginPanelRoute($plugin) : '';
            if (! empty($panelRoute) && ! in_array($panelRoute, $seenRoutes)) {
                $routeCode = str_replace('.', '_', $panelRoute);
                if ($admin->can($routeCode)) {
                    try {
                        $url          = panel_route($panelRoute);
                        $seenRoutes[] = $panelRoute;
                        $items[]      = [
                            'title'    => $pluginTitle,
                            'url'      => $url,
                            'keywords' => $dirname,
                        ];

                        continue;
                    } catch (\Exception $e) {
                        // ignore route resolution errors
                    }
                }
            }

            // All other plugins: link to plugins.edit page
            try {
                $url     = panel_route('plugins.edit', ['plugin' => $dirname]);
                $items[] = [
                    'title'    => $pluginTitle,
                    'url'      => $url,
                    'keywords' => $dirname,
                ];
            } catch (\Exception $e) {
                // ignore
            }
        }

        return $items;
    }

    /**
     * Get panel_route from plugin config.json.
     */
    private function getPluginPanelRoute(Plugin $plugin): string
    {
        $configFile = $plugin->getPath().'/config.json';
        if (! file_exists($configFile)) {
            return '';
        }

        $config = json_decode(file_get_contents($configFile), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return '';
        }

        return $config['panel_route'] ?? '';
    }

    /**
     * Get localized plugin name.
     */
    private function getPluginName(Plugin $plugin): string
    {
        $configFile = $plugin->getPath().'/config.json';
        if (! file_exists($configFile)) {
            return $plugin->getDirname();
        }

        $config = json_decode(file_get_contents($configFile), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $plugin->getDirname();
        }

        $names = $config['name'] ?? [];
        if (! is_array($names)) {
            return (string) $names;
        }

        $locale = app()->getLocale();

        return $names[$locale] ?? $names['en'] ?? $plugin->getDirname();
    }
}
