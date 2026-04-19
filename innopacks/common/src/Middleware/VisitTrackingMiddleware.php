<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoShop <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Common\Middleware;

use Closure;
use Illuminate\Http\Request;
use InnoCMS\Common\Services\VisitTrackingService;

class VisitTrackingMiddleware
{
    /**
     * Visit tracking service instance
     *
     * @var VisitTrackingService
     */
    private VisitTrackingService $visitTrackingService;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->visitTrackingService = new VisitTrackingService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        // Get session ID
        $sessionId = $request->session()->getId();

        // Get admin ID if authenticated
        $customerId = current_admin()?->id;

        // Track visit
        $this->visitTrackingService->trackVisit($request, $sessionId, $customerId);

        // Process request
        return $next($request);
    }
}
