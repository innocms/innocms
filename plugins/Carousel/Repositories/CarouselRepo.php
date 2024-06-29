<?php
/**
 * Copyright (c) Since 2024 InnoShop - All Rights Reserved
 *
 * @link       https://www.innoshop.com
 * @author     XING GUI YU <xingguiyu@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Plugin\Carousel\Repositories;

use InnoCMS\Common\Repositories\BaseRepo;
use Plugin\Carousel\Models\Carousel;

class CarouselRepo extends BaseRepo
{
    /**
     * @param  $data
     * @return mixed
     */
    public function create($data): mixed
    {
        $data = $this->handleData($data);

        return Carousel::query()->create($data);
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
            'name'            => $data['name'],
            'page_id'         => $data['page_id']         ?? 0,
            'position'        => $data['position']        ?? 'top',
            'style'           => $data['style']           ?? 'container-fluid',
            'height'          => $data['height']          ?? $data['style'] = 'container-fluid' ? 600 : 400,
            'order_index'     => $data['order_index']     ?? 0,
            'active'          => $data['active']          ?? true,
            'auto_play'       => $data['auto_play']       ?? true,
            'with_controls'   => $data['with_controls']   ?? true,
            'with_indicators' => $data['with_indicators'] ?? true,
            'with_captions'   => $data['with_captions']   ?? false,
            'cross_fade'      => $data['cross_fade']      ?? false,
            'dark_variant'    => $data['dark_variant']    ?? false,
            'touch_swiping'   => $data['touch_swiping']   ?? true,
        ];
    }
}
