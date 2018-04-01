<?php
namespace app\models;
use Yii;

class Image {
	public function getAverageColor(string $image): string {
		$i = imagecreatefromstring($image);
		for ($x=0; $x < imagesx($i); $x++) {
			for ($y=0; $y < imagesy($i); $y++) {
				$rgb = imagecolorat($i, $x, $y);
				$r = ($rgb >> 16) & 0xFF;
				$g = ($rgb >> 8) & 0xFF;
				$b = $rgb & 0xFF;
				$rTotal += $r;
				$gTotal += $g;
				$bTotal += $b;
				$total++;
			}
		}
		return sprintf('#%02X%02X%02X', $rTotal/$total, $gTotal/$total, $bTotal/$total); 
	}
}
