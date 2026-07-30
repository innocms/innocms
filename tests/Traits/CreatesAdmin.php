<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Tests\Traits;

use InnoCMS\Common\Models\Admin;

trait CreatesAdmin
{
    protected function createAdmin(array $attributes = []): Admin
    {
        return Admin::query()->create([
            'name'     => $attributes['name'] ?? 'test-admin',
            'email'    => $attributes['email'] ?? 'test-admin@example.com',
            'password' => bcrypt('password'),
            'active'   => true,
            'locale'   => 'zh-cn',
        ] + $attributes);
    }
}
