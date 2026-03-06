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
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class FileManagerService
{
    protected string $fileBasePath = '';

    protected string $mediaDir = 'static/media';

    protected string $basePath = '';

    protected const EXCLUDED_FILES = ['index.html'];

    protected const SORT_FIELD_CREATED = 'created';

    protected const SORT_ORDER_DESC = 'desc';

    public function __construct()
    {
        $this->basePath     = '/'.$this->mediaDir;
        $this->fileBasePath = public_path().$this->basePath;
    }

    public function getDirectories(string $baseFolder = '/'): array
    {
        $baseFolder   = FileSecurityValidator::validateDirectoryPath($baseFolder);
        $realBasePath = realpath($this->fileBasePath);
        if ($realBasePath === false) {
            return [];
        }

        $currentBasePath = rtrim($this->fileBasePath.$baseFolder, '/');
        $directories     = glob("$currentBasePath/*", GLOB_ONLYDIR) ?: [];

        $result = [];
        foreach ($directories as $directory) {
            $realDir = realpath($directory);
            if ($realDir === false || ! str_starts_with($realDir, $realBasePath)) {
                continue;
            }

            $baseName = basename($directory);
            $dirName  = str_replace($this->fileBasePath, '', $directory);
            if (! str_starts_with($dirName, '/')) {
                $dirName = '/'.$dirName;
            }

            try {
                $item = [
                    'name' => $baseName,
                    'path' => $dirName,
                ];
                $subDirectories = $this->getDirectories($dirName);
                if (! empty($subDirectories)) {
                    $item['children'] = $subDirectories;
                }
                $result[] = $item;
            } catch (\Exception $e) {
                Log::warning('Skipping directory due to error:', ['directory' => $directory, 'error' => $e->getMessage()]);
            }
        }

        return $result;
    }

    public function getFiles(string $baseFolder, string $keyword = '', string $sort = self::SORT_FIELD_CREATED, string $order = self::SORT_ORDER_DESC, int $page = 1, int $perPage = 20): array
    {
        $baseFolder   = FileSecurityValidator::validateDirectoryPath($baseFolder);
        $realBasePath = realpath($this->fileBasePath);
        if ($realBasePath === false) {
            return $this->getEmptyFileList($page);
        }

        $currentBasePath = rtrim($this->fileBasePath.$baseFolder, '/');
        $folders         = $this->collectFolders($currentBasePath, $realBasePath);
        $images          = $this->collectFiles($currentBasePath, $realBasePath, $keyword);

        $allItems = array_merge($folders, $images);
        $allItems = $this->sortItems($allItems, $sort, $order);
        $allItems = $this->removeTemporaryFields($allItems);

        return $this->paginateItems($allItems, $page, $perPage);
    }

    public function createDirectory(string $path): bool
    {
        try {
            $path = FileSecurityValidator::validateDirectoryPath($path);

            $folderPath = $this->getFullPath($path);
            if (is_dir($folderPath)) {
                throw new Exception('Directory already exists');
            }

            create_directories("$this->mediaDir/$path");

            return true;
        } catch (Exception $e) {
            Log::error('Create directory failed:', ['path' => $path, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

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
                throw new Exception('Failed to move directory');
            }

            return true;
        } catch (Exception $e) {
            Log::error('Move directory failed', ['source' => $sourcePath, 'destination' => $destPath, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function moveFiles(array $files, string $destPath): bool
    {
        try {
            $this->validateFilesNotEmpty($files);
            $destPath = FileSecurityValidator::validateDirectoryPath($destPath);
            $files    = $this->validateFilePaths($files);

            $destFullPath = $this->getFullPath($destPath);
            $this->ensureDirectoryExists($destFullPath);

            foreach ($files as $fileName) {
                $this->moveSingleFile($fileName, $destFullPath);
            }

            return true;
        } catch (Exception $e) {
            Log::error('Move files failed', ['files' => $files, 'destination' => $destPath, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function deleteDirectoryOrFile(string $path): bool
    {
        try {
            $path     = FileSecurityValidator::validateDirectoryPath($path);
            $fullPath = $this->getFullPath($path);

            if (is_dir($fullPath)) {
                $files = glob($fullPath.'/*');
                if ($files) {
                    throw new Exception('Directory is not empty');
                }
                if (! @rmdir($fullPath)) {
                    throw new Exception('Failed to delete directory');
                }
            } elseif (file_exists($fullPath)) {
                if (! @unlink($fullPath)) {
                    throw new Exception('Failed to delete file');
                }
            } else {
                throw new Exception('File or directory not found');
            }

            return true;
        } catch (Exception $e) {
            Log::error('Delete failed', ['path' => $path, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function updateName(string $originPath, string $newPath): bool
    {
        try {
            $originPath = FileSecurityValidator::validateDirectoryPath($originPath);
            $newPath    = FileSecurityValidator::validateDirectoryPath($newPath);

            $newFileName = basename($newPath);
            FileSecurityValidator::validateFileName($newFileName);

            if (pathinfo($newFileName, PATHINFO_EXTENSION)) {
                FileSecurityValidator::validateFileExtension($newFileName);
            }

            $originFullPath = $this->getFullPath($originPath);
            $newFullPath    = $this->getFullPath($newPath);

            if (! is_dir($originFullPath) && ! file_exists($originFullPath)) {
                throw new Exception('Target does not exist');
            }

            if (file_exists($newFullPath)) {
                $dirPath     = dirname($newPath);
                $newName     = $this->getUniqueFileName($dirPath, basename($newPath));
                $newPath     = $dirPath === '/' ? "/$newName" : "$dirPath/$newName";
                $newFullPath = $this->getFullPath($newPath);
            }

            if (! @rename($originFullPath, $newFullPath)) {
                throw new Exception('Failed to rename');
            }

            return true;
        } catch (Exception $e) {
            Log::error('Rename failed', ['origin' => $originPath, 'new' => $newPath, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function uploadFile(UploadedFile $file, string $savePath, string $originName): string
    {
        FileSecurityValidator::validateFile($originName);
        $savePath = FileSecurityValidator::validateDirectoryPath($savePath);

        $originName = $this->getUniqueFileName($savePath, $originName);
        $filePath   = $file->storeAs($savePath, $originName, 'media');

        return asset($this->mediaDir.'/'.$filePath);
    }

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
            Log::error('Copy files failed', ['files' => $files, 'destination' => $destPath, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function getUniqueFileName(string $savePath, string $originName): string
    {
        $fullPath = $this->getFullPath("$savePath/$originName");
        if (file_exists($fullPath)) {
            $originName = $this->getNewFileName($originName);
            return $this->getUniqueFileName($savePath, $originName);
        }
        return $originName;
    }

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

    // ==================== Helper Methods ====================

    protected function getFullPath(string $path): string
    {
        $normalizedPath = ltrim($path, '/');
        $fullPath       = public_path("$this->basePath/$normalizedPath");

        $realPath = realpath(dirname($fullPath));
        if ($realPath !== false) {
            $realBasePath = realpath($this->fileBasePath);
            if ($realBasePath !== false && str_starts_with($realPath, $realBasePath)) {
                return rtrim($fullPath, '/');
            }
        }

        return rtrim($fullPath, '/');
    }

    protected function collectFolders(string $currentBasePath, string $realBasePath): array
    {
        $directories = glob("$currentBasePath/*", GLOB_ONLYDIR) ?: [];
        $folders     = [];

        foreach ($directories as $directory) {
            $realDirectory = realpath($directory);
            if ($realDirectory === false || ! str_starts_with($realDirectory, $realBasePath)) {
                continue;
            }

            $baseName = basename($directory);
            $dirPath  = str_replace($this->fileBasePath, '', $directory);
            if (! str_starts_with($dirPath, '/')) {
                $dirPath = '/'.$dirPath;
            }

            $folders[] = [
                'id'           => $dirPath,
                'name'         => $baseName,
                'path'         => $dirPath,
                'is_dir'       => true,
                'thumb'        => asset('images/icons/folder.png'),
                'url'          => '',
                'mime'         => 'directory',
                'created_time' => @filemtime($realDirectory) ?: time(),
            ];
        }

        return $folders;
    }

    protected function collectFiles(string $currentBasePath, string $realBasePath, string $keyword = ''): array
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

            $filePath = str_replace($this->fileBasePath, '', $file);
            if (! str_starts_with($filePath, '/')) {
                $filePath = '/'.$filePath;
            }

            $thumbPath = $path = "$this->mediaDir$filePath";
            $mime = mime_content_type($realFile) ?: '';
            if (str_starts_with($mime, 'application/')) {
                $thumbPath = 'images/panel/doc.png';
            } elseif (str_starts_with($mime, 'video/')) {
                $thumbPath = 'images/panel/video.png';
            }

            $images[] = [
                'id'           => $filePath,
                'path'         => '/'.$path,
                'name'         => $baseName,
                'origin_url'   => image_origin($path),
                'url'          => image_resize($thumbPath),
                'mime'         => $mime,
                'selected'     => false,
                'created_time' => @filemtime($realFile) ?: time(),
            ];
        }

        return $images;
    }

    protected function shouldSkipFile(string $baseName, string $keyword): bool
    {
        if (in_array($baseName, self::EXCLUDED_FILES, true)) {
            return true;
        }
        return $keyword !== '' && ! str_contains($baseName, $keyword);
    }

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

    protected function removeTemporaryFields(array $items): array
    {
        return array_map(function ($item) {
            unset($item['created_time']);
            return $item;
        }, $items);
    }

    protected function paginateItems(array $items, int $page, int $perPage): array
    {
        $collection   = collect($items);
        $currentItems = $collection->forPage($page, $perPage);

        return [
            'images'      => $currentItems->values(),
            'image_total' => $collection->count(),
            'image_page'  => $page,
        ];
    }

    protected function getEmptyFileList(int $page): array
    {
        return [
            'images'      => [],
            'image_total' => 0,
            'image_page'  => $page,
        ];
    }

    protected function validateFilesNotEmpty(array $files): void
    {
        if (empty($files)) {
            throw new Exception('No files selected');
        }
    }

    protected function validateFilePaths(array $files): array
    {
        $validatedFiles = [];
        foreach ($files as $file) {
            $validatedFiles[] = FileSecurityValidator::validateDirectoryPath($file);
        }
        return $validatedFiles;
    }

    protected function ensureDirectoryExists(string $dirPath): void
    {
        if (! is_dir($dirPath)) {
            throw new Exception('Target directory does not exist');
        }
    }

    protected function moveSingleFile(string $fileName, string $destFullPath): void
    {
        $sourcePath   = $this->getFullPath($fileName);
        $destFilePath = rtrim($destFullPath, '/').'/'.basename($fileName);

        if (! file_exists($sourcePath)) {
            throw new Exception('Source file does not exist');
        }

        if (file_exists($destFilePath)) {
            @unlink($destFilePath);
        }

        if (! @rename($sourcePath, $destFilePath)) {
            throw new Exception('Failed to move file');
        }
    }

    protected function copySingleFile(string $fileName, string $destFullPath, string $destPath): void
    {
        $sourcePath   = $this->getFullPath($fileName);
        $destFilePath = rtrim($destFullPath, '/').'/'.basename($fileName);

        if (! file_exists($sourcePath)) {
            throw new Exception('Source file does not exist');
        }

        if (file_exists($destFilePath)) {
            $newName      = $this->getUniqueFileName($destPath, basename($fileName));
            $destFilePath = rtrim($destFullPath, '/').'/'.$newName;
        }

        if (! @copy($sourcePath, $destFilePath)) {
            throw new Exception('Failed to copy file');
        }
    }

    protected function validatePathsNotEmpty(string $sourcePath, string $destPath): void
    {
        if (empty($sourcePath) || empty($destPath)) {
            throw new Exception('Path cannot be empty');
        }
    }

    protected function validateNotMovingToSubdirectory(string $sourcePath, string $destPath): void
    {
        if (str_starts_with($destPath, $sourcePath.'/')) {
            throw new Exception('Cannot move directory to its subdirectory');
        }
    }

    protected function ensurePathDoesNotExist(string $path): void
    {
        if (is_dir($path) || file_exists($path)) {
            throw new Exception('Target already exists');
        }
    }
}
