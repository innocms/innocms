<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Common\Models;

class CatalogTranslation extends BaseModel
{
    protected $fillable = [
        'title', 'summary', 'locale', 'meta_title', 'meta_description', 'meta_keywords',
    ];
}
