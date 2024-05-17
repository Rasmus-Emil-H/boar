<?php

namespace app\core\src\utilities;

class Image {

    private string $imagePath;
    private string $imageType;

    private const DEFAULT_RESIZE_HEIGHT = 1000;
    private const DEFAULT_RESIZE_WIDTH = 1000;

    private const WIDTH = 'width'; 
    private const HEIGHT = 'width'; 

    public function __construct(string $imagePath) {
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

        return $width > self::DEFAULT_RESIZE_WIDTH || $height > self::DEFAULT_RESIZE_HEIGHT ? $this->calculateAspectRatio($width, $height) : [self::WIDTH => $width, self::HEIGHT => $height];
    }

    private function calculateAspectRatio(int $width, int $height): array {
        if ($width <= self::DEFAULT_RESIZE_WIDTH && $height <= self::DEFAULT_RESIZE_HEIGHT) return [self::WIDTH => $width, self::HEIGHT => $height];
        
        $aspectRatio = $width / $height;

        return self::DEFAULT_RESIZE_WIDTH / $aspectRatio > self::DEFAULT_RESIZE_HEIGHT ? 
            [self::WIDTH => self::DEFAULT_RESIZE_HEIGHT * $aspectRatio, self::HEIGHT => self::DEFAULT_RESIZE_HEIGHT] : 
            [self::WIDTH => self::DEFAULT_RESIZE_WIDTH, self::HEIGHT => self::DEFAULT_RESIZE_WIDTH / $aspectRatio];
    }

    public function resizeImage($width = self::DEFAULT_RESIZE_WIDTH, $height = self::DEFAULT_RESIZE_HEIGHT): bool {
        $imageInfo = getimagesize($this->imagePath);
        $this->imageType = $imageInfo[2];
        $gdImage = $this->imageCreateFrom();
        $dimensions = $this->evaluateDimensions($imageInfo);
        $resized = imagescale($gdImage, (int)$dimensions[self::WIDTH], (int)$dimensions[self::HEIGHT]);

        imagejpeg($resized, $this->imagePath);
        imagedestroy($gdImage);
        imagedestroy($resized);

        return true;
    }
}