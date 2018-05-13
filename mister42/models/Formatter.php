<?php
namespace app\models;
use Yii;
use app\models\Video;
use GK\JavascriptPacker;
use yii\helpers\{FileHelper, Markdown};

class Formatter extends \yii\i18n\Formatter {
	public function cleanInput(string $data, string $markdown = 'original', bool $allowHtml = false): string {
		$data = $allowHtml ? parent::asRaw($data) : parent::asHtml($data, ['HTML.Allowed' => '']);
		$data = preg_replace_callback_array([
			'/(vimeo):(()?[[:digit:]]+):(21by9|16by9|4by3|1by1)/U'				=> 'static::getVideo',
			'/(youtube):((PL)?[[:ascii:]]{11,32}):(21by9|16by9|4by3|1by1)/U'	=> 'static::getVideo',
		], $data);
		if ($markdown) :
			$data = Markdown::process($data, $markdown);
		endif;
		$data = self::addImageFluidClass($data);
		return trim($data);
	}

	public function jspack(string $file, array $replace = []): string {
		$filename = Yii::getAlias("@app/assets/js/{$file}");
		$cachefile = Yii::getAlias("@runtime/assets/js/{$file}");

		if (!file_exists($filename)) :
			return "{$file} does not exist.";
		endif;

		if (!file_exists($cachefile) || filemtime($cachefile) < filemtime($filename)) :
			FileHelper::createDirectory(Yii::getAlias('@runtime/assets/js'));
			$js = strtr(file_get_contents($filename), $replace);
			$jp = new JavascriptPacker($js, 0);
			if (!empty($replace)) :
				return $jp->pack();
			endif;
			FileHelper::createDirectory(dirname($cachefile));
			file_put_contents($cachefile, $jp->pack());
			touch($cachefile, filemtime($filename));
		endif;
		return file_get_contents($cachefile);
	}

	private static function addImageFluidClass($html) {
		$html = preg_match('/<img.*? class="/', $html)
			? $html = preg_replace('/(<img.*? class=" .*?)(".*?\="">)/', '$1 img-fluid $2', $html)
			: $html = preg_replace('/(<img.*?)(\>)/', '$1 class="img-fluid" $2', $html);
		$html = preg_replace('/(width|height)=\"\d*\"\s/', "", $html);
		return $html;
	}

	private static function getVideo(array $match): string {
		return Video::getEmbed($match[1], $match[2], $match[4], $match[3] === 'PL' ? true : $match[3]);
	}
}
