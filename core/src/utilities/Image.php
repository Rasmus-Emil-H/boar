<?php

namespace app\core\src\utilities;

class Image {

    private string $imagePath;
    private int $imageType;

    private const DEFAULT_RESIZE_HEIGHT = 2000;
    private const DEFAULT_RESIZE_WIDTH = 2000;

    private const WIDTH = 'width'; 
    private const HEIGHT = 'height';

    public function __construct(string $imagePath) {
        $this->imagePath = $imagePath;
    }

    private function imageCreateFrom(): ?\GdImage {
        $gdImage = null;
        if ($this->imageType == IMAGETYPE_JPEG) $gdImage = imagecreatefromjpeg($this->imagePath);
        if ($this->imageType == IMAGETYPE_PNG)  $gdImage = imagecreatefrompng($this->imagePath);
        if ($this->imageType == IMAGETYPE_GIF)  $gdImage = imagecreatefromgif($this->imagePath);

        return $gdImage;
    }

    private function evaluateDimensions(array $originalMeasures): array {
        list($width, $height) = $originalMeasures;

        return ($width > self::DEFAULT_RESIZE_WIDTH || $height > self::DEFAULT_RESIZE_HEIGHT) 
            ? $this->calculateAspectRatio($width, $height) 
            : [self::WIDTH => $width, self::HEIGHT => $height];
    }

    private function calculateAspectRatio(int $width, int $height): array {
        if ($width <= self::DEFAULT_RESIZE_WIDTH && $height <= self::DEFAULT_RESIZE_HEIGHT)
            return [self::WIDTH => $width, self::HEIGHT => $height];

        $aspectRatio = $width / $height;

        return $width / $height > self::DEFAULT_RESIZE_WIDTH / self::DEFAULT_RESIZE_HEIGHT ? 
        [self::WIDTH => self::DEFAULT_RESIZE_WIDTH, self::HEIGHT => self::DEFAULT_RESIZE_WIDTH / $aspectRatio] :
        [self::WIDTH => self::DEFAULT_RESIZE_HEIGHT * $aspectRatio, self::HEIGHT => self::DEFAULT_RESIZE_HEIGHT];
    }

    public function resizeImage(int $newWidth = self::DEFAULT_RESIZE_WIDTH, int $newHeight = self::DEFAULT_RESIZE_HEIGHT): bool {
        $imageInfo = getimagesize($this->imagePath);
        if ($imageInfo === false)
            return false;

        $this->imageType = $imageInfo[2];
        $gdImage = $this->imageCreateFrom();

        if ($gdImage === null)
            return false;

        $dimensions = $this->evaluateDimensions([$newWidth, $newHeight]);
        $resized = imagescale($gdImage, $dimensions[self::WIDTH], $dimensions[self::HEIGHT], IMG_BILINEAR_FIXED);

        if ($resized === false) {
            imagedestroy($gdImage);
            return false;
        }

        $success = false;

        switch ($this->imageType) {
            case IMAGETYPE_JPEG:
                $success = imagejpeg($resized, $this->imagePath);
                break;
            case IMAGETYPE_PNG:
                $success = imagepng($resized, $this->imagePath);
                break;
            case IMAGETYPE_GIF:
                $success = imagegif($resized, $this->imagePath);
                break;
            default:
                return false;
        }

        imagedestroy($gdImage);
        imagedestroy($resized);

        return $success;
    }
}
