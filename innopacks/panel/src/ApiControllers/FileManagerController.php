<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Panel\ApiControllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InnoCMS\Common\Services\FileManagerService;

class FileManagerController extends BaseApiController
{
    private FileManagerService $fileManager;

    public function __construct()
    {
        $this->fileManager = new FileManagerService;
    }

    /**
     * Get files list with pagination
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $baseFolder = $request->get('path', '/');
            $keyword    = $request->get('keyword', '');
            $sort       = $request->get('sort', 'created');
            $order      = $request->get('order', 'desc');
            $page       = (int) $request->get('page', 1);
            $perPage    = (int) $request->get('per_page', 20);

            $data = $this->fileManager->getFiles($baseFolder, $keyword, $sort, $order, $page, $perPage);

            return json_success('Success', $data);
        } catch (Exception $e) {
            return json_fail($e->getMessage());
        }
    }

    /**
     * Get directories tree
     */
    public function directories(Request $request): JsonResponse
    {
        try {
            $baseFolder  = $request->get('path', '/');
            $directories = $this->fileManager->getDirectories($baseFolder);

            return json_success('Success', ['directories' => $directories]);
        } catch (Exception $e) {
            return json_fail($e->getMessage());
        }
    }

    /**
     * Create directory
     */
    public function createDirectory(Request $request): JsonResponse
    {
        try {
            $path = $request->get('path');
            if (empty($path)) {
                throw new Exception('Path is required');
            }

            $this->fileManager->createDirectory($path);

            return json_success('Success');
        } catch (Exception $e) {
            return json_fail($e->getMessage());
        }
    }

    /**
     * Upload file
     */
    public function upload(Request $request): JsonResponse
    {
        try {
            $file       = $request->file('file');
            $savePath   = $request->get('path', '/');
            $originName = $file->getClientOriginalName();

            $url = $this->fileManager->uploadFile($file, $savePath, $originName);

            return json_success('Success', ['url' => $url]);
        } catch (Exception $e) {
            return json_fail($e->getMessage());
        }
    }

    /**
     * Move files or directories
     */
    public function move(Request $request): JsonResponse
    {
        try {
            $type     = $request->get('type');
            $source   = $request->get('source');
            $dest     = $request->get('destination');
            $files    = $request->get('files', []);

            if ($type === 'directory') {
                $this->fileManager->moveDirectory($source, $dest);
            } else {
                $this->fileManager->moveFiles($files, $dest);
            }

            return json_success('Success');
        } catch (Exception $e) {
            return json_fail($e->getMessage());
        }
    }

    /**
     * Copy files
     */
    public function copy(Request $request): JsonResponse
    {
        try {
            $files = $request->get('files', []);
            $dest  = $request->get('destination');

            $this->fileManager->copyFiles($files, $dest);

            return json_success('Success');
        } catch (Exception $e) {
            return json_fail($e->getMessage());
        }
    }

    /**
     * Rename file or directory
     */
    public function rename(Request $request): JsonResponse
    {
        try {
            $originPath = $request->get('origin');
            $newPath    = $request->get('new');

            $this->fileManager->updateName($originPath, $newPath);

            return json_success('Success');
        } catch (Exception $e) {
            return json_fail($e->getMessage());
        }
    }

    /**
     * Delete files or directories
     */
    public function destroy(Request $request): JsonResponse
    {
        try {
            $path  = $request->get('path');
            $files = $request->get('files', []);

            if ($path) {
                $this->fileManager->deleteDirectoryOrFile($path);
            } elseif (! empty($files)) {
                $basePath = $request->get('base_path', '/');
                $this->fileManager->deleteFiles($basePath, $files);
            }

            return json_success('Success');
        } catch (Exception $e) {
            return json_fail($e->getMessage());
        }
    }
}
