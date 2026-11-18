<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Common\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * In-site notification (ported down from the platform package, with the tenant column removed).
 *
 * The writer only inserts pending rows; sending/retrying is consumed by the downstream event bus.
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $admin_user_id
 * @property int|null $worker_id
 * @property string $channel
 * @property string $event
 * @property string $title
 * @property string|null $content
 * @property array|null $payload
 * @property string $status
 * @property Carbon|null $sent_at
 * @property Carbon|null $read_at
 * @property Carbon $created_at
 */
class Notification extends Model
{
    public const CHANNEL_SYSTEM = 'system';

    protected $fillable = [
        'user_id', 'admin_user_id', 'worker_id',
        'channel', 'event', 'title', 'content', 'payload',
        'status', 'sent_at', 'read_at', 'created_at', 'updated_at',
    ];

    protected $casts = [
        'payload'    => 'array',
        'sent_at'    => 'datetime',
        'read_at'    => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Pending send queue.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }
}
