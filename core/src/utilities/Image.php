<?php

namespace app\core\src\utilities;

class Image {

    private string $imagePath;
    private string $imageType;

    private const DEFAULT_RESIZE_HEIGHT = 600;
    private const DEFAULT_RESIZE_WIDTH = 600;

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

    private function evaluateDimensions(array $originalMeasures): array {
        list($width, $height) = $originalMeasures;

        return $width > self::DEFAULT_RESIZE_WIDTH || $height > self::DEFAULT_RESIZE_HEIGHT ? $this->calculateAspectRatio($width, $height) : ['width' => $width, 'height' => $height];
    }

    private function calculateAspectRatio(int $width, int $height): array {
        if ($width <= self::DEFAULT_RESIZE_WIDTH && $height <= self::DEFAULT_RESIZE_HEIGHT) return ['width' => $width, 'height' => $height];
        
        $aspectRatio = $width / $height;

        return self::DEFAULT_RESIZE_WIDTH / $aspectRatio > self::DEFAULT_RESIZE_HEIGHT ? 
            ['width' => self::DEFAULT_RESIZE_HEIGHT * $aspectRatio, 'height' => self::DEFAULT_RESIZE_HEIGHT] : 
            ['width' => self::DEFAULT_RESIZE_WIDTH, 'height' => self::DEFAULT_RESIZE_WIDTH / $aspectRatio];
    }

    public function resizeImage($width = self::DEFAULT_RESIZE_WIDTH, $height = self::DEFAULT_RESIZE_HEIGHT): bool {
        $imageInfo = getimagesize($this->imagePath);
        $this->imageType = $imageInfo[2];
        $gdImage = $this->imageCreateFrom();
        $dimensions = $this->evaluateDimensions($imageInfo);
        $resized = imagescale($gdImage, (int)$dimensions['width'], (int)$dimensions['height']);

        imagejpeg($resized, $this->imagePath);
        imagedestroy($gdImage);
        imagedestroy($resized);

        return true;
    }
}