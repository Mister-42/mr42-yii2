<?php
namespace app\models;
use Yii;
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
}
