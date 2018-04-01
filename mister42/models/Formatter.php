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
			'/(vimeo):(()?[[:digit:]]+):(16by9|4by3)/U'				=> 'self::getVideo',
			'/(youtube):((PL)?[[:ascii:]]{11,32}):(16by9|4by3)/U'	=> 'self::getVideo',
		], $data);
		if ($markdown)
			$data = Markdown::process($data, $markdown);
		$data = self::addImageResponsiveClass($data);
		return trim($data);
	}

	public function jspack(string $file, array $replace = []): string {
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

	private function addImageResponsiveClass($html) {
		$html = preg_match('/<img.*? class="/', $html)
			? $html = preg_replace('/(<img.*? class=" .*?)(".*?\="">)/', '$1 img-responsive $2', $html)
			: $html = preg_replace('/(<img.*?)(\>)/', '$1 class="img-responsive" $2', $html);
		$html = preg_replace( '/(width|height)=\"\d*\"\s/', "", $html);
		return $html;
	}

	private function getVideo(array $match): string {
		return Video::getEmbed($match[1], $match[2], $match[4], $match[3] === 'PL' ? true : $match[3]);
	}
}
