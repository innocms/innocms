<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Aicore\Tools;

use InnoCMS\Aicore\Contracts\ToolInterface;

abstract class BaseTool implements ToolInterface
{
    public function inputSchema(): array
    {
        return [
            'type'       => 'object',
            'properties' => new \stdClass,
        ];
    }

    public function requiredPermission(): ?string
    {
        return null;
    }
}
