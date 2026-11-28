<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Panel\Tests\Feature;

use Illuminate\Support\Facades\Route;
use InnoCMS\Panel\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * A resource route registers methods the controller may not have. The panel controllers have
 * no read-only detail page, so their resource routes must exclude "show", and the plugin
 * routes must exclude "create" and "show". Otherwise a direct visit answers 500.
 */
class PanelRoutesTest extends TestCase
{
    /**
     * Resources whose controller has no show() method.
     */
    private const WITHOUT_SHOW = ['articles', 'catalogs', 'pages', 'tags', 'products', 'categories'];

    #[Test]
    public function every_route_points_at_an_existing_controller_method(): void
    {
        $broken = [];

        foreach (Route::getRoutes() as $route) {
            $action = $route->getAction('uses');

            if (! is_string($action) || ! str_contains($action, '@')) {
                continue;
            }

            [$controller, $method] = explode('@', $action, 2);

            if (! class_exists($controller) || ! method_exists($controller, $method)) {
                $broken[] = ($route->getName() ?: $route->uri()).' -> '.$controller.'::'.$method.'()';
            }
        }

        $this->assertSame([], $broken, "Routes point at methods that do not exist:\n".implode("\n", $broken));
    }

    #[Test]
    public function the_content_resources_have_no_show_route(): void
    {
        foreach (self::WITHOUT_SHOW as $resource) {
            $this->assertNull(
                Route::getRoutes()->getByName('panel.'.$resource.'.show'),
                "The $resource resource must not register a show route: the controller has no show() method."
            );
            $this->assertNotNull(
                Route::getRoutes()->getByName('panel.'.$resource.'.edit'),
                "The $resource resource must keep its edit route."
            );
        }
    }

    /**
     * The plugin pack registers its routes only when the plugins table exists, which is not
     * the case while the providers boot in the test environment. The routes are therefore
     * checked from the route file itself.
     */
    #[Test]
    public function the_plugin_routes_have_no_create_or_show_route(): void
    {
        $file = base_path('innopacks/plugin/routes/web.php');
        $this->assertFileExists($file);

        $source = file_get_contents($file);

        $this->assertMatchesRegularExpression(
            "/Route::resource\('\/plugins',\s*PluginController::class\)->except\(\['create',\s*'show'\]\)/",
            $source,
            'The plugin resource route must exclude create and show: the controller has neither method.'
        );
    }

    /**
     * Every route a resource registers must exist on the controller.
     *
     * The route names of a pack depend on the state of the site (the front routes carry a
     * locale prefix, the AI routes exist only when AI is enabled), so the route names
     * cannot be checked in the test environment. What can be checked is the pair of
     * controller and method a route file names: that is what breaks a direct visit.
     */
    #[Test]
    public function every_route_of_a_pack_points_at_an_existing_controller_method(): void
    {
        $broken = [];

        foreach ($this->routeFiles() as $file) {
            $source = file_get_contents($file);

            // Route::resource('/articles', Controllers\ArticleController::class)
            preg_match_all("/Route::resource\(\s*'([^']+)'\s*,\s*([A-Za-z0-9_\\\\]+)::class\s*\)([^;]*);/", $source, $resources, PREG_SET_ORDER);

            foreach ($resources as [, $uri, $shortClass, $chain]) {
                $controller = $this->resolveControllerClass($file, $shortClass);

                if ($controller === null) {
                    continue;
                }

                $excluded = $this->excludedActions($chain);

                foreach (['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'] as $action) {
                    if (in_array($action, $excluded, true)) {
                        continue;
                    }

                    if (! method_exists($controller, $action)) {
                        $broken[] = str_replace(base_path().'/', '', $file)." -> $uri/$action -> $controller::$action()";
                    }
                }
            }

            // Route::get('/x', [Controllers\FooController::class, 'bar'])
            preg_match_all("/Route::(get|post|put|patch|delete)\(\s*'[^']*'\s*,\s*\[([A-Za-z0-9_\\\\]+)::class\s*,\s*'([a-zA-Z]+)'\]/", $source, $methods, PREG_SET_ORDER);

            foreach ($methods as [$verb, $shortClass, $method]) {
                $controller = $this->resolveControllerClass($file, $shortClass);

                if ($controller === null || method_exists($controller, $method)) {
                    continue;
                }

                $broken[] = str_replace(base_path().'/', '', $file)." -> $verb -> $controller::$method()";
            }
        }

        $this->assertSame([], $broken, "Routes point at methods that do not exist:\n".implode("\n", $broken));
    }

    /**
     * @return array<string>
     */
    private function routeFiles(): array
    {
        $files = [];
        foreach (glob(base_path('innopacks/*/routes/*.php')) as $file) {
            $files[] = $file;
        }

        return $files;
    }

    /**
     * The controller classes a route file names, with their namespace resolved.
     *
     * @param  string  $file
     * @return array<string>
     */
    private function controllerClassesIn(string $file): array
    {
        $source = file_get_contents($file);
        $uses   = [];

        preg_match_all('/^use\s+([A-Za-z0-9_\\\\]+)(?:\s+as\s+([A-Za-z0-9_]+))?;/m', $source, $imports, PREG_SET_ORDER);

        foreach ($imports as [$full, $alias]) {
            $uses[$alias !== '' ? $alias : substr($full, strrpos($full, '\\') + 1)] = $full;
        }

        $classes = [];
        preg_match_all('/([A-Za-z0-9_\\\\]+)::class/', $source, $references);

        foreach ($references[1] as $reference) {
            $class = $uses[$reference] ?? null;

            if ($class === null) {
                $namespace = $this->namespaceOf($file);
                $class     = $namespace ? $namespace.'\\'.$reference : $reference;
            }

            $classes[] = $class;
        }

        return array_values(array_unique($classes));
    }

    /**
     * The controller of a resource route, or null when it cannot be resolved.
     */
    private function resolveControllerClass(string $file, string $shortClass): ?string
    {
        $classes = $this->controllerClassesIn($file);

        foreach ($classes as $class) {
            if (str_ends_with($class, '\\'.$shortClass)) {
                return $class;
            }
        }

        return null;
    }

    /**
     * The actions a resource route excludes, for example ->except(['show']).
     *
     * @return array<string>
     */
    private function excludedActions(string $chain): array
    {
        if (! preg_match('/->except\(\s*\[([^\]]*)\]/', $chain, $match)) {
            return [];
        }

        preg_match_all("/'([a-z_]+)'/", $match[1], $actions);

        return $actions[1];
    }

    private function namespaceOf(string $file): string
    {
        $source = file_get_contents($file);

        if (preg_match('/^namespace\s+([^;]+);/m', $source, $match)) {
            return trim($match[1]);
        }

        return '';
    }
}
