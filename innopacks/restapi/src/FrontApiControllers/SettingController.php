<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\RestAPI\FrontApiControllers;

use Exception;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Unauthenticated;

#[Group('Front - Settings')]
class SettingController extends BaseController
{
    /**
     * @return mixed
     * @throws Exception
     */
    #[Endpoint('Get system settings')]
    #[Unauthenticated]
    public function index(): mixed
    {
        $system   = setting('system', []);
        $settings = is_array($system) ? $system : [];

        $settings['locales'] = locales()->map(fn ($locale) => [
            'name' => $locale->name,
            'code' => $locale->code,
        ])->values()->all();

        return read_json_success($settings);
    }
}
