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
use InnoCMS\Panel\Repositories\RouteRepo;
use InnoCMS\Panel\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;

class RouteRepoTest extends TestCase
{
    /**
     * A panel route registered with a closure has no controller class, so the
     * permission tree must still be buildable instead of blowing up with
     * "Undefined array key 1" inside RouteRepo::getPluginNamespace().
     */
    #[Test]
    public function it_builds_the_permission_tree_when_a_panel_route_uses_a_closure(): void
    {
        Route::get('/panel/closure-probe', fn () => 'ok')->name('panel.closure_probe.index');

        $permissions = RouteRepo::getInstance($this->makeRole())->getPanelPermissions();

        $this->assertContains('closure_probe_index', $this->collectRouteSlugs($permissions));
    }

    /**
     * A closure route cannot be mapped to a plugin namespace, so it belongs to
     * the system module group and its label comes from the panel route file.
     */
    #[Test]
    public function it_treats_a_closure_route_as_a_system_module(): void
    {
        Route::get('/panel/closure-probe', fn () => 'ok')->name('panel.closure_probe.index');

        $permissions = RouteRepo::getInstance($this->makeRole())->getPanelPermissions();

        $module = $this->findModule($permissions, 'closure_probe');
        $this->assertNotNull($module, 'Closure route module is missing from the permission tree.');
        $this->assertFalse($module['is_plugin'], 'Closure route module must be treated as a system module.');

        $item = $this->findPermission($module['permissions'], 'closure_probe_index');
        $this->assertNotNull($item, 'Closure route is missing from the permission tree.');
        $this->assertSame(__('panel/route.closure_probe_index'), $item['label']);
    }

    /**
     * The panel language files live in the application lang directory, so they
     * have to be resolved with the "panel/..." group syntax. The "panel::..."
     * namespace was never registered and always fell back to the raw key.
     */
    #[Test]
    public function it_resolves_system_module_labels_from_the_panel_language_files(): void
    {
        $permissions = RouteRepo::getInstance($this->makeRole())->getPanelPermissions();

        $module = $this->findModule($permissions, 'articles');
        $this->assertNotNull($module, 'System module is missing from the permission tree.');
        $this->assertSame(__('panel/menu.articles'), $module['label']);
        $this->assertNotSame('panel::menu.articles', $module['label']);

        $item = $this->findPermission($module['permissions'], 'articles_index');
        $this->assertNotNull($item, 'System route is missing from the permission tree.');
        $this->assertSame(__('panel/route.articles_index'), $item['label']);
        $this->assertNotSame('panel::route.articles_index', $item['label']);
    }

    /**
     * Core packages ship their own language namespace, so their labels must not
     * fall back to the core language files nor to an unknown namespace.
     */
    #[Test]
    public function it_resolves_core_package_labels_from_the_package_language_namespace(): void
    {
        $permissions = RouteRepo::getInstance($this->makeRole())->getPanelPermissions();

        $module = $this->findModule($permissions, 'content_ai');
        $this->assertNotNull($module, 'Core package module is missing from the permission tree.');
        $this->assertFalse($module['is_plugin'], 'Core package routes must not be flagged as plugin routes.');
        $this->assertSame(trans('aicore::route.title'), $module['label']);
        $this->assertNotSame('aicore::route.title', $module['label'], 'The package must ship its own route language file.');

        $item = $this->findPermission($module['permissions'], 'content_ai_generate');
        $this->assertNotNull($item, 'Core package route is missing from the permission tree.');
        $this->assertSame(trans('aicore::route.content_ai_generate'), $item['label']);
        $this->assertNotSame('aicore::route.content_ai_generate', $item['label']);
    }

    /**
     * Plugin labels must point at the plugin language namespace, which is the
     * plugin directory name and not a guess built from the route name.
     */
    #[Test]
    public function it_resolves_plugin_labels_from_the_plugin_language_namespace(): void
    {
        Route::get('/panel/demo-probe', 'Plugin\DemoPlugin\Controllers\Panel\DemoController@index')
            ->name('panel.demo_probe.index');

        $permissions = RouteRepo::getInstance($this->makeRole())->getPanelPermissions();

        $module = $this->findModule($permissions, 'demo_probe');
        $this->assertNotNull($module, 'Plugin module is missing from the permission tree.');
        $this->assertTrue($module['is_plugin'], 'Plugin routes must be flagged as plugin routes.');
        $this->assertSame(trans('DemoPlugin::route.title'), $module['label']);
        $this->assertNotSame('DemoProbe::route.title', $module['label'], 'The plugin namespace must not be guessed from the route name.');

        $item = $this->findPermission($module['permissions'], 'demo_probe_index');
        $this->assertNotNull($item, 'Plugin route is missing from the permission tree.');
        $this->assertSame(trans('DemoPlugin::route.demo_probe_index'), $item['label']);
    }

    /**
     * A module level title key wins over the namespace wide one, so a plugin
     * shipping several modules can name each of them.
     */
    #[Test]
    public function it_prefers_the_module_level_title_key(): void
    {
        app('translator')->addNamespace('FakeNs', __DIR__.'/../fixtures/lang');

        Route::get('/panel/fake-ns', 'Plugin\FakeNs\Controllers\DemoController@index')
            ->name('panel.fake_ns.index');

        $permissions = RouteRepo::getInstance($this->makeRole())->getPanelPermissions();

        $module = $this->findModule($permissions, 'fake_ns');
        $this->assertNotNull($module, 'Module is missing from the permission tree.');
        $this->assertSame('Fake Module', $module['label']);

        $item = $this->findPermission($module['permissions'], 'fake_ns_index');
        $this->assertNotNull($item);
        $this->assertSame('Fake Item', $item['label']);
    }

    /**
     * A module mixing plugin controllers with one legacy closure redirect is
     * still a plugin module.
     */
    #[Test]
    public function it_flags_a_mixed_module_as_a_plugin_module(): void
    {
        Route::get('/panel/mix-probe', 'Plugin\MixPlugin\Controllers\DemoController@index')
            ->name('panel.mix_probe.index');
        Route::post('/panel/mix-probe/legacy', fn () => 'ok')
            ->name('panel.mix_probe.legacy');

        $permissions = RouteRepo::getInstance($this->makeRole())->getPanelPermissions();

        $module = $this->findModule($permissions, 'mix_probe');
        $this->assertNotNull($module, 'Module is missing from the permission tree.');
        $this->assertTrue($module['is_plugin'], 'A module with plugin routes must be flagged as a plugin module.');
    }

    /**
     * Themes and plugins own their wording, they must be able to provide it
     * without the core knowing anything about them.
     */
    #[Test]
    public function it_lets_themes_and_plugins_provide_their_own_label(): void
    {
        Route::get('/panel/closure-probe', fn () => 'ok')->name('panel.closure_probe.index');

        $callback = function (array $data): array {
            if ($data['module'] === 'closure_probe') {
                $data['label'] = 'Probe Label';
            }

            return $data;
        };
        app('eventy')->addFilter('panel.permission.label', $callback);

        try {
            $permissions = RouteRepo::getInstance($this->makeRole())->getPanelPermissions();

            $module = $this->findModule($permissions, 'closure_probe');
            $this->assertNotNull($module);
            $this->assertSame('Probe Label', $module['label']);
        } finally {
            app('eventy')->removeFilter('panel.permission.label', $callback);
        }
    }

    /**
     * @return array<int, string>
     */
    private function collectRouteSlugs(array $permissions): array
    {
        $slugs = [];
        foreach ($permissions as $module) {
            foreach ($module['permissions'] as $permission) {
                $slugs[] = $permission['route_slug'];
            }
        }

        return $slugs;
    }

    /**
     * @param  array<int, array<string, mixed>>  $permissions
     * @return array<string, mixed>|null
     */
    private function findModule(array $permissions, string $module): ?array
    {
        foreach ($permissions as $group) {
            if ($group['module'] === $module) {
                return $group;
            }
        }

        return null;
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @return array<string, mixed>|null
     */
    private function findPermission(array $items, string $slug): ?array
    {
        foreach ($items as $item) {
            if ($item['route_slug'] === $slug) {
                return $item;
            }
        }

        return null;
    }

    private function makeRole(): Role
    {
        $role = $this->createMock(Role::class);
        $role->method('hasPermissionTo')->willReturn(false);

        return $role;
    }
}
