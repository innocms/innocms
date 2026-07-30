<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Mcp\Server;

use InnoCMS\Aicore\Contracts\ToolInterface;
use InnoCMS\Aicore\Services\ToolRegistry;
use InnoCMS\Mcp\ShopIdentity;
use InnoCMS\Mcp\Tools\RegistryToolAdapter;
use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;
use Laravel\Mcp\Server\Contracts\Transport;
use Laravel\Mcp\Server\ServerContext;

#[Name('InnoCMS')]
#[Version('1.0.0')]
#[Instructions('InnoCMS MCP server. Exposes site management tools registered in innopacks/aicore. All tools are adapters over existing repositories.')]
class InnoCmsMcpServer extends Server
{
    protected array $capabilities = [
        self::CAPABILITY_TOOLS => [
            'listChanged' => false,
        ],
    ];

    public int $defaultPaginationLength = 100;

    public int $maxPaginationLength = 200;

    public function __construct(Transport $transport, ToolRegistry $registry, private readonly ShopIdentity $shopIdentity)
    {
        parent::__construct($transport);

        $this->tools = array_map(
            fn (ToolInterface $tool) => new RegistryToolAdapter($tool),
            array_values($registry->all())
        );
    }

    public function createContext(): ServerContext
    {
        $context = parent::createContext();

        $context->instructions .= "\n\nServer: {$this->shopIdentity->host()}";

        return $context;
    }
}
