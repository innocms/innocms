<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Panel\Components;

use Illuminate\View\Component;

class Sidebar extends Component
{
    public mixed $adminUser;

    public array $menuLinks = [];

    private string $currentUri;

    private string $currentRoute;

    private string $currentPrefix;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->adminUser = current_admin();

        $routeNameWithPrefix = request()->route()->getName();
        $this->currentRoute  = (string) str_replace(panel_name().'.', '', $routeNameWithPrefix);

        $patterns = explode('.', $this->currentRoute);

        $this->currentPrefix = $patterns[0];

        $routeUriWithPrefix = request()->route()->uri();
        $this->currentUri   = (string) str_replace(panel_name().'/', '', $routeUriWithPrefix);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return mixed
     */
    public function render(): mixed
    {
        $this->menuLinks = $this->handleMenus($this->getMenus());

        return view('panel::components.sidebar');
    }

    /**
     * Get all menus
     */
    private function getMenus(): array
    {
        $menus = [
            [
                'route' => 'home.index',
                'title' => __('panel::menu.dashboard'),
                'icon'  => 'bi-house',
            ],
            [
                'title'    => __('panel::menu.top_content'),
                'icon'     => 'bi-sticky',
                'prefixes' => ['articles', 'catalogs', 'tags', 'pages'],
                'children' => $this->getContentSubRoutes(),
            ],
            [
                'title'    => __('panel::menu.top_analytic'),
                'icon'     => 'bi-bar-chart',
                'prefixes' => ['analytics'],
                'children' => $this->getAnalyticSubRoutes(),
            ],
            [
                'title'    => __('panel::menu.top_design'),
                'icon'     => 'bi-palette',
                'children' => $this->getDesignSubRoutes(),
            ],
            [
                'title'    => __('panel::menu.top_plugin'),
                'icon'     => 'bi-puzzle',
                'children' => $this->getPluginSubRoutes(),
            ],
            [
                'title'    => __('panel::menu.top_setting'),
                'icon'     => 'bi-gear',
                'children' => $this->getSettingSubRoutes(),
            ],
        ];

        return fire_hook_filter('component.sidebar.menus', $menus);
    }

    /**
     * Handle menus like whether check or not.
     */
    private function handleMenus($links): array
    {
        $result = [];
        foreach ($links as $index => $link) {
            $topUrl   = $link['url']   ?? '';
            $topRoute = $link['route'] ?? '';
            if (empty($topUrl) && $topRoute) {
                $link['url'] = panel_route($topRoute);
            }

            $parentChecked = false;
            if ($this->checkChildActive($topRoute)) {
                $parentChecked = true;
            }

            $prefixes = $link['prefixes'] ?? [];
            $children = $link['children'] ?? [];

            $link['has_children'] = (bool) $children;
            foreach ($children as $key => $item) {
                $code = str_replace('.', '_', $item['route']);
                if (! $this->adminUser->can($code)) {
                    unset($link['children'][$key]);

                    continue;
                }

                $url = $item['url'] ?? '';
                if (empty($url)) {
                    $item['url'] = panel_route($item['route']);
                }
                if ($this->checkChildActive($item['route'])) {
                    $item['active'] = true;
                    $parentChecked  = true;
                } else {
                    $item['active'] = false;
                }

                if (! isset($item['blank'])) {
                    $item['blank'] = false;
                }
                $link['children'][$key] = $item;
            }

            if (! $parentChecked && $this->checkParentActive($prefixes)) {
                $parentChecked = true;
            }

            if ($topRoute != 'home.index' && empty($link['children'])) {
                unset($link);
            }

            if (isset($link) && $link) {
                $result[$index]           = $link;
                $result[$index]['active'] = $parentChecked;
            }
        }

        return $result;
    }

    /**
     * @param  $route
     * @return bool
     */
    private function checkChildActive($route): bool
    {
        if ($route == $this->currentRoute) {
            return true;
        }

        $routePart   = substr($route, 0, strpos($route, '.'));
        $currentPath = substr($this->currentRoute, 0, strpos($this->currentRoute, '.'));
        if ($routePart == $currentPath) {
            return true;
        }

        return false;
    }

    /**
     * @param  $prefixes
     * @return bool
     */
    private function checkParentActive($prefixes): bool
    {
        if ($prefixes && in_array($this->currentPrefix, $prefixes)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get article sub routes
     */
    public function getAnalyticSubRoutes(): array
    {
        $routes = [
            ['route' => 'analytics.index', 'title' => __('panel::menu.analytics')],
        ];

        return fire_hook_filter('component.sidebar.analytic.routes', $routes);
    }

    /**
     * Get content sub routes
     */
    public function getContentSubRoutes(): array
    {
        $routes = [
            ['route' => 'articles.index', 'title' => __('panel::menu.articles')],
            ['route' => 'catalogs.index', 'title' => __('panel::menu.catalogs')],
            ['route' => 'tags.index', 'title' => __('panel::menu.tags')],
            ['route' => 'pages.index', 'title' => __('panel::menu.pages')],
        ];

        return fire_hook_filter('component.sidebar.content.routes', $routes);
    }

    /**
     * Get design sub routes.
     */
    public function getDesignSubRoutes(): array
    {
        $routes = [
            ['route' => 'themes_settings.index', 'title' => __('panel::menu.themes_settings')],
            ['route' => 'themes.index', 'title' => __('panel::menu.themes')],
        ];

        return fire_hook_filter('component.sidebar.design.routes', $routes);
    }

    /**
     * Get design sub routes.
     */
    public function getPluginSubRoutes(): array
    {
        $routes = [
            ['route' => 'plugins.index', 'title' => __('panel::menu.plugins')],
        ];

        return fire_hook_filter('component.sidebar.plugin.routes', $routes);
    }

    /**
     * Get setting sub routes.
     */
    public function getSettingSubRoutes(): array
    {
        $routes = [
            ['route' => 'settings.index', 'title' => __('panel::menu.settings')],
            ['route' => 'account.index', 'title' => __('panel::menu.account')],
            ['route' => 'admins.index', 'title' => __('panel::menu.admins')],
            ['route' => 'roles.index', 'title' => __('panel::menu.roles')],
            ['route' => 'locales.index', 'title' => __('panel::menu.locales')],
        ];

        return fire_hook_filter('component.sidebar.setting.routes', $routes);
    }
}
