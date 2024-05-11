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
use InnoCMS\Common\Traits\Translatable;

class Catalog extends BaseModel
{
    use Translatable;

    protected $fillable = [
        'parent_id', 'slug', 'position', 'active',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Catalog::class, 'parent_id', 'id');
    }
}
