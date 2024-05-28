<?php
/**
 * Copyright (c) Since 2024 InnoShop - All Rights Reserved
 *
 * @link       https://www.innoshop.com
 * @author     InnoShop <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Plugin\PartnerLink\Models;

use InnoCMS\Common\Models\BaseModel;

class PartnerLink extends BaseModel
{
    protected $table = 'partner_links';

    protected $fillable = [
        'name', 'url', 'logo', 'position', 'active',
    ];
}
