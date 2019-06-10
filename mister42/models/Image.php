<?php
namespace app\models;
use DOMDocument;
use Yii;
use yii\helpers\{ArrayHelper, Html};

class Image {
	public static function getAverageImageColor(string $image): string {
		$img = imagecreatefromstring($image);
		[$width, $height] = getimagesizefromstring($image);

		$tmp = imagecreatetruecolor(1, 1);
		imagecopyresampled($tmp, $img, 0, 0, 0, 0, 1, 1, $width, $height);
		$rgb = imagecolorat($tmp, 0, 0);

		imagedestroy($img);
		imagedestroy($tmp);
		return sprintf('#%02X%02X%02X', ($rgb >> 16) & 0xFF, ($rgb >> 8) & 0xFF, $rgb & 0xFF);
	}

	public static function resize(string $image, int $size): string {
		$process = proc_open("convert -resize {$size} -strip -quality 85% -interlace Plane - jpg:-", [['pipe', 'r'], ['pipe', 'w']], $pipes);
		if (is_resource($process)) :
			fwrite($pipes[0], $image);
			fclose($pipes[0]);

			$image = stream_get_contents($pipes[1]);
			fclose($pipes[1]);

			proc_close($process);
		endif;

		return $image;
	}
}
