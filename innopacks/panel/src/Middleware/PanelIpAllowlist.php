<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Panel\Middleware;

use Closure;
use Illuminate\Http\Request;
use InnoCMS\Common\Audit\AuditLogger;

/**
 * IP allowlist for admin panel paths (sunk down from the platform pack, tenant column removed).
 *
 * Mounted globally on the web group (appendToGroup in the Provider), self-guarding by path prefix,
 * covering the base /panel by default (prefix table panel.security.guard_prefixes; downstream
 * products may append their own admin panel prefixes).
 * Configure panel.security.panel_ip_allowlist: exact IP or IPv4 CIDR, comma separated.
 * Not configured = disabled. A matched denial returns 404 (not exposing the admin panel existence) and writes an audit log.
 */
class PanelIpAllowlist
{
    public function handle(Request $request, Closure $next): mixed
    {
        $allowlist = config('panel.security.panel_ip_allowlist', []);

        if (empty($allowlist) || ! static::isGuardedPath($request)) {
            return $next($request);
        }

        $ip = (string) $request->ip();

        if (static::isAllowed($ip, (array) $allowlist)) {
            return $next($request);
        }

        AuditLogger::log('security.ip_blocked', 'admin_panel', null, null, [
            'ip'   => $ip,
            'path' => $request->path(),
        ]);

        abort(404);
    }

    protected static function isGuardedPath(Request $request): bool
    {
        foreach ((array) config('panel.security.guard_prefixes', ['panel']) as $prefix) {
            if ($request->is($prefix) || $request->is($prefix.'/*')) {
                return true;
            }
        }

        return false;
    }

    public static function isAllowed(string $ip, array $allowlist): bool
    {
        foreach ($allowlist as $rule) {
            $rule = trim((string) $rule);
            if ($rule === '') {
                continue;
            }

            if ($rule === $ip) {
                return true;
            }

            // IPv4 CIDR, e.g. 203.0.113.0/24
            if (str_contains($rule, '/') && ! str_contains($rule, ':')) {
                [$subnet, $bits] = explode('/', $rule, 2);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)
                    && filter_var($subnet, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)
                    && static::ipv4InCidr($ip, $subnet, (int) $bits)) {
                    return true;
                }
            }
        }

        return false;
    }

    protected static function ipv4InCidr(string $ip, string $subnet, int $bits): bool
    {
        if ($bits < 0 || $bits > 32) {
            return false;
        }

        return (ip2long($ip) >> (32 - $bits)) === (ip2long($subnet) >> (32 - $bits));
    }
}
