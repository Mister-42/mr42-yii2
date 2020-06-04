<?php

namespace mister42\models;

use Imagick;

class Image
{
    public static function getAverageImageColor(string $image): string
    {
        $img = imagecreatefromstring($image);
        [$width, $height] = getimagesizefromstring($image);

        $tmp = imagecreatetruecolor(1, 1);
        imagecopyresampled($tmp, $img, 0, 0, 0, 0, 1, 1, $width, $height);
        $rgb = imagecolorat($tmp, 0, 0);

        imagedestroy($img);
        imagedestroy($tmp);
        return sprintf('#%02X%02X%02X', ($rgb >> 16) & 0xFF, ($rgb >> 8) & 0xFF, $rgb & 0xFF);
    }

    public static function isValid(string $image): bool
    {
        $img = imagecreatefromstring($image);
        $isValid = $img !== false;
        if (is_resource($img)) {
            imagedestroy($img);
        }
        return $isValid;
    }

    public static function resize(string $image, int $size): string
    {
        $imagick = new Imagick();
        $imagick->readImageBlob($image);
        $imagick->resizeImage($size, $size, Imagick::FILTER_LANCZOS, 1, true);
        $imagick->stripImage();
        $imagick->setImageCompressionQuality(85);
        $imagick->setInterlaceScheme(Imagick::INTERLACE_PLANE);
        $imagick->setImageFormat('jpg');
        $image = (string) $imagick;
        $imagick->destroy();

        return $image;
    }
}
