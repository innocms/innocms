<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Common\Repositories;

use Illuminate\Database\Eloquent\Builder;
use InnoCMS\Common\Models\Locale;

class LocaleRepo extends BaseRepo
{
    /**
     * @throws \Exception
     */
    public function getListWithPath(): array
    {
        $languages = Locale::all()->keyBy('code')->toArray();

        $result = [];
        foreach (front_lang_path_codes() as $localeCode) {
            $langFile = inno_path("panel/lang/$localeCode/base.php");
            if (! is_file($langFile)) {
                throw new \Exception("File ($langFile) not exist!");
            }
            $baseData = require $langFile;
            $name     = $baseData['name'] ?? $localeCode;
            $result[] = [
                'code'     => $localeCode,
                'name'     => $name,
                'id'       => $languages[$localeCode]['id']       ?? 0,
                'image'    => $languages[$localeCode]['image']    ?? "images/flag/$localeCode.png",
                'position' => $languages[$localeCode]['position'] ?? 0,
                'active'   => $languages[$localeCode]['active']   ?? true,
            ];
        }

        return $result;
    }

    /**
     * @param  array  $filters
     * @return Builder
     * @throws \Exception
     */
    public function builder(array $filters = []): Builder
    {
        $builder = Locale::query();

        $code = $filters['code'] ?? '';
        if ($code) {
            $builder->where('code', $code);
        }

        if (isset($filters['active'])) {
            $builder->where('active', (bool) $filters['active']);
        }

        return $builder;
    }

    /**
     * Get active list.
     *
     * @return mixed
     * @throws \Exception
     */
    public function getActiveList(): mixed
    {
        return $this->builder(['active' => true])->get();
    }
}
