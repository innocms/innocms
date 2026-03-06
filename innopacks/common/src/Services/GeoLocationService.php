<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Common\Services;

use Exception;
use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class GeoLocationService
{
    private ?Reader $reader = null;

    private string $databasePath = '';

    public function __construct()
    {
        $this->databasePath = storage_path('app/geoip/GeoLite2-City.mmdb');
    }

    /**
     * Get location information by IP address
     *
     * @param  string  $ip
     * @return array
     */
    public function getLocation(string $ip): array
    {
        if (empty($ip) || $ip === '127.0.0.1' || str_starts_with($ip, '192.168.') || str_starts_with($ip, '10.')) {
            return $this->getDefaultLocation();
        }

        if (! File::exists($this->databasePath)) {
            return $this->getDefaultLocation();
        }

        try {
            if ($this->reader === null) {
                $this->reader = new Reader($this->databasePath);
            }

            $record = $this->reader->city($ip);

            return [
                'country_code' => $record->country->isoCode ?? '',
                'country_name' => $record->country->name ?? '',
                'city'         => $record->city->name ?? '',
                'latitude'     => $record->location->latitude ?? null,
                'longitude'    => $record->location->longitude ?? null,
            ];
        } catch (AddressNotFoundException $e) {
            return $this->getDefaultLocation();
        } catch (Exception $e) {
            Log::warning('GeoLocationService: Failed to get location', [
                'ip'    => $ip,
                'error' => $e->getMessage(),
            ]);

            return $this->getDefaultLocation();
        }
    }

    /**
     * Get default location (empty)
     *
     * @return array
     */
    private function getDefaultLocation(): array
    {
        return [
            'country_code' => '',
            'country_name' => '',
            'city'         => '',
            'latitude'     => null,
            'longitude'    => null,
        ];
    }

    /**
     * Check if GeoLite2 database is available
     *
     * @return bool
     */
    public function isAvailable(): bool
    {
        return File::exists($this->databasePath);
    }

    /**
     * Get the database path
     *
     * @return string
     */
    public function getDatabasePath(): string
    {
        return $this->databasePath;
    }
}
