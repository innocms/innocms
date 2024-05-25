<?php
/**
 * Copyright (c) Since 2024 InnoShop - All Rights Reserved
 *
 * @link       https://www.innoshop.com
 * @author     InnoShop <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoShop\Plugin\Models;

use InnoCMS\Common\Models\BaseModel;

class Setting extends BaseModel
{
    protected $fillable = [
        'space', 'name', 'value', 'json',
    ];
}
