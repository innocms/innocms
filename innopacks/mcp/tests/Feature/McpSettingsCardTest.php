<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Mcp\Tests\Feature;

use Database\Seeders\LocaleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InnoCMS\Common\Repositories\SettingRepo;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class McpSettingsCardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(LocaleSeeder::class);
    }

    #[Test]
    public function test_tools_settings_page_renders_mcp_card(): void
    {
        $html = view('mcp::settings._tools')->render();

        $this->assertStringContainsString('name="mcp_enabled"', $html);
        $this->assertStringContainsString(url('/mcp'), $html);
        $this->assertStringContainsString('mcpServers', $html);
        $this->assertStringContainsString('claude mcp add', $html);
    }

    #[Test]
    public function test_mcp_enabled_setting_persists(): void
    {
        SettingRepo::getInstance()->updateSystemValue('mcp_enabled', 1);
        $this->assertDatabaseHas('settings', ['space' => 'system', 'name' => 'mcp_enabled', 'value' => '1']);

        SettingRepo::getInstance()->updateSystemValue('mcp_enabled', 0);
        $this->assertDatabaseHas('settings', ['space' => 'system', 'name' => 'mcp_enabled', 'value' => '0']);
    }
}
