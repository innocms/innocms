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
use InnoCMS\Common\Repositories\ProductRepo;
use InnoCMS\Common\Resources\CatalogSimple;
use InnoCMS\Common\Resources\PageSimple;

/** @phpstan-consistent-constructor */
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
        $products = $this->getProductsMenuItem();
        $contact  = $this->getContactMenuItem();
        $menus    = array_merge($products, $catalogs, $pages, $contact);

        return fire_hook_filter('global.header.menus', $menus);
    }

    /**
     * Built-in products entry. Only shown when the site actually has
     * product rows — the route existing alone made every site nav
     * carry a default entry regardless of whether they use the module.
     *
     * @return array
     */
    private function getProductsMenuItem(): array
    {
        if (! has_front_route('products.index')) {
            return [];
        }

        if (! ProductRepo::getInstance()->builder()->exists()) {
            return [];
        }

        return [
            [
                'name'     => __('front::common.products_title'),
                'url'      => front_route('products.index'),
                'slug'     => 'products',
                'children' => [],
            ],
        ];
    }

    /**
     * Built-in "Contact Us" entry pointing to the contacts route.
     * Skipped when a contact page already covers it (otherwise nav
     * shows duplicate contact entries).
     *
     * @return array
     */
    private function getContactMenuItem(): array
    {
        if (! has_front_route('contacts.index')) {
            return [];
        }

        if (PageRepo::getInstance()->builder(['active' => true, 'slug' => 'contact'])->exists()) {
            return [];
        }

        return [
            [
                'name'     => __('front::common.contact_us'),
                'url'      => front_route('contacts.index'),
                'slug'     => 'contact',
                'children' => [],
            ],
        ];
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
