<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Common\Models\Visit;

use Illuminate\Database\Eloquent\Relations\HasMany;
use InnoCMS\Common\Models\BaseModel;

class Visit extends BaseModel
{
    protected $table = 'visits';

    protected $fillable = [
        'session_id',
        'customer_id',
        'ip_address',
        'user_agent',
        'country_code',
        'country_name',
        'city',
        'referrer',
        'device_type',
        'browser',
        'os',
        'locale',
        'first_visited_at',
        'last_visited_at',
    ];

    protected $casts = [
        'first_visited_at' => 'datetime',
        'last_visited_at'  => 'datetime',
    ];

    public function visitEvents(): HasMany
    {
        return $this->hasMany(VisitEvent::class, 'session_id', 'session_id');
    }
}
