<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Common\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use InnoCMS\Common\Repositories\SettingRepo;
use InnoCMS\Common\Support\DebugMode;
use InnoCMS\Common\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class DebugModeTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_overrides_app_debug_when_setting_saved()
    {
        SettingRepo::getInstance()->updateSystemValue('debug', '1');
        (new DebugMode)->apply($this->app);
        $this->assertTrue(config('app.debug'));

        SettingRepo::getInstance()->updateSystemValue('debug', '0');
        (new DebugMode)->apply($this->app);
        $this->assertFalse(config('app.debug'));
    }

    #[Test]
    public function it_keeps_env_value_when_setting_missing()
    {
        config(['app.debug' => true]);
        (new DebugMode)->apply($this->app);
        $this->assertTrue(config('app.debug'));

        config(['app.debug' => false]);
        (new DebugMode)->apply($this->app);
        $this->assertFalse(config('app.debug'));
    }
}
