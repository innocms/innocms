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
use Illuminate\Support\Facades\Log;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ImageService
{
    private string $originImage;

    private string $image;

    private string $imagePath;

    private string $placeholderImage;

    const PLACEHOLDER_IMAGE = 'images/placeholder.png';

    /**
     * @throws Exception
     */
    public function __construct($image)
    {
        $this->originImage      = (string) $image;
        $this->placeholderImage = system_setting('placeholder', self::PLACEHOLDER_IMAGE);
        if (! is_file(public_path($this->placeholderImage))) {
            $this->placeholderImage = self::PLACEHOLDER_IMAGE;
        }
        $this->image     = $image ?: $this->placeholderImage;
        $this->imagePath = public_path($this->image);
        if (! is_file($this->imagePath)) {
            $this->image     = $this->placeholderImage;
            $this->imagePath = public_path($this->placeholderImage);
        }
    }

    /**
     * @param  $image
     * @return static
     * @throws Exception
     */
    public static function getInstance($image): self
    {
        return new self($image);
    }

    /**
     * Set plugin directory name
     *
     * @param  $dirName
     * @return $this
     */
    public function setPluginDirName($dirName): static
    {
        $originImage     = $this->originImage;
        $this->imagePath = plugin_path("{$dirName}/Static").$originImage;
        if (file_exists($this->imagePath)) {
            $this->image = strtolower('plugin/'.$dirName.$originImage);
        } else {
            $this->image     = $this->placeholderImage;
            $this->imagePath = public_path($this->image);
        }

        return $this;
    }

    /**
     * Generate thumbnail image
     *
     * @param  int  $width
     * @param  int  $height
     * @return string
     */
    public function resize(int $width = 100, int $height = 100): string
    {
        try {
            $extension = pathinfo($this->imagePath, PATHINFO_EXTENSION);
            $newImage  = 'cache/'.mb_substr($this->image, 0, mb_strrpos($this->image, '.')).'-'.$width.'x'.$height.'.'.$extension;

            $newImagePath = public_path($newImage);
            if (! is_file($newImagePath) || (filemtime($this->imagePath) > filemtime($newImagePath))) {
                create_directories(dirname($newImagePath));

                $manager = new ImageManager(new Driver());
                $image   = $manager->read($this->imagePath);
                $image->scale($width, $height);
                $image->save($newImagePath);
            }

            return asset($newImage);
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return $this->originUrl();
        }
    }

    /**
     * Get original image url.
     *
     * @return string
     */
    public function originUrl(): string
    {
        return asset($this->image);
    }
}
