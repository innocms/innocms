<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Common\Services;

use Detection\MobileDetect;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use InnoCMS\Common\Models\Visit;

class VisitTrackingService
{
    private GeoLocationService $geoLocationService;

    private MobileDetect $detect;

    public function __construct()
    {
        $this->geoLocationService = new GeoLocationService;
        $this->detect             = new MobileDetect;
    }

    public function trackVisit(Request $request, string $sessionId): ?Visit
    {
        try {
            if ($this->shouldSkipTracking($request)) {
                return null;
            }

            $this->detect->setUserAgent($request->userAgent());

            $ip       = $this->getClientIp($request);
            $location = $this->geoLocationService->getLocation($ip);

            $visit = Visit::where('session_id', $sessionId)->first();

            if ($visit) {
                $visit->update([
                    'last_visited_at' => now(),
                ]);
            } else {
                $visit = Visit::create([
                    'session_id'       => $sessionId,
                    'ip_address'       => $ip,
                    'user_agent'       => $request->userAgent(),
                    'country_code'     => $location['country_code'] ?? null,
                    'country_name'     => $location['country_name'] ?? null,
                    'city'             => $location['city'] ?? null,
                    'referrer'         => $request->header('referer'),
                    'device_type'      => $this->getDeviceType(),
                    'browser'          => $this->getBrowser(),
                    'os'               => $this->getOperatingSystem(),
                    'locale'           => front_locale_code(),
                    'first_visited_at' => now(),
                    'last_visited_at'  => now(),
                ]);
            }

            return $visit;
        } catch (Exception $e) {
            Log::error('VisitTrackingService: Failed to track visit', [
                'session_id' => $sessionId,
                'error'      => $e->getMessage(),
            ]);

            return null;
        }
    }

    private function shouldSkipTracking(Request $request): bool
    {
        $routeName = $request->route()?->getName();

        if (str_starts_with($request->path(), 'api/') || str_starts_with($request->path(), 'panel/')) {
            return true;
        }

        return false;
    }

    private function getClientIp(Request $request): string
    {
        $ip = $request->ip();

        if (str_starts_with($ip, '::ffff:')) {
            $ip = substr($ip, 7);
        }

        return $ip;
    }

    private function getDeviceType(): string
    {
        if ($this->detect->isMobile()) {
            return 'mobile';
        }

        if ($this->detect->isTablet()) {
            return 'tablet';
        }

        return 'desktop';
    }

    private function getBrowser(): string
    {
        if ($this->detect->isChrome()) {
            return 'Chrome';
        }
        if ($this->detect->isFirefox()) {
            return 'Firefox';
        }
        if ($this->detect->isSafari()) {
            return 'Safari';
        }
        if ($this->detect->isOpera()) {
            return 'Opera';
        }
        if ($this->detect->isIE()) {
            return 'IE';
        }

        return '';
    }

    private function getOperatingSystem(): string
    {
        if ($this->detect->isIOS()) {
            return 'iOS';
        }
        if ($this->detect->isAndroidOS()) {
            return 'Android';
        }
        if ($this->detect->isWindowsPhoneOS()) {
            return 'Windows Phone';
        }
        if ($this->detect->isWindows()) {
            return 'Windows';
        }
        if ($this->detect->isMac()) {
            return 'macOS';
        }
        if ($this->detect->isLinux()) {
            return 'Linux';
        }

        return '';
    }
}
