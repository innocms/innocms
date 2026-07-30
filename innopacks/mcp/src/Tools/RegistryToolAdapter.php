<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Mcp\Tools;

use InnoCMS\Aicore\Contracts\ToolInterface;
use InnoCMS\Mcp\ShopIdentity;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Tool;
use stdClass;
use Throwable;

/**
 * Adapts an innopacks/ai ToolInterface into a laravel/mcp Tool so every
 * registered tool is automatically exposed over MCP.
 */
class RegistryToolAdapter extends Tool
{
    public function __construct(private readonly ToolInterface $tool) {}

    public function name(): string
    {
        return $this->tool->name();
    }

    public function description(): string
    {
        $base = $this->tool->description();

        try {
            $host = app(ShopIdentity::class)->host();
        } catch (Throwable) {
            return $base;
        }

        return $host === '' ? $base : "{$base} [server: {$host}]";
    }

    public function toArray(): array
    {
        return [
            'name'        => $this->tool->name(),
            'description' => $this->tool->description(),
            'inputSchema' => $this->tool->inputSchema() ?: ['type' => 'object', 'properties' => new stdClass],
            'annotations' => new stdClass,
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        $permission = $this->tool->requiredPermission();
        if ($permission !== null) {
            $user = $request->user();
            if (! $user || ! $user->can($permission)) {
                return $this->withShopMeta(
                    Response::error("Permission denied: [{$permission}] is required.")
                );
            }
        }

        try {
            $result = $this->tool->execute($request->all());

            // Inject site identity into the payload itself. _meta on the
            // JSON-RPC envelope is stripped by MCP harnesses; the only
            // reliable channel to the LLM is the content body.
            if (is_array($result)) {
                $result['_site'] = app(ShopIdentity::class)->toMeta()['site'];
            }

            $response = is_string($result) ? Response::text($result) : Response::json($result);

            return $this->withShopMeta($response);
        } catch (Throwable $e) {
            return $this->withShopMeta(
                Response::error($e->getMessage())
            );
        }
    }

    private function withShopMeta(Response $response): ResponseFactory
    {
        return (new ResponseFactory($response))
            ->withMeta(app(ShopIdentity::class)->toMeta());
    }
}
