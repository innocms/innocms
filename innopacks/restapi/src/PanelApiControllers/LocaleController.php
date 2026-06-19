<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\RestAPI\PanelApiControllers;

use Illuminate\Http\Request;
use InnoCMS\Common\Models\Locale;
use InnoCMS\Common\Repositories\LocaleRepo;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\QueryParam;
use Knuckles\Scribe\Attributes\UrlParam;

#[Group('Panel - Locales')]
class LocaleController extends BaseController
{
    /**
     * List locales installed on the system.
     * Merges disk-available locales (lang/<code>/common/base.php) with DB records,
     * so the response reflects what users actually see in the locale switcher.
     *
     * @param  Request  $request
     * @return mixed
     * @throws \Exception
     */
    #[Endpoint('List locales')]
    #[QueryParam('active', 'bool', required: false, description: 'Only active locales')]
    public function index(Request $request): mixed
    {
        $locales = LocaleRepo::getInstance()->getListWithPath();
        if ($request->boolean('active')) {
            $locales = array_values(array_filter($locales, fn ($l) => $l['active']));
        }

        return read_json_success($locales);
    }

    /**
     * Get a single locale by ID.
     *
     * @param  Locale  $locale
     * @return mixed
     */
    #[Endpoint('Get locale detail')]
    #[UrlParam('locale', 'integer', description: 'Locale ID')]
    public function show(Locale $locale): mixed
    {
        return read_json_success($locale);
    }
}
