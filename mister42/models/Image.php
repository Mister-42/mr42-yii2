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

	public static function loadSvg(string $fileName): DOMDocument {
		$doc = new DOMDocument();
		if (!file_exists(Yii::getAlias($fileName)))
			$fileName = '@bower/fontawesome/svgs/solid/question-circle.svg';

		$doc->load(Yii::getAlias($fileName));
		return $doc;
	}

	public static function processSvg(DOMDocument $doc, array $options): string {
		ArrayHelper::setValue($options, 'aria-hidden', 'true');
		ArrayHelper::setValue($options, 'role', 'img');

		$svg = $doc->getElementsByTagName('svg')->item(0);
		if ($title = ArrayHelper::remove($options, 'title'))
			$svg->appendChild($doc->createElement('title', $title));
		[,, $svgWidth, $svgHeight] = explode(' ', $svg->getAttribute('viewBox'));
		switch ($height = ArrayHelper::getValue($options, 'height', 0)) :
			case 0:
				Html::addCssClass($options, 'icon');
				Html::addCssClass($options, 'icon-w-'.ceil($svgWidth / $svgHeight * 16));
				break;
			default:
				ArrayHelper::setValue($options, 'width', round($height * $svgWidth / $svgHeight));
		endswitch;

		if (ArrayHelper::remove($options, 'target', 'web') !== 'pdf')
			foreach ($doc->getElementsByTagName('path') as $path)
				$path->setAttribute('fill', 'currentColor');

		foreach ($options as $key => $value)
			$svg->setAttribute($key, $value);
		return $doc->saveXML($svg);
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
