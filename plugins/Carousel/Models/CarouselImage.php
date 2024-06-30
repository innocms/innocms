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

class CarouselImage extends BaseModel
{
    protected $table = 'carousel_images';

    protected $fillable = [
        'carousel_id',
        'title',
        'description',
        'image_url',
        'target_url',
        'position',
        'active',
        'item_interval',
    ];

    public function carousel()
    {
        return $this->belongsTo(Carousel::class);
    }
}
