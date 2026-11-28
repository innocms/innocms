<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Panel\Repositories;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;
use Spatie\Permission\Models\Role;

/** @phpstan-consistent-constructor */
class RouteRepo
{
    const IGNORE_LIST = [
        'login.index', 'login.store', 'logout.index', 'dashboard.index', 'locale.switch',
    ];

    private Role $adminRole;

    /**
     * @param  Role  $adminRole
     */
    public function __construct(Role $adminRole)
    {
        $this->adminRole = $adminRole;
    }

    /**
     * @param  Role  $adminRole
     * @return static
     */
    public static function getInstance(Role $adminRole): static
    {
        return new static($adminRole);
    }

    /**
     * @return array
     */
    public function getPanelPermissions(): array
    {
        $permissions = [];
        $panelRoutes = $this->getPanelRoutes();
        foreach ($panelRoutes as $module => $routes) {
            $namespace = $this->getModuleNamespace($routes);

            $permissions[] = [
                'is_plugin'   => $this->isPluginModule($routes),
                'module'      => $module,
                'label'       => $this->resolveModuleLabel($namespace, $module),
                'permissions' => $this->getPermissionList($module, $routes),
            ];
        }

        return $permissions;
    }

    /**
     * @return array
     */
    public function getPanelRoutes(): array
    {
        $routeList = [];
        $routes    = Route::getRoutes();
        foreach ($routes as $route) {
            $routeName = $route->getName();
            if (! Str::startsWith($route->getName(), 'panel.')) {
                continue;
            }

            $routeName = substr($routeName, strlen('panel.'));
            if (in_array($routeName, self::IGNORE_LIST)) {
                continue;
            }

            $parseNames  = explode('.', $routeName);
            $routeModule = $parseNames[0];
            $controller  = $route->getControllerClass();

            $routeList[$routeModule][] = [
                'route_name'  => $routeName,
                'action_name' => $controller,
                'namespace'   => $this->getRouteNamespace($controller),
                'is_plugin'   => $this->isPluginController($controller),
            ];
        }

        return $routeList;
    }

    /**
     * Resolve the translation namespace owning the route label.
     *
     * An empty namespace means the core panel language files. Core packages
     * and plugins ship their own language namespace, so their labels stay in
     * their own repository instead of the core one.
     *
     * @param  string|null  $controller
     * @return string
     */
    private function getRouteNamespace($controller): string
    {
        // Closure routes have no controller class, they fall back to the core language files.
        if (! is_string($controller) || $controller === '') {
            return '';
        }

        if (Str::startsWith($controller, 'InnoCMS\\Panel')
            || Str::startsWith($controller, 'InnoCMS\\Plugin')) {
            return '';
        }

        $parts = explode('\\', $controller);
        if (count($parts) < 2) {
            return '';
        }

        if ($parts[0] === 'InnoCMS') {
            // Core package, e.g. InnoCMS\Aicore -> aicore language namespace.
            return strtolower($parts[1]);
        }

        return $parts[1];
    }

    /**
     * @param  string|null  $controller
     * @return bool
     */
    private function isPluginController($controller): bool
    {
        return is_string($controller) && Str::startsWith($controller, 'Plugin\\');
    }

    /**
     * @param  array  $routes
     * @return string
     */
    private function getModuleNamespace(array $routes): string
    {
        foreach ($routes as $route) {
            if ($route['namespace'] !== '') {
                return $route['namespace'];
            }
        }

        return '';
    }

    /**
     * A module counts as a plugin module when any of its routes belongs to a
     * plugin, so one legacy closure redirect does not hide the plugin origin.
     *
     * @param  array  $routes
     * @return bool
     */
    private function isPluginModule(array $routes): bool
    {
        foreach ($routes as $route) {
            if ($route['is_plugin']) {
                return true;
            }
        }

        return false;
    }

    /**
     * Resolve the module label. A module level key wins over the namespace wide
     * one, so a plugin shipping several modules can name each of them.
     *
     * @param  string  $namespace
     * @param  string  $module
     * @return string
     */
    private function resolveModuleLabel(string $namespace, string $module): string
    {
        if ($namespace === '') {
            return $this->resolveLabel("panel/menu.$module", $module, null);
        }

        $specificKey = "{$namespace}::route.title_{$module}";
        if (trans($specificKey) !== $specificKey) {
            return $this->resolveLabel($specificKey, $module, null);
        }

        return $this->resolveLabel("{$namespace}::route.title", $module, null);
    }

    /**
     * Resolve a permission label, allowing themes and plugins to provide their
     * own wording without the core knowing anything about them.
     *
     * @param  string  $translationKey
     * @param  string  $module
     * @param  string|null  $routeName
     * @return string
     */
    private function resolveLabel(string $translationKey, string $module, ?string $routeName): string
    {
        $label = trans($translationKey);
        if (! is_string($label)) {
            // A partially nested language file would return an array here.
            $label = $routeName ?? $module;
        }

        $filtered = fire_hook_filter('panel.permission.label', [
            'translation_key' => $translationKey,
            'label'           => $label,
            'module'          => $module,
            'route'           => $routeName,
        ]);

        if (is_array($filtered) && isset($filtered['label']) && is_string($filtered['label'])) {
            return $filtered['label'];
        }

        return $label;
    }

    /**
     * Get permission list by module and route.
     *
     * @param  string  $module
     * @param  array  $routes
     * @return array
     */
    private function getPermissionList(string $module, array $routes): array
    {
        $items = [];
        foreach ($routes as $route) {
            $routeName = $route['route_name'];
            $namespace = $route['namespace'];

            $routeSlug = str_replace('.', '_', $routeName);
            $labelKey  = $namespace === ''
                ? "panel/route.{$routeSlug}"
                : "{$namespace}::route.{$routeSlug}";

            $items[] = [
                'route_slug' => $routeSlug,
                'label'      => $this->resolveLabel($labelKey, $module, $routeName),
                'selected'   => $this->hasPermission($routeSlug),
            ];
        }

        return $items;
    }

    /**
     * Detect current user or role has permission.
     *
     * @param  $permission
     * @return bool
     */
    private function hasPermission(string $permission): bool
    {
        try {
            return $this->adminRole->hasPermissionTo($permission);
        } catch (PermissionDoesNotExist $e) {
            Log::error($e->getMessage());

            return false;
        }
    }
}
