<?php
namespace app\models;
use Yii;
use JShrink\Minifier;
use GK\JavascriptPacker;
use yii\helpers\Markdown;

class Formatter extends \yii\i18n\Formatter {
	public function cleanInput($data, $markdown = 'original', $allowHtml = false) {
		$data = ($allowHtml) ? Yii::$app->formatter->asRaw($data) : Yii::$app->formatter->asHtml($data, ['HTML.Allowed' => '']);
		if ($markdown)
			$data = Markdown::process($data, $markdown);
		if ($allowHtml)
			$data = preg_replace('#@yt:([a-zA-Z0-9-]+):(16by9|4by3)#U', '<div class="embed-responsive embed-responsive-$2"><iframe class="embed-responsive-item" src="https://www.youtube.com/embed/$1?wmode=opaque" frameborder="0" allowfullscreen></iframe></div>', $data);
		return trim($data);
	}

	public function minify($file, $minify = false) {
		$file = Yii::getAlias('@app/assets/src/js/' . $file);
		if (!$js = file_get_contents($file))
			return $file . ' does not exist.';

		if ($minify)
			return Minifier::minify($js, array('flaggedComments' => false));

		$jp = new JavascriptPacker($js, 0);
		return $jp->pack();
	}
}
