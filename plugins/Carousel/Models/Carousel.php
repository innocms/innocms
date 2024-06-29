<?php
/**
 * Copyright (c) Since 2024 InnoShop - All Rights Reserved
 *
 * @link       https://www.innoshop.com
 * @author     XING GUI YU <xingguiyu@foxmail.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Plugin\Carousel\Models;

use InnoCMS\Common\Models\BaseModel;
use InnoCMS\Common\Models\Page;

class Carousel extends BaseModel
{
    protected $table = 'carousels';

    protected $fillable = [
        'name',
        'page_id',
        'position',
        'style',
        'height',
        'order_index',
        'active',
        'auto_play',
        'with_controls',
        'with_indicators',
        'with_captions',
        'cross_fade',
        'dark_variant',
        'touch_swiping',
    ];

    public function images()
    {
        return $this->hasMany(CarouselImage::class);
    }

    public function page()
    {
        return $this->belongsTo(Page::class);
    }
}
