<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\RestAPI\PanelApiControllers;

use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Unauthenticated;

#[Group('Panel - Introduction')]
class IntroductionController extends BaseController
{
    #[Endpoint('Panel API base info')]
    #[Unauthenticated]
    public function index(): string
    {
        return 'This is Panel Restful APIs for '.innocms_version();
    }
}
