<?php
/**
 * Copyright (c) Since 2024 InnoShop - All Rights Reserved
 *
 * @link       https://www.innoshop.com
 * @author     InnoShop <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Plugin\PartnerLink\Repositories;

use InnoCMS\Common\Repositories\BaseRepo;
use Plugin\PartnerLink\Models\PartnerLink;

class PartnerLinkRepo extends BaseRepo
{
    /**
     * @param  $data
     * @return mixed
     */
    public function create($data): mixed
    {
        $data = $this->handleData($data);

        return PartnerLink::query()->create($data);
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
            'name'     => $data['name'],
            'url'      => $data['url'],
            'logo'     => $data['logo']     ?? '',
            'position' => $data['position'] ?? 0,
            'active'   => $data['active']   ?? false,
        ];
    }
}
