<?php
namespace app\models;
use Yii;
use app\models\Video;
use GK\JavascriptPacker;
use yii\helpers\{FileHelper, Markdown};

class Formatter extends \yii\i18n\Formatter {
	public function cleanInput(string $data, string $markdown = 'original', bool $allowHtml = false): string {
		$data = $allowHtml ? Yii::$app->formatter->asRaw(trim($data)) : Yii::$app->formatter->asHtml(trim($data), ['HTML.Allowed' => '']);
		$data = preg_replace_callback_array([
			'/(vimeo):(()?[[:digit:]]+):(21by9|16by9|4by3|1by1)/U'				=> [$this, 'getVideo'],
			'/(youtube):((PL)?[[:ascii:]]{11,32}):(21by9|16by9|4by3|1by1)/U'	=> [$this, 'getVideo'],
		], $data);
		if ($markdown)
			$data = Markdown::process($data, $markdown);
		if (Yii::$app->request->isConsoleRequest || Yii::$app->controller->id !== 'feed')
			$data = $this->addImageResponsiveClass($data);
		return trim($data);
	}

	public function jspack(string $file): string {
		if (!file_exists($fileName = Yii::getAlias("@app/assets/js/{$file}")))
			return "{$file} does not exist.";

		$cacheFile = Yii::getAlias("@runtime/assets/js/{$file}");
		if (!file_exists($cacheFile) || filemtime($cacheFile) < filemtime($fileName)) :
			FileHelper::createDirectory(Yii::getAlias('@runtime/assets/js'));
			$jp = new JavascriptPacker(file_get_contents($fileName), 0);
			FileHelper::createDirectory(dirname($cacheFile));
			file_put_contents($cacheFile, $jp->pack());
			touch($cacheFile, filemtime($fileName));
		endif;
		return file_get_contents($cacheFile);
	}

	private function addImageResponsiveClass(string $html): string {
		$html = preg_match('/<img.*? class="/', $html)
			? preg_replace("/<img (.*?) class=\"(.*?)\"(.*?)>/i", '<img $1 class="$2 img-fluid"$3>', $html)
			: preg_replace('/(<img.*?)(\>)/', '$1 class="img-fluid"$2', $html);
		$html = preg_replace('/(width|height)=\"\d*\"\s/', "", $html);
		return $html;
	}

	private function getVideo(array $match): string {
		return Video::getEmbed($match[1], $match[2], $match[4], $match[3] === 'PL' ? true : $match[3]);
	}
}
