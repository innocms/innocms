<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innocms.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Panel\Controllers;

use InnoCMS\Common\Requests\UploadFileRequest;
use InnoCMS\Common\Requests\UploadImageRequest;
use InnoCMS\Restapi\Services\UploadService;

class UploadController
{
    /**
     * Upload images.
     *
     * @param  UploadImageRequest  $request
     * @return mixed
     */
    public function images(UploadImageRequest $request): mixed
    {
        $image = $request->file('image');
        $type  = $request->file('type', 'common');

        $data = UploadService::getInstance()->uploadFile($image, $type);

        return json_success(trans('common/upload.upload_success'), $data);
    }

    /**
     * Upload document files
     *
     * @param  UploadFileRequest  $request
     * @return mixed
     */
    public function files(UploadFileRequest $request): mixed
    {
        $file = $request->file('file');
        $type = $request->file('type', 'files');

        $data = UploadService::getInstance()->uploadFile($file, $type);

        return json_success(trans('common/upload.upload_success'), $data);
    }
}
