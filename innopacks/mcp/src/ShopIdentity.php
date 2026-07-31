<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Mcp;

/**
 * Encapsulates site identity metadata for MCP tool responses,
 * so clients can distinguish which instance a result came from.
 */
class ShopIdentity
{
    /**
     * @return array{site: array{name: string, host: string, url: string}}
     */
    public function toMeta(): array
    {
        return [
            'site' => [
                'name' => $this->name(),
                'host' => $this->host(),
                'url'  => config('app.url', ''),
            ],
        ];
    }

    public function name(): string
    {
        return system_setting_locale('meta_title') ?: config('app.name', 'InnoCMS');
    }

    public function host(): string
    {
        return parse_url(config('app.url', ''), PHP_URL_HOST) ?: '';
    }
}
