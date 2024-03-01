<?php

namespace app\core\src\utilities;

class Image {

    private string $imagePath;
    private string $imageType;

    private const DEFAULT_RESIZE_HEIGHT = 100;
    private const DEFAULT_RESIZE_WIDTH = 100;

    public function __construct($imagePath) {
        $this->imagePath = $imagePath;
    }

    public function imageCreateFrom(): ?\GdImage {

        $source = null;
        if ($this->imageType == IMAGETYPE_JPEG) $source = imagecreatefromjpeg($this->imagePath);
        if ($this->imageType == IMAGETYPE_PNG)  $source = imagecreatefrompng($this->imagePath);
        if ($this->imageType == IMAGETYPE_GIF)  $source = imagecreatefromgif($this->imagePath);

        return $source;
    }

    public function resizeImage($width = self::DEFAULT_RESIZE_WIDTH, $height = self::DEFAULT_RESIZE_HEIGHT): bool {
        $imageInfo = getimagesize($this->imagePath);
        $this->imageType = $imageInfo[2];
        $source = $this->imageCreateFrom();
        $resized = imagescale($source, $width, $height);

        imagejpeg($resized, $this->imagePath);
        imagedestroy($source);
        imagedestroy($resized);

        return true;
    }
}