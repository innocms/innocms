<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     XING GUI YU <xingguiyu@foxmail.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Plugin\Carousel\Models;

use InnoCMS\Common\Models\Page as InnoCmsPage;

class Page extends InnoCmsPage
{
    public function carousels()
    {
        return $this->hasMany(Carousel::class);
    }
}
