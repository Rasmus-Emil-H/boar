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

        $gdImage = null;
        if ($this->imageType == IMAGETYPE_JPEG) $gdImage = imagecreatefromjpeg($this->imagePath);
        if ($this->imageType == IMAGETYPE_PNG)  $gdImage = imagecreatefrompng($this->imagePath);
        if ($this->imageType == IMAGETYPE_GIF)  $gdImage = imagecreatefromgif($this->imagePath);

        return $gdImage;
    }

    public function resizeImage($width = self::DEFAULT_RESIZE_WIDTH, $height = self::DEFAULT_RESIZE_HEIGHT): bool {
        $imageInfo = getimagesize($this->imagePath);
        $this->imageType = $imageInfo[2];
        $gdImage = $this->imageCreateFrom();
        $resized = imagescale($gdImage, $width, $height);

        imagejpeg($resized, $this->imagePath);
        imagedestroy($gdImage);
        imagedestroy($resized);

        return true;
    }
}