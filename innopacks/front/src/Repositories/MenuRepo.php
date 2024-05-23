<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Front\Repositories;

use InnoCMS\Common\Repositories\CatalogRepo;
use InnoCMS\Common\Repositories\PageRepo;
use InnoCMS\Common\Resources\CatalogSimple;
use InnoCMS\Common\Resources\PageSimple;

class MenuRepo
{
    /**
     * @return static
     */
    public static function getInstance(): static
    {
        return new static;
    }

    /**
     * Generate header menus for frontend.
     *
     * @return array
     * @throws \Exception
     */
    public function getMenus(): array
    {
        $catalogs = $this->getCatalogs();
        $pages    = $this->getPages();
        $menus    = array_merge($catalogs, $pages);

        return fire_hook_filter('global.header.menus', $menus);
    }

    /**
     * @return array
     * @throws \Exception
     */
    private function getCatalogs(): array
    {
        $catalogs = CatalogRepo::getInstance()
            ->builder(['active' => true, 'parent_id' => 0])
            ->orderBy('position')
            ->get();

        return CatalogSimple::collection($catalogs)->jsonSerialize();
    }

    /**
     * @return array
     * @throws \Exception
     */
    private function getPages(): array
    {
        $catalogs = PageRepo::getInstance()
            ->builder(['active' => true])
            ->orderBy('position')
            ->get();

        return PageSimple::collection($catalogs)->jsonSerialize();
    }
}
