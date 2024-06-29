<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Front\Controllers;

use Illuminate\Http\JsonResponse;
use InnoCMS\Front\Requests\UploadFileRequest;
use InnoCMS\Front\Requests\UploadImageRequest;

class UploadController
{
    /**
     * Upload images.
     *
     * @param  UploadImageRequest  $request
     * @return JsonResponse
     */
    public function images(UploadImageRequest $request): JsonResponse
    {
        $image    = $request->file('image');
        $type     = $request->get('type','common');
        $event    = $request->get('event','');
        $filePath = $image->store("/{$type}/{$event}", 'public');
        $realPath = "/storage/$filePath";

        $data = [
            'url'   => asset($realPath),
            'value' => $realPath,
            'type'  => $type,
            'event' => $event
        ];

        return json_success('上传成功', $data);
    }

    /**
     * Upload document files
     *
     * @param  UploadFileRequest  $request
     * @return JsonResponse
     */
    public function files(UploadFileRequest $request): JsonResponse
    {
        $file     = $request->file('file');
        $type     = $request->file('type', 'common');
        $filePath = $file->store("/{$type}", 'public');
        $realPath = "/storage/$filePath";

        $data = [
            'url'   => asset($realPath),
            'value' => $realPath,
        ];

        return json_success('上传成功', $data);
    }
}
