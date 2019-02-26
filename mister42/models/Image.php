<?php
namespace app\models;

class Image {
	public static function getAverageImageColor(string $image): string {
		$i = imagecreatefromstring($image);
		$rTotal = $gTotal = $bTotal = $total = 0;
		list($width, $height) = getimagesizefromstring($image);
		for ($x = 0; $x < $width; $x++) :
			for ($y = 0; $y < $height; $y++) :
				$rgb = imagecolorat($i, $x, $y);
				$rTotal += ($rgb >> 16) & 0xFF;
				$gTotal += ($rgb >> 8) & 0xFF;
				$bTotal += $rgb & 0xFF;
				$total++;
			endfor;
		endfor;
		return sprintf('#%02X%02X%02X', $rTotal / $total, $gTotal / $total, $bTotal / $total);
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
