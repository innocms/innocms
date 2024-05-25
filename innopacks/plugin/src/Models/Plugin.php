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

class Plugin extends BaseModel
{
    protected $table = 'plugins';

    protected $fillable = ['type', 'code', 'priority'];
}
