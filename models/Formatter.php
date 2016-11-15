<?php
namespace app\models;
use Yii;
use GK\JavascriptPacker;
use yii\helpers\{FileHelper, Markdown};

class Formatter extends \yii\i18n\Formatter {
	public function cleanInput($data, $markdown = 'original', $allowHtml = false) {
		$data = $allowHtml ? parent::asRaw($data) : parent::asHtml($data, ['HTML.Allowed' => '']);
		if ($markdown)
			$data = Markdown::process($data, $markdown);
		if ($allowHtml)
			$data = preg_replace('#@yt:([a-zA-Z0-9-]+):(16by9|4by3)#U', '<div class="embed-responsive embed-responsive-$2"><iframe class="embed-responsive-item" src="https://www.youtube.com/embed/$1?wmode=opaque" frameborder="0" allowfullscreen></iframe></div>', $data);
		return trim($data);
	}

	public function jspack($file, $replace = []) {
		$filename = Yii::getAlias('@app/assets/src/js/' . $file);
		$cachefile = Yii::getAlias('@runtime/assets/js/' . $file);

		if (!file_exists($filename))
			return $filename . ' does not exist.';

		if (!file_exists($cachefile) || filemtime($cachefile) < filemtime($filename)) {
			$js = empty($replace) ? file_get_contents($filename) : strtr(file_get_contents($filename), $replace);
			$jp = new JavascriptPacker($js, 0);
			if (!empty($replace))
				return $jp->pack();
			FileHelper::createDirectory(dirname($cachefile));
			file_put_contents($cachefile, $jp->pack());
			touch($cachefile, filemtime($filename));
		}
		return file_get_contents($cachefile);
	}
}
