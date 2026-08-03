<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

use Illuminate\Support\Facades\Route;
use InnoCMS\Mcp\Http\Controllers\McpController;
use InnoCMS\Mcp\Http\Middleware\EnsureMcpEnabled;
use InnoCMS\Mcp\Http\Middleware\ValidateMcpOrigin;
use InnoCMS\Mcp\Server\InnoCmsMcpServer;
use InnoCMS\Restapi\Middleware\EnsureUserIsAdmin;
use Laravel\Mcp\Facades\Mcp;

Mcp::web('/mcp', InnoCmsMcpServer::class)
    ->middleware([
        EnsureMcpEnabled::class,
        ValidateMcpOrigin::class,
        'auth:sanctum',
        EnsureUserIsAdmin::class,
    ]);

Route::get('/mcp', [McpController::class, 'welcome'])
    ->middleware([EnsureMcpEnabled::class]);
