<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\RestAPI\Services;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use InnoCMS\Common\Models\MediaFile;
use InnoCMS\Common\Services\FileSecurityValidator;
use InnoCMS\Common\Services\MediaUrlResolver;
use InnoCMS\Common\Services\StorageService;

class FileManagerService implements FileManagerInterface
{
    protected string $fileBasePath = '';

    protected string $mediaDir;

    protected string $basePath = '';

    /**
     * Excluded files from listing
     */
    protected const EXCLUDED_FILES = ['index.html'];

    /**
     * Default sort field
     */
    protected const SORT_FIELD_CREATED = 'created';

    /**
     * Default sort order
     */
    protected const SORT_ORDER_DESC = 'desc';

    public function __construct()
    {
        $this->mediaDir     = rtrim(StorageService::STORAGE_PREFIX, '/');
        $this->basePath     = '/'.$this->mediaDir;
        $this->fileBasePath = public_path().$this->basePath;
    }

    /**
     * Retrieves directories within a base folder.
     *
     * @param  string  $baseFolder  Path to the base folder
     * @return array Array of directories with their details
     */
    public function getDirectories(string $baseFolder = '/'): array
    {
        $baseFolder   = FileSecurityValidator::validateDirectoryPath($baseFolder);
        $realBasePath = $this->getRealBasePath();
        if ($realBasePath === false) {
            return [];
        }

        $currentBasePath = rtrim($this->fileBasePath.$baseFolder, '/');
        $directories     = glob("$currentBasePath/*", GLOB_ONLYDIR) ?: [];

        $result = [];
        foreach ($directories as $directory) {
            $processed = $this->processDirectoryIterative($directory, $realBasePath);
            if ($processed !== null) {
                $result[] = $processed;
            }
        }

        return $result;
    }

    /**
     * Get files list with pagination and filtering
     *
     * @param  string  $baseFolder  Base folder path
     * @param  string  $keyword  Search keyword
     * @param  string  $sort  Sort field (created or name)
     * @param  string  $order  Sort order (asc or desc)
     * @param  int  $page  Current page number
     * @param  int  $perPage  Items per page
     * @return array Paginated file list with metadata
     * @throws Exception If an error occurs during retrieval
     */
    public function getFiles(string $baseFolder, string $keyword = '', string $sort = self::SORT_FIELD_CREATED, string $order = self::SORT_ORDER_DESC, int $page = 1, int $perPage = 20, bool $includeDirectories = false): array
    {
        $baseFolder   = FileSecurityValidator::validateDirectoryPath($baseFolder);
        $realBasePath = $this->getRealBasePath();
        if ($realBasePath === false) {
            return $this->getEmptyFileList($page);
        }

        $currentBasePath = rtrim($this->fileBasePath.$baseFolder, '/');
        $fileEntries     = $this->collectFileEntries($currentBasePath, $realBasePath, $keyword);
        $allItems        = $includeDirectories
            ? array_merge($this->collectFolders($currentBasePath, $realBasePath), $fileEntries)
            : $fileEntries;
        $allItems = $this->sortItems($allItems, $sort, $order);

        $total     = count($allItems);
        $offset    = ($page - 1) * $perPage;
        $pageItems = array_slice($allItems, $offset, $perPage);
        $pageItems = $this->enrichFileMetadata($pageItems);

        return [
            'items'    => $pageItems,
            'total'    => $total,
            'page'     => $page,
            'per_page' => $perPage,
            'success'  => true,
        ];
    }

    /**
     * Creates a new directory.
     *
     * @param  string  $path  Path where the directory should be created
     * @return bool True if directory was created successfully
     * @throws Exception If directory already exists or creation fails
     */
    public function createDirectory(string $path): bool
    {
        try {
            // Validate path security
            $path = FileSecurityValidator::validateDirectoryPath($path);

            $folderPath = $this->getFullPath($path);
            if (is_dir($folderPath)) {
                throw new Exception(trans('panel/media.directory_already_exist'));
            }

            create_directories("$this->mediaDir/$path");

            return true;
        } catch (Exception $e) {
            Log::error('Create directory failed:', [
                'path'  => $path,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Moves a directory to a new path.
     *
     * @param  string  $sourcePath  Source directory path
     * @param  string  $destPath  Destination directory path
     * @return bool True if directory was moved successfully
     * @throws Exception If source or destination is invalid, or move operation fails
     */
    public function moveDirectory(string $sourcePath, string $destPath): bool
    {
        try {
            $this->validatePathsNotEmpty($sourcePath, $destPath);
            $this->validateNotMovingToSubdirectory($sourcePath, $destPath);

            $sourceDirPath = $this->getFullPath($sourcePath);
            $destDirPath   = $this->getFullPath($destPath);
            $destFullPath  = rtrim($destDirPath, '/').'/'.basename($sourcePath);

            $this->ensureDirectoryExists($sourceDirPath);
            $this->ensureDirectoryExists($destDirPath);
            $this->ensurePathDoesNotExist($destFullPath);

            if (! @rename($sourceDirPath, $destFullPath)) {
                Log::error('Failed to move directory:', [
                    'error' => error_get_last(),
                ]);
                throw new Exception(trans('panel/media.move_failed'));
            }

            $newRelDir = rtrim($destPath, '/').'/'.basename($sourcePath);
            $this->relocateMediaUnderDirectory($sourcePath, $newRelDir);

            return true;
        } catch (Exception $e) {
            $this->logError('Move directory failed', $e, [
                'source'      => $sourcePath,
                'destination' => $destPath,
            ]);
            throw $e;
        }
    }

    /**
     * Moves multiple files to a new directory.
     *
     * @param  array  $files  Array of file paths to move
     * @param  string  $destPath  Destination directory path
     * @return bool True if all files were moved successfully
     * @throws Exception If files cannot be moved or other error occurs
     */
    public function moveFiles(array $files, string $destPath): bool
    {
        try {
            $this->validateFilesNotEmpty($files);
            $destPath = FileSecurityValidator::validateDirectoryPath($destPath);
            $files    = $this->validateFilePaths($files);

            $destFullPath = $this->getFullPath($destPath);
            $this->ensureDirectoryExists($destFullPath);

            foreach ($files as $fileName) {
                $this->moveSingleFile($fileName, $destFullPath, $destPath);
            }

            return true;
        } catch (Exception $e) {
            $this->logError('Move files failed', $e, ['files' => $files, 'destination' => $destPath]);
            throw $e;
        }
    }

    /**
     * Deletes a file or folder.
     *
     * @param  string  $path  Path to the file or folder to delete
     * @return bool True if deletion was successful
     * @throws Exception If deletion fails or path is invalid
     */
    public function deleteDirectoryOrFile(string $path): bool
    {
        try {
            $path     = FileSecurityValidator::validateDirectoryPath($path);
            $fullPath = $this->getFullPath($path);

            if (is_dir($fullPath)) {
                $this->deleteDirectory($fullPath);
            } elseif (file_exists($fullPath)) {
                $this->deleteFile($fullPath);
            } else {
                Log::warning('Path not found:', ['path' => $fullPath]);
                throw new Exception(trans('panel/media.file_not_exist'));
            }

            return true;
        } catch (Exception $e) {
            $this->logError('Delete path failed', $e, ['path' => $path]);
            throw $e;
        }
    }

    /**
     * Delete multiple files.
     *
     * @param  string  $basePath  Base directory path
     * @param  array  $files  Array of filenames to delete
     * @return bool True if all files were deleted successfully
     * @throws Exception If deletion fails or files are not found
     */
    public function deleteFiles(string $basePath, array $files): bool
    {
        try {
            $this->validateFilesNotEmpty($files);

            foreach ($files as $file) {
                $filePath = $this->getFullPath("$basePath/$file");

                if (file_exists($filePath)) {
                    $this->deleteFile($filePath);
                } else {
                    Log::warning('File not found:', ['path' => $filePath]);
                }
            }

            return true;
        } catch (Exception $e) {
            $this->logError('Delete files failed', $e, ['files' => $files]);
            throw $e;
        }
    }

    /**
     * Renames a file or folder.
     *
     * @param  string  $originPath  Original path
     * @param  string  $newPath  New path
     * @return bool True if renaming was successful
     * @throws Exception If renaming fails or paths are invalid
     */
    public function updateName(string $originPath, string $newPath): bool
    {
        try {
            // Validate path security (防止路径遍历)
            $originPath = FileSecurityValidator::validateDirectoryPath($originPath);
            $newPath    = FileSecurityValidator::validateDirectoryPath($newPath);

            // Validate new file name security (防止文件名包含路径分隔符等)
            $newFileName = basename($newPath);
            FileSecurityValidator::validateFileName($newFileName);

            // Validate file extension if it's a file rename (防止危险扩展名)
            if (pathinfo($newFileName, PATHINFO_EXTENSION)) {
                FileSecurityValidator::validateFileExtension($newFileName);
            }

            $originFullPath = $this->getFullPath($originPath);
            $newFullPath    = $this->getFullPath($newPath);

            if (! is_dir($originFullPath) && ! file_exists($originFullPath)) {
                throw new Exception(trans('panel/media.target_not_exist'));
            }

            if (file_exists($newFullPath)) {
                $dirPath     = dirname($newPath);
                $newName     = $this->getUniqueFileName($dirPath, basename($newPath));
                $newPath     = $dirPath === '/' ? "/$newName" : "$dirPath/$newName";
                $newFullPath = $this->getFullPath($newPath);
            }

            if (! @rename($originFullPath, $newFullPath)) {
                Log::error('Failed to rename:', [
                    'from'  => $originFullPath,
                    'to'    => $newFullPath,
                    'error' => error_get_last(),
                ]);
                throw new Exception(trans('panel/media.rename_failed'));
            }

            // Sync MediaFile DB records: file = relocate single key; dir = relocate under prefix.
            if (is_dir($newFullPath)) {
                $this->relocateMediaUnderDirectory($originPath, $newPath);
            } else {
                $this->relocateMediaByKey(
                    StorageService::storageKey($originPath),
                    StorageService::storageKey($newPath)
                );
            }

            return true;
        } catch (Exception $e) {
            Log::error('Rename failed:', [
                'error'       => $e->getMessage(),
                'origin_path' => $originPath,
                'new_path'    => $newPath,
            ]);
            throw $e;
        }
    }

    /**
     * Uploads a file to a specified path.
     *
     * @param  UploadedFile  $file  The uploaded file
     * @param  string  $savePath  Path where the file should be saved
     * @param  string  $originName  Original filename
     * @return string URL to the uploaded file
     */
    public function uploadFile(UploadedFile $file, string $savePath, string $originName): string
    {
        // Validate file security (包括文件名和扩展名安全性)
        FileSecurityValidator::validateFile($originName);

        // Validate save path security
        $savePath = FileSecurityValidator::validateDirectoryPath($savePath);

        $originName = $this->getUniqueFileName($savePath, $originName);
        $filePath   = $file->storeAs($savePath, $originName, 'media');
        $storageKey = StorageService::storageKey($filePath);

        $this->registerMediaFromUpload($file, $storageKey);

        return $storageKey;
    }

    /**
     * Download a remote file from URL and save to the specified directory.
     *
     * @param  string  $url  Remote file URL
     * @param  string  $savePath  Target directory path in file manager
     * @param  string|null  $fileName  Optional file name (defaults to URL basename)
     * @return string Storage key of the saved file
     *
     * @throws Exception
     */
    public function downloadRemoteFile(string $url, string $savePath, ?string $fileName = null): string
    {
        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            throw new Exception(trans('panel/media.invalid_url'));
        }

        $savePath = FileSecurityValidator::validateDirectoryPath($savePath);

        // Download the file first
        $response = Http::timeout(60)->get($url);
        if (! $response->successful()) {
            throw new Exception(trans('panel/media.download_failed'));
        }

        // Determine file name: explicit > URL path > content-type based
        if (empty($fileName)) {
            $fileName = basename(parse_url($url, PHP_URL_PATH));
        }
        if (empty($fileName) || ! str_contains($fileName, '.')) {
            $extension = $this->getExtensionFromResponse($response, $url);
            $fileName  = md5($url).'.'.$extension;
        }

        FileSecurityValidator::validateFile($fileName);

        // Ensure target directory exists
        $dirFullPath = $this->getFullPath($savePath);
        if (! is_dir($dirFullPath)) {
            create_directories("$this->mediaDir/$savePath");
        }

        $fileName = $this->getUniqueFileName($savePath, $fileName);
        $filePath = ltrim($savePath, '/').'/'.ltrim($fileName, '/');
        $fullPath = $this->getFullPath("$savePath/$fileName");

        file_put_contents($fullPath, $response->body());

        $storageKey = StorageService::storageKey($filePath);
        $this->registerMediaFromLocalPath($storageKey, $fullPath, $fileName);

        return $storageKey;
    }

    /**
     * Guess file extension from response Content-Type or URL.
     */
    protected function getExtensionFromResponse($response, string $url): string
    {
        $contentType = $response->header('Content-Type') ?? '';
        $mimeToExt   = [
            'image/jpeg'      => 'jpg',
            'image/png'       => 'png',
            'image/gif'       => 'gif',
            'image/webp'      => 'webp',
            'image/svg+xml'   => 'svg',
            'image/bmp'       => 'bmp',
            'video/mp4'       => 'mp4',
            'application/pdf' => 'pdf',
        ];

        $mime = strtolower(strtok($contentType, ';'));
        if (isset($mimeToExt[$mime])) {
            return $mimeToExt[$mime];
        }

        // Fallback: try to guess from URL query params
        parse_str(parse_url($url, PHP_URL_QUERY) ?? '', $query);
        $format    = $query['fm'] ?? '';
        $formatMap = ['jpg' => 'jpg', 'jpeg' => 'jpg', 'png' => 'png', 'gif' => 'gif', 'webp' => 'webp'];
        if (isset($formatMap[$format])) {
            return $formatMap[$format];
        }

        return 'jpg';
    }

    /**
     * Generates a unique file name to avoid conflicts.
     *
     * @param  string  $savePath  Directory path
     * @param  string  $originName  Original filename
     * @return string Unique filename
     */
    public function getUniqueFileName(string $savePath, string $originName): string
    {
        $maxAttempts = 100;
        for ($i = 0; $i < $maxAttempts; $i++) {
            $fullPath = $this->getFullPath("$savePath/$originName");
            if (! file_exists($fullPath)) {
                return $originName;
            }
            $originName = $this->getNewFileName($originName);
        }

        return $originName;
    }

    /**
     * Generates a new file name by appending an incremented index.
     *
     * @param  string  $originName  Original filename
     * @return string New filename with index
     */
    public function getNewFileName(string $originName): string
    {
        $extension = pathinfo($originName, PATHINFO_EXTENSION);
        $name      = pathinfo($originName, PATHINFO_FILENAME);

        if (preg_match('/(.+?)\((\d+)\)$/', $name, $matches)) {
            $index = (int) $matches[2] + 1;
            $name  = "{$matches[1]}({$index})";
        } else {
            $name .= '(1)';
        }

        return "{$name}.{$extension}";
    }

    /**
     * Processes a folder and returns its metadata.
     *
     * @param  string  $folderPath  Path to the folder
     * @param  string  $folderName  Folder name
     * @return array Folder metadata
     */
    protected function handleFolder(string $folderPath, string $folderName): array
    {
        return [
            'name' => $folderName,
            'path' => $folderPath,
        ];
    }

    /**
     * Copies multiple files to a new directory.
     *
     * @param  array  $files  Array of file paths to copy
     * @param  string  $destPath  Destination directory path
     * @return bool True if all files were copied successfully
     * @throws Exception If copying fails or files are not found
     */
    public function copyFiles(array $files, string $destPath): bool
    {
        try {
            $this->validateFilesNotEmpty($files);
            $destPath = FileSecurityValidator::validateDirectoryPath($destPath);
            $files    = $this->validateFilePaths($files);

            $destFullPath = $this->getFullPath($destPath);
            $this->ensureDirectoryExists($destFullPath);

            foreach ($files as $fileName) {
                $this->copySingleFile($fileName, $destFullPath, $destPath);
            }

            return true;
        } catch (Exception $e) {
            $this->logError('Copy files failed', $e, ['files' => $files, 'destination' => $destPath]);
            throw $e;
        }
    }

    /**
     * Gets the full system path for a relative path.
     *
     * @param  string  $path  Relative path
     * @return string Full system path
     */
    protected function getFullPath(string $path): string
    {
        $normalizedPath = ltrim($path, '/');
        $fullPath       = public_path("$this->basePath/$normalizedPath");

        $realPath = realpath($fullPath);
        if ($realPath !== false) {
            $realBasePath = realpath($this->fileBasePath);
            if ($realBasePath !== false && str_starts_with($realPath, $realBasePath)) {
                return $realPath;
            }
        }

        return rtrim($fullPath, '/');
    }

    // ==================== Helper Methods ====================

    /**
     * Get the real base path, ensuring it's accessible.
     *
     * @return string|false The real base path or false if not accessible
     */
    protected function getRealBasePath()
    {
        return realpath($this->fileBasePath);
    }

    /**
     * Normalize a relative path to ensure it starts with '/'.
     *
     * @param  string  $path  Path to normalize
     * @return string Normalized path
     */
    protected function normalizeRelativePath(string $path): string
    {
        return str_starts_with($path, '/') ? $path : '/'.$path;
    }

    /**
     * Process a directory entry iteratively, building subdirectory tree without recursion.
     */
    protected function processDirectoryIterative(string $directory, string $realBasePath): ?array
    {
        $realDirectory = realpath($directory);
        if ($realDirectory === false || ! str_starts_with($realDirectory, $realBasePath)) {
            return null;
        }

        if (! is_dir($realDirectory)) {
            return null;
        }

        $baseName = basename($directory);
        $dirName  = str_replace($this->fileBasePath, '', $directory);
        if (! str_starts_with($dirName, '/')) {
            $dirName = '/'.$dirName;
        }

        $item = $this->handleFolder($dirName, $baseName);

        // Build subdirectory tree iteratively using a stack
        $stack = [['dirName' => $dirName, 'item' => &$item]];
        while (! empty($stack)) {
            $current = array_pop($stack);
            $subPath = rtrim($this->fileBasePath.$current['dirName'], '/');
            $subs    = glob("$subPath/*", GLOB_ONLYDIR) ?: [];

            foreach ($subs as $sub) {
                $realSub = realpath($sub);
                if ($realSub === false || ! str_starts_with($realSub, $realBasePath) || ! is_dir($realSub)) {
                    continue;
                }

                $subBaseName = basename($sub);
                $subDirName  = str_replace($this->fileBasePath, '', $sub);
                if (! str_starts_with($subDirName, '/')) {
                    $subDirName = '/'.$subDirName;
                }

                $child                         = $this->handleFolder($subDirName, $subBaseName);
                $current['item']['children'][] = $child;

                // Push child to stack for further processing
                $stack[] = [
                    'dirName' => $subDirName,
                    'item'    => &$current['item']['children'][count($current['item']['children']) - 1],
                ];
            }
            unset($current);
        }

        return $item;
    }

    /**
     * Collect folders from a directory path.
     *
     * @param  string  $currentBasePath  Current base path
     * @param  string  $realBasePath  Real base path for validation
     * @return array Array of folder metadata
     */
    protected function collectFolders(string $currentBasePath, string $realBasePath): array
    {
        $directories = glob("$currentBasePath/*", GLOB_ONLYDIR) ?: [];
        $folders     = [];

        foreach ($directories as $directory) {
            $realDirectory = realpath($directory);
            if ($realDirectory === false || ! str_starts_with($realDirectory, $realBasePath)) {
                continue;
            }

            try {
                $baseName = basename($directory);
                $dirPath  = $this->normalizeRelativePath(str_replace($this->fileBasePath, '', $directory));

                $folders[] = [
                    'id'           => $dirPath,
                    'name'         => $baseName,
                    'path'         => $dirPath,
                    'is_dir'       => true,
                    'icon'         => 'bi bi-folder-fill',
                    'thumb'        => '',
                    'url'          => '',
                    'mime'         => 'directory',
                    'created_time' => @filemtime($realDirectory) ?: time(),
                ];
            } catch (Exception $e) {
                Log::warning('Skipping directory due to access restriction:', [
                    'directory' => $directory,
                    'error'     => $e->getMessage(),
                ]);
            }
        }

        return $folders;
    }

    /**
     * Collect lightweight file entries (name and path only, no metadata).
     * Metadata is loaded only for the current page items via enrichFileMetadata().
     */
    protected function collectFileEntries(string $currentBasePath, string $realBasePath, string $keyword = ''): array
    {
        $files  = glob($currentBasePath.'/*') ?: [];
        $images = [];

        foreach ($files as $file) {
            $realFile = realpath($file);
            if ($realFile === false || ! str_starts_with($realFile, $realBasePath)) {
                continue;
            }

            if (! is_file($realFile)) {
                continue;
            }

            $baseName = basename($file);
            if ($this->shouldSkipFile($baseName, $keyword)) {
                continue;
            }

            $fileName = $this->normalizeRelativePath(str_replace($this->fileBasePath, '', $file));

            $images[] = [
                'id'           => $fileName,
                'path'         => StorageService::storageKey(ltrim($fileName, '/')),
                'name'         => $baseName,
                'is_dir'       => false,
                'thumb'        => '',
                'url'          => '',
                'origin_url'   => '',
                'mime'         => '',
                'selected'     => false,
                'created_time' => @filemtime($realFile) ?: time(),
                '_realPath'    => $realFile,
            ];
        }

        return $images;
    }

    /**
     * Map a mime type to a Bootstrap Icons class.
     */
    protected function mimeToIcon(string $mime): string
    {
        if (str_starts_with($mime, 'image/')) {
            return 'bi bi-file-earmark-image';
        }
        if (str_starts_with($mime, 'video/')) {
            return 'bi bi-file-earmark-play';
        }
        if (str_starts_with($mime, 'audio/')) {
            return 'bi bi-file-earmark-music';
        }
        if ($mime === 'application/pdf') {
            return 'bi bi-file-earmark-pdf';
        }
        if (in_array($mime, ['application/zip', 'application/x-rar-compressed', 'application/x-tar', 'application/gzip', 'application/x-gzip', 'application/x-7z-compressed'], true)) {
            return 'bi bi-file-earmark-zip';
        }
        if (in_array($mime, ['application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.oasis.opendocument.text'], true)) {
            return 'bi bi-file-earmark-word';
        }
        if (in_array($mime, ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.oasis.opendocument.spreadsheet', 'text/csv'], true)) {
            return 'bi bi-file-earmark-spreadsheet';
        }
        if (in_array($mime, ['application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/vnd.oasis.opendocument.presentation'], true)) {
            return 'bi bi-file-earmark-slides';
        }
        if (in_array($mime, ['application/json', 'application/javascript', 'application/x-php', 'text/html', 'text/css', 'text/xml', 'application/xml'], true)) {
            return 'bi bi-file-earmark-code';
        }
        if (str_starts_with($mime, 'text/')) {
            return 'bi bi-file-earmark-text';
        }

        return 'bi bi-file-earmark';
    }

    /**
     * Enrich page items with full metadata (mime, thumbnails).
     * Only called for items on the current page to avoid unnecessary I/O.
     */
    protected function enrichFileMetadata(array $items): array
    {
        return array_map(function ($item) {
            if (($item['is_dir'] ?? false) || empty($item['_realPath'])) {
                unset($item['_realPath'], $item['created_time']);

                return $item;
            }

            $realPath = $item['_realPath'];
            $path     = $item['path'];

            $mime    = '';
            $isVideo = false;
            if (file_exists($realPath)) {
                $mime = mime_content_type($realPath);
                if (str_starts_with($mime, 'video/')) {
                    $isVideo = true;
                }
            }

            $item['mime']       = $mime;
            $item['icon']       = $this->mimeToIcon($mime);
            $item['origin_url'] = storage_url($path);
            $item['url']        = str_starts_with($mime, 'image/') ? image_resize($path) : '';
            $item['thumb']      = str_starts_with($mime, 'image/') ? storage_url($path) : '';

            unset($item['_realPath'], $item['created_time']);

            return $item;
        }, $items);
    }

    /**
     * Check if a file should be skipped based on exclusion rules or keyword filter.
     *
     * @param  string  $baseName  Base filename
     * @param  string  $keyword  Search keyword
     * @return bool True if file should be skipped
     */
    protected function shouldSkipFile(string $baseName, string $keyword): bool
    {
        if (in_array($baseName, self::EXCLUDED_FILES, true)) {
            return true;
        }

        return $keyword !== '' && ! str_contains($baseName, $keyword);
    }

    /**
     * Sort items by specified field and order.
     *
     * @param  array  $items  Items to sort
     * @param  string  $sort  Sort field (created or name)
     * @param  string  $order  Sort order (asc or desc)
     * @return array Sorted items
     */
    protected function sortItems(array $items, string $sort, string $order): array
    {
        if ($sort === self::SORT_FIELD_CREATED) {
            usort($items, function ($a, $b) use ($order) {
                $timeA = $a['created_time'] ?? 0;
                $timeB = $b['created_time'] ?? 0;

                return ($order === self::SORT_ORDER_DESC) ? $timeB - $timeA : $timeA - $timeB;
            });
        } else {
            usort($items, function ($a, $b) use ($order) {
                if (($a['is_dir'] ?? false) && ! ($b['is_dir'] ?? false)) {
                    return -1;
                }
                if (! ($a['is_dir'] ?? false) && ($b['is_dir'] ?? false)) {
                    return 1;
                }

                return ($order === self::SORT_ORDER_DESC) ?
                    strcasecmp($b['name'], $a['name']) :
                    strcasecmp($a['name'], $b['name']);
            });
        }

        return $items;
    }

    /**
     * Get empty file list result.
     *
     * @param  int  $page  Current page number
     * @return array Empty file list structure
     */
    protected function getEmptyFileList(int $page): array
    {
        return [
            'items'    => [],
            'total'    => 0,
            'page'     => $page,
            'per_page' => 20,
            'success'  => true,
        ];
    }

    /**
     * Validate that files array is not empty.
     *
     * @param  array  $files  Files array
     * @return void
     * @throws Exception If files array is empty
     */
    protected function validateFilesNotEmpty(array $files): void
    {
        if (empty($files)) {
            throw new Exception(trans('panel/media.no_files_selected'));
        }
    }

    /**
     * Validate and normalize file paths.
     *
     * @param  array  $files  Array of file paths
     * @return array Validated file paths
     */
    protected function validateFilePaths(array $files): array
    {
        $validatedFiles = [];
        foreach ($files as $file) {
            $validatedFiles[] = FileSecurityValidator::validateDirectoryPath($file);
        }

        return $validatedFiles;
    }

    /**
     * Ensure directory exists, throw exception if not.
     *
     * @param  string  $dirPath  Directory path
     * @return void
     * @throws Exception If directory does not exist
     */
    protected function ensureDirectoryExists(string $dirPath): void
    {
        if (! is_dir($dirPath)) {
            throw new Exception(trans('panel/media.target_dir_not_exist'));
        }
    }

    /**
     * Move a single file to destination directory.
     *
     * @param  string  $fileName  Source file name/path
     * @param  string  $destFullPath  Destination full path
     * @param  string  $destPath  Destination relative path (for logging)
     * @return void
     * @throws Exception If move operation fails
     */
    protected function moveSingleFile(string $fileName, string $destFullPath, string $destPath): void
    {
        $sourcePath   = $this->getFullPath($fileName);
        $destFilePath = rtrim($destFullPath, '/').'/'.basename($fileName);

        if (! file_exists($sourcePath)) {
            Log::warning('Source file not found:', ['path' => $sourcePath]);
            throw new Exception(trans('panel/media.source_file_not_exist'));
        }

        if (file_exists($destFilePath)) {
            throw new Exception(trans('panel/media.target_file_exists'));
        }

        if (! @rename($sourcePath, $destFilePath)) {
            Log::error('Failed to move file:', [
                'source'      => $sourcePath,
                'destination' => $destFilePath,
                'error'       => error_get_last(),
            ]);
            throw new Exception(trans('panel/media.move_failed'));
        }

        $newRelKey = ltrim(rtrim($destPath, '/'), '/').'/'.basename($fileName);
        $this->relocateMediaByKey(
            StorageService::storageKey($fileName),
            StorageService::storageKey($newRelKey)
        );
    }

    /**
     * Copy a single file to destination directory.
     *
     * @param  string  $fileName  Source file name/path
     * @param  string  $destFullPath  Destination full path
     * @param  string  $destPath  Destination relative path
     * @return void
     * @throws Exception If copy operation fails
     */
    protected function copySingleFile(string $fileName, string $destFullPath, string $destPath): void
    {
        $sourcePath   = $this->getFullPath($fileName);
        $destFilePath = rtrim($destFullPath, '/').'/'.basename($fileName);
        $newName      = basename($fileName);

        if (! file_exists($sourcePath)) {
            Log::warning('Source file not found:', ['path' => $sourcePath]);
            throw new Exception(trans('panel/media.source_file_not_exist'));
        }

        if (file_exists($destFilePath)) {
            $newName      = $this->getUniqueFileName($destPath, basename($fileName));
            $destFilePath = rtrim($destFullPath, '/').'/'.$newName;
        }

        if (! @copy($sourcePath, $destFilePath)) {
            Log::error('Failed to copy file:', [
                'source'      => $sourcePath,
                'destination' => $destFilePath,
                'error'       => error_get_last(),
            ]);
            throw new Exception(trans('panel/media.copy_failed'));
        }

        $newRelKey     = ltrim(rtrim($destPath, '/'), '/').'/'.$newName;
        $newStorageKey = StorageService::storageKey($newRelKey);
        $this->registerMediaCopy($newStorageKey, $destFilePath, $newName);
    }

    /**
     * Validate that paths are not empty.
     *
     * @param  string  $sourcePath  Source path
     * @param  string  $destPath  Destination path
     * @return void
     * @throws Exception If any path is empty
     */
    protected function validatePathsNotEmpty(string $sourcePath, string $destPath): void
    {
        if (empty($sourcePath) || empty($destPath)) {
            throw new Exception(trans('panel/media.empty_path'));
        }
    }

    /**
     * Validate that destination is not a subdirectory of source.
     *
     * @param  string  $sourcePath  Source path
     * @param  string  $destPath  Destination path
     * @return void
     * @throws Exception If destination is a subdirectory of source
     */
    protected function validateNotMovingToSubdirectory(string $sourcePath, string $destPath): void
    {
        if (str_starts_with($destPath, $sourcePath.'/')) {
            throw new Exception(trans('panel/media.cannot_move_to_subdirectory'));
        }
    }

    /**
     * Ensure path does not exist, throw exception if it does.
     *
     * @param  string  $path  Path to check
     * @return void
     * @throws Exception If path exists
     */
    protected function ensurePathDoesNotExist(string $path): void
    {
        if (is_dir($path) || file_exists($path)) {
            throw new Exception(trans('panel/media.target_dir_exist'));
        }
    }

    /**
     * Delete a directory.
     *
     * @param  string  $dirPath  Directory path
     * @return void
     * @throws Exception If directory is not empty or deletion fails
     */
    protected function deleteDirectory(string $dirPath): void
    {
        $files = glob($dirPath.'/*');
        if ($files) {
            throw new Exception(trans('panel/media.directory_not_empty'));
        }

        $relPath = $this->relativePathFromFull($dirPath);
        if ($relPath !== null) {
            $this->removeMediaUnderDirectory($relPath);
        }

        if (! @rmdir($dirPath)) {
            Log::error('Failed to delete directory:', [
                'path'  => $dirPath,
                'error' => error_get_last(),
            ]);
            throw new Exception(trans('panel/media.delete_failed'));
        }
    }

    /**
     * Delete a file.
     *
     * @param  string  $filePath  File path
     * @return void
     * @throws Exception If deletion fails
     */
    protected function deleteFile(string $filePath): void
    {
        $relPath = $this->relativePathFromFull($filePath);
        if ($relPath !== null) {
            $this->removeMediaByKey(StorageService::storageKey($relPath));
        }

        if (! @unlink($filePath)) {
            Log::error('Failed to delete file:', [
                'path'  => $filePath,
                'error' => error_get_last(),
            ]);
            throw new Exception(trans('panel/media.delete_failed'));
        }
    }

    /**
     * Convert an absolute filesystem path back to a path relative to the media
     * base directory. Returns null when the path is outside the media tree.
     */
    protected function relativePathFromFull(string $fullPath): ?string
    {
        $realBase = $this->getRealBasePath();
        if (! is_string($realBase) || $realBase === '') {
            return null;
        }
        $realBase = rtrim($realBase, '/').'/';
        $realFull = realpath($fullPath);
        if ($realFull === false) {
            $realFull = rtrim($fullPath, '/');
        }
        if (! str_starts_with($realFull, $realBase)) {
            return null;
        }

        return ltrim(substr($realFull, strlen($realBase)), '/');
    }

    /**
     * Log error with context.
     *
     * @param  string  $message  Error message
     * @param  Exception  $exception  Exception object
     * @param  array  $context  Additional context
     * @return void
     */
    protected function logError(string $message, Exception $exception, array $context = []): void
    {
        Log::error($message, array_merge([
            'error' => $exception->getMessage(),
        ], $context));
    }

    /**
     * Register a media record after a fresh upload (dedups by checksum).
     */
    protected function registerMediaFromUpload(UploadedFile $file, string $storageKey): void
    {
        try {
            MediaUrlResolver::getInstance()->registerFromUploadedFile($file, $storageKey, 'local');
        } catch (\Throwable $e) {
            Log::warning('Media register (upload) failed: '.$e->getMessage(), ['key' => $storageKey]);
        }
    }

    /**
     * Register a media record for a file that landed on disk via a non-upload
     * path (e.g. downloadRemoteFile). Reads size/mime from the stored file.
     */
    protected function registerMediaFromLocalPath(string $storageKey, string $absolutePath, ?string $originalName = null): void
    {
        try {
            if (! is_file($absolutePath)) {
                return;
            }
            MediaUrlResolver::getInstance()->register([
                'disk'          => 'local',
                'storage_key'   => $storageKey,
                'original_name' => $originalName ?? basename($absolutePath),
                'checksum'      => hash_file('sha256', $absolutePath) ?: null,
                'mime'          => mime_content_type($absolutePath) ?: null,
                'size'          => filesize($absolutePath) ?: 0,
                'source'        => 'url_import',
            ]);
        } catch (\Throwable $e) {
            Log::warning('Media register (local) failed: '.$e->getMessage(), ['key' => $storageKey]);
        }
    }

    /**
     * Update a single media record's storage_key after a file rename/move.
     */
    protected function relocateMediaByKey(string $oldKey, string $newKey): void
    {
        try {
            MediaUrlResolver::getInstance()->relocateByKey($oldKey, $newKey);
        } catch (\Throwable $e) {
            Log::warning('Media relocate failed: '.$e->getMessage(), ['old' => $oldKey, 'new' => $newKey]);
        }
    }

    /**
     * Register a media record for a freshly-copied file.
     */
    protected function registerMediaCopy(string $storageKey, string $absolutePath, ?string $originalName = null): void
    {
        try {
            MediaUrlResolver::getInstance()->registerCopy($storageKey, 'local', [
                'original_name' => $originalName ?? basename($absolutePath),
                'checksum'      => is_file($absolutePath) ? hash_file('sha256', $absolutePath) ?: null : null,
                'mime'          => is_file($absolutePath) ? (mime_content_type($absolutePath) ?: null) : null,
                'size'          => is_file($absolutePath) ? (filesize($absolutePath) ?: 0) : 0,
                'source'        => 'copy',
            ]);
        } catch (\Throwable $e) {
            Log::warning('Media copy register failed: '.$e->getMessage(), ['key' => $storageKey]);
        }
    }

    /**
     * Soft-delete a single media record after its file is removed.
     */
    protected function removeMediaByKey(string $storageKey): void
    {
        try {
            MediaUrlResolver::getInstance()->removeByKey($storageKey);
        } catch (\Throwable $e) {
            Log::warning('Media remove failed: '.$e->getMessage(), ['key' => $storageKey]);
        }
    }

    /**
     * Soft-delete every media record under a directory (used by deleteDirectory).
     */
    protected function removeMediaUnderDirectory(string $relDirPath): void
    {
        $prefix = StorageService::storageKey(ltrim($relDirPath, '/').'/');
        try {
            foreach (MediaFile::query()->where('storage_key', 'like', $prefix.'%')->cursor() as $media) {
                $media->delete();
            }
        } catch (\Throwable $e) {
            Log::warning('Media remove under dir failed: '.$e->getMessage(), ['prefix' => $prefix]);
        }
    }

    /**
     * Rewrite storage_key for every media record under a directory (used by moveDirectory).
     */
    protected function relocateMediaUnderDirectory(string $oldRelDirPath, string $newRelDirPath): void
    {
        $oldPrefix = StorageService::storageKey(ltrim($oldRelDirPath, '/').'/');
        $newPrefix = StorageService::storageKey(ltrim($newRelDirPath, '/').'/');
        try {
            $resolver = MediaUrlResolver::getInstance();
            foreach (MediaFile::query()->where('storage_key', 'like', $oldPrefix.'%')->cursor() as $media) {
                $newKey = $newPrefix.substr($media->storage_key, strlen($oldPrefix));
                $resolver->relocate($media->id, $newKey);
            }
        } catch (\Throwable $e) {
            Log::warning('Media relocate under dir failed: '.$e->getMessage(), ['old' => $oldPrefix, 'new' => $newPrefix]);
        }
    }
}
