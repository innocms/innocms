<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Front\Middleware;

use Closure;
use Illuminate\Http\Request;
use InnoCMS\Common\Services\VisitTrackingService;

class TrackVisit
{
    private VisitTrackingService $visitTrackingService;

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
        if (! installed()) {
            return $next($request);
        }

        $sessionId = $request->session()->getId();

        $this->visitTrackingService->trackVisit($request, $sessionId);

        return $next($request);
    }
}
