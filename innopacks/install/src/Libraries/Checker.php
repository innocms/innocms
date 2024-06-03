<?php
/**
 * Copyright (c) Since 2024 InnoShop - All Rights Reserved
 *
 * @link       https://www.innoshop.com
 * @author     InnoShop <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Install\Libraries;

use Illuminate\Support\Facades\DB;

class Checker
{
    /**
     * @return Checker
     */
    public static function getInstance(): Checker
    {
        return new self;
    }

    /**
     * @param  $folder
     * @param  $permission
     * @return bool
     */
    public function checkPermission($folder, $permission): bool
    {
        if (! ($this->getPermission($folder) >= $permission) && php_uname('s') != 'Windows NT') {
            return false;
        }

        return true;
    }

    /**
     * @param  $folder
     * @return string
     */
    private function getPermission($folder): string
    {
        return substr(sprintf('%o', fileperms(base_path($folder))), -4);
    }

    /**
     * Check environment.
     * @return array
     */
    public function checkEnvironment(): array
    {
        $phpVersion = phpversion();

        return [
            'php_version' => $phpVersion,
            'php_env'     => version_compare($phpVersion, '8.2.0') >= 0,
            'extensions'  => [
                'ctype'     => extension_loaded('ctype'),
                'filter'    => extension_loaded('filter'),
                'hash'      => extension_loaded('hash'),
                'mbstring'  => extension_loaded('mbstring'),
                'mysql'     => extension_loaded('pdo_mysql'),
                'openssl'   => extension_loaded('openssl'),
                'session'   => extension_loaded('session'),
                'tokenizer' => extension_loaded('tokenizer'),
                'xml'       => extension_loaded('xml'),
            ],
            'permissions' => [
                '.env'            => $this->checkPermission('.env', 755),
                'storage'         => $this->checkPermission('storage', 755),
                'public/cache'    => $this->checkPermission('public/cache', 755),
                'bootstrap/cache' => $this->checkPermission('bootstrap/cache', 755),
            ],
        ];
    }

    /**
     * Check database connected.
     *
     * @param  $data
     * @return array
     */
    public function checkConnection($data): array
    {
        $connection = strtolower($data['type']);
        $settings   = config("database.connections.$connection");
        config([
            'database' => [
                'default'     => $connection,
                'connections' => [
                    $connection => array_merge($settings, [
                        'driver'   => $connection,
                        'host'     => $data['db_hostname'],
                        'port'     => $data['db_port'],
                        'database' => $data['db_name'],
                        'username' => $data['db_username'],
                        'password' => $data['db_password'],
                        'options'  => [
                            \PDO::ATTR_TIMEOUT => 1,
                        ],
                    ]),
                ],
            ],
        ]);
        DB::purge();
        $result = [];
        try {
            $pdo     = DB::connection()->getPdo();
            $version = $pdo->getAttribute(\PDO::ATTR_SERVER_VERSION);
            if (version_compare($version, '5.7', '<')) {
                $result['db_version'] = trans('install::common.invalid_version');

                return $result;
            }
            $result['db_success'] = true;
            (new Creator())->saveEnv($data);

            return $result;
        } catch (\PDOException $e) {
            switch ($e->getCode()) {
                case 1115:
                    $result['db_version'] = trans('install::common.invalid_version');
                    break;
                case 2002:
                    $result['db_hostname'] = trans('install::common.failed_host_port');
                    $result['db_port']     = trans('install::common.failed_host_port');
                    break;
                case 1045:
                    $result['db_username'] = trans('install::common.failed_user_password');
                    $result['db_password'] = trans('install::common.failed_user_password');
                    break;
                case 1049:
                    $result['db_name'] = trans('install::common.failed_db_name');
                    break;
                default:
                    $result['db_other'] = $e->getMessage();
            }
            $result['db_success'] = false;
        } catch (\Exception $e) {
            $result['env_other'] = $e->getMessage();
        }

        return $result;
    }
}
