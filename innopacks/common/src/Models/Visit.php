<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Common\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use InnoCMS\Common\Models\BaseModel;

class Visit extends BaseModel
{
    protected $table = 'visits';

    protected $fillable = [
        'session_id',
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

    /**
     * Get visit events relationship.
     *
     * @return HasMany
     */
    public function visitEvents(): HasMany
    {
        return $this->hasMany(VisitEvent::class, 'session_id', 'session_id');
    }

    /**
     * Get device type display name.
     *
     * @return string
     */
    public function getDeviceTypeDisplayAttribute(): string
    {
        $types = [
            'desktop' => 'Desktop',
            'mobile'  => 'Mobile',
            'tablet'  => 'Tablet',
        ];

        return $types[$this->device_type] ?? $this->device_type;
    }
}
