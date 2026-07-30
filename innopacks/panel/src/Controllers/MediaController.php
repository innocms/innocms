<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Panel\Controllers;

class MediaController extends BaseController
{
    /**
     * Get media manager configuration data.
     */
    protected function getMediaData(): array
    {
        $uploadMaxFileSize = ini_get('upload_max_filesize') ?: '2M';
        $postMaxSize       = ini_get('post_max_size') ?: '8M';
        $request           = request();
        $mediaDriver       = system_setting('media_driver', 'local');

        return [
            'isIframe'        => $request->header('X-Iframe') === '1',
            'multiple'        => $request->query('multiple') === '1',
            'type'            => $request->query('type', 'all'),
            'base_folder'     => '/',
            'driver'          => $mediaDriver,
            'title'           => $mediaDriver !== 'local' ? trans('panel/media.cloud_title') : trans('panel/media.root_name'),
            'enabled_drivers' => $this->getEnabledDrivers(),
            'config'          => [
                'driver'   => $mediaDriver,
                'endpoint' => system_setting("storage_{$mediaDriver}_endpoint", system_setting('storage_endpoint', '')),
                'bucket'   => system_setting("storage_{$mediaDriver}_bucket", system_setting('storage_bucket', '')),
                'baseUrl'  => config('app.url'),
            ],
            'uploadMaxFileSize' => $uploadMaxFileSize,
            'postMaxSize'       => $postMaxSize,
        ];
    }

    /**
     * Display the media manager full page.
     */
    public function index()
    {
        $data = $this->getMediaData();

        return view('panel::media.index', $data);
    }

    /**
     * Display the media manager iframe view.
     */
    public function iframe()
    {
        $data             = $this->getMediaData();
        $data['isIframe'] = true;

        return view('panel::media.iframe', $data);
    }

    /**
     * Get enabled storage drivers.
     */
    private function getEnabledDrivers(): array
    {
        $valid   = ['oss', 'cos', 'qiniu', 's3', 'obs', 'r2', 'minio'];
        $drivers = ['local'];

        foreach ($valid as $driver) {
            if (system_setting("storage_{$driver}_enabled", '0') === '1') {
                $drivers[] = $driver;
            }
        }

        return $drivers;
    }
}
