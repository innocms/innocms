<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Common\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Comment extends BaseModel
{
    protected $table = 'comments';

    protected $fillable = [
        'commentable_type',
        'commentable_id',
        'author_name',
        'author_email',
        'content',
        'rating',
        'parent_id',
        'status',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    const STATUS_PENDING  = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_SPAM     = 'spam';

    /**
     * Get the commentable entity (article, page, etc.)
     */
    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the parent comment.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * Get the child comments (replies).
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    /**
     * Scope to get only approved comments.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope to get only pending comments.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Check if comment is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if comment is pending.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Get status display name.
     */
    public function getStatusDisplayAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_APPROVED => trans('panel/comment.status_approved'),
            self::STATUS_SPAM     => trans('panel/comment.status_spam'),
            default               => trans('panel/comment.status_pending'),
        };
    }
}
