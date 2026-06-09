<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Common\Models\Tag;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use InnoCMS\Common\Models\BaseModel;

class Translation extends BaseModel
{
    protected $table = 'tag_translations';

    protected $fillable = [
        'tag_id', 'locale', 'name',
    ];

    public function tag(): BelongsTo
    {
        return $this->belongsTo(Tag::class);
    }
}
