<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Common\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;

trait Translatable
{
    /**
     * 设置 Description model
     * @return string
     */
    public function getDescriptionModelClass(): string
    {
        return self::class.'Translation';
    }

    /**
     * Define translations relationship
     *
     * @return HasMany
     */
    public function translations(): HasMany
    {
        $class = $this->getDescriptionModelClass();

        return $this->hasMany($class, $this->getForeignKey(), $this->getKeyName());
    }

    /**
     * Locale translation object
     *
     * @return mixed
     * @throws \Exception
     */
    public function translation(): mixed
    {
        $class = $this->getDescriptionModelClass();

        return $this->hasOne($class, $this->getForeignKey(), $this->getKeyName())
            ->where('locale', locale());
    }

    /**
     * Translate field by locale
     *
     * @param  $locale
     * @param  $field
     * @return string
     */
    public function translate($locale, $field): string
    {
        return $this->translations->keyBy('locale')[$locale][$field] ?? '';
    }
}
