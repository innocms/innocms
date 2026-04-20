<?php

namespace InnoCMS\Panel\Controllers;

class FileManagerController extends BaseController
{
    /**
     * Get file manager configuration data.
     */
    protected function getFileManagerData(): array
    {
        $uploadMaxFileSize = ini_get('upload_max_filesize') ?: '2M';
        $postMaxSize       = ini_get('post_max_size') ?: '8M';
        $request           = request();
        $fmDriver          = system_setting('file_manager_driver', 'local');

        return [
            'isIframe'        => $request->header('X-Iframe') === '1',
            'multiple'        => $request->query('multiple') === '1',
            'type'            => $request->query('type', 'all'),
            'base_folder'     => '/',
            'driver'          => $fmDriver,
            'title'           => $fmDriver !== 'local' ? trans('panel/file_manager.oss_title') : trans('panel/file_manager.root_name'),
            'enabled_drivers' => $this->getEnabledDrivers(),
            'config'          => [
                'driver'   => $fmDriver,
                'endpoint' => system_setting("storage_{$fmDriver}_endpoint", system_setting('storage_endpoint', '')),
                'bucket'   => system_setting("storage_{$fmDriver}_bucket", system_setting('storage_bucket', '')),
                'baseUrl'  => config('app.url'),
            ],
            'uploadMaxFileSize' => $uploadMaxFileSize,
            'postMaxSize'       => $postMaxSize,
        ];
    }

    /**
     * Display the file manager full page.
     */
    public function index()
    {
        $data = $this->getFileManagerData();

        return view('panel::file_manager.index', $data);
    }

    /**
     * Display the file manager iframe view.
     */
    public function iframe()
    {
        $data             = $this->getFileManagerData();
        $data['isIframe'] = true;

        return view('panel::file_manager.iframe', $data);
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
