<?php
/**
 * Copyright (c) Since 2024 InnoShop - All Rights Reserved
 *
 * @link       https://www.innoshop.com
 * @author     InnoShop <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Plugin\Carousel\Repositories;

use InnoCMS\Common\Repositories\BaseRepo;
use Plugin\Carousel\Models\CarouselImage;

class CarouselImageRepo extends BaseRepo
{
    /**
     * @param  $data
     * @return mixed
     */
    public function create($data): mixed
    {
        $data = $this->handleData($data);

        return CarouselImage::query()->create($data);
    }

    /**
     * @param  mixed  $item
     * @param  $data
     * @return mixed
     */
    public function update(mixed $item, $data): mixed
    {
        $data = $this->handleData($data);

        $item->update($data);

        return $item;
    }

    /**
     * @param  $data
     * @return array
     */
    private function handleData($data): array
    {
        return [
            'carousel_id'     => $data['carousel_id'],
            'title'      => $data['title'] ?? '',
            'description'     => $data['description']   ?? '',
            'image_url' => $data['image_url'] ?? '',
            'target_url'   => $data['target_url']   ?? '',
            'position'  => $data['position'] ?? 0,
            'active'    => $data['active'] ?? true,
            'item_interval' => $data['item_interval'] ?? 5000
        ];
    }
}
