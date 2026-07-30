<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Aicore\Contracts;

/**
 * A Tool is a unit of AI-callable capability, shared by the panel AI assistant
 * and the MCP server (innopacks/mcp). Business logic must delegate to existing
 * repositories/services — a Tool is only an adapter.
 */
interface ToolInterface
{
    /**
     * Unique snake_case identifier, e.g. product_list.
     */
    public function name(): string;

    /**
     * Human/LLM-readable description of what the tool does.
     */
    public function description(): string;

    /**
     * JSON Schema describing the accepted arguments.
     */
    public function inputSchema(): array;

    /**
     * Panel permission slug required to execute this tool, null = unrestricted.
     */
    public function requiredPermission(): ?string;

    /**
     * Run the tool with validated arguments.
     */
    public function execute(array $arguments): mixed;
}
