<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Common\Models\Visit;

use InnoCMS\Common\Models\BaseModel;

class ConversionDaily extends BaseModel
{
    protected $table = 'conversion_daily';

    protected $primaryKey = 'date';

    public $incrementing = false;

    protected $fillable = [
        'date',
        'home_views',
        'catalog_views',
        'article_views',
        'searches',
        'overall_conversion_rate',
    ];

    protected $casts = [
        'date' => 'date',
    ];
}
