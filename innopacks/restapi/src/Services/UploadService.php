<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\RestAPI\Services;

use Illuminate\Support\Facades\Storage;
use InnoCMS\Common\Requests\UploadFileRequest;
use InnoCMS\Common\Requests\UploadImageRequest;
use InnoCMS\Common\Services\FileSecurityValidator;
use InnoCMS\Common\Services\StorageService;
use InnoCMS\Front\Services\BaseService;

class UploadService extends BaseService
{
    /**
     * Generic upload method for different file types and storage disks.
     * Always returns storage key as value; URLs generated via storage_url().
     *
     * @param  mixed  $file  The uploaded file
     * @param  string  $type  File type/directory
     * @return array
     */
    public function uploadFile($file, string $type = 'common'): array
    {
        FileSecurityValidator::validateFile($file->getClientOriginalName());

        $filePath   = $file->store("/{$type}", 'media');
        $storageKey = StorageService::storageKey($filePath);

        return [
            'url'        => storage_url($storageKey),
            'origin_url' => storage_url($storageKey),
            'value'      => $storageKey,
        ];
    }

    /**
     * Upload images.
     *
     * @param  UploadImageRequest  $request
     * @return array
     */
    public function images(UploadImageRequest $request): array
    {
        $image = $request->file('image');
        $type  = $request->file('type', 'common');

        return $this->uploadFile($image, $type);
    }

    /**
     * Upload document files
     *
     * @param  UploadFileRequest  $request
     * @return array
     */
    public function docs(UploadFileRequest $request): array
    {
        $file = $request->file('file');
        $type = $request->file('type', 'docs');

        return $this->uploadFile($file, $type);
    }

    /**
     * Upload document files
     *
     * @param  UploadFileRequest  $request
     * @return array
     */
    public function files(UploadFileRequest $request): array
    {
        $file = $request->file('file');
        $type = $request->file('type', 'files');

        return $this->uploadFile($file, $type);
    }
}
