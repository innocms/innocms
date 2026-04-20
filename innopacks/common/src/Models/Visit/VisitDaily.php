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

class VisitDaily extends BaseModel
{
    protected $table = 'visit_daily';

    protected $primaryKey = 'date';

    public $incrementing = false;

    protected $fillable = [
        'date',
        'pv',
        'uv',
        'ip',
        'new_visitors',
        'bounces',
        'avg_duration',
        'desktop_pv',
        'mobile_pv',
        'tablet_pv',
    ];

    protected $casts = [
        'date' => 'date',
    ];
}
