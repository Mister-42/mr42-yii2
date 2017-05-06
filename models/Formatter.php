<?php
namespace app\models;
use Yii;
use GK\JavascriptPacker;
use yii\bootstrap\Html;
use yii\helpers\{FileHelper, Markdown};

class Formatter extends \yii\i18n\Formatter {
	public function cleanInput(string $data, string $markdown = 'original', bool $allowHtml = false): string {
		$data = $allowHtml ? parent::asRaw($data) : parent::asHtml($data, ['HTML.Allowed' => '']);
		$data = preg_replace_callback_array([
			'/(vimeo):(()?[[:digit:]]+):(16by9|4by3)/U'				=> 'self::getVideoMatch',
			'/(youtube):((PL)?[[:ascii:]]{11,32}):(16by9|4by3)/U'	=> 'self::getVideoMatch',
		], $data);
		if ($markdown)
			$data = Markdown::process($data, $markdown);
		return trim($data);
	}

	public function getVideo(string $source, string $id, string $ratio, bool $playlist = false, bool $embed = true): string {
		if ($source === 'youtube' && $playlist)
			$playlist = 'PL';
		return self::getVideoMatch([null, $source, $id, $playlist, $ratio, $embed]);
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

	private function getVideoMatch(array $match): string {
		if (!$match[5]) {
			if ($match[1] === 'vimeo' && !$match[3])
				return "https://vimeo.com/$match[2]";
			elseif ($match[1] === 'vimeo' && $match[3])
				return "https://vimeo.com/album/$match[2]";
			elseif ($match[1] === 'youtube' && !$match[3])
				return "https://youtu.be/$match[2]";
			elseif ($match[1] === 'youtube' && $match[3] === 'PL')
				return 'https://www.youtube.com/playlist?' . http_build_query(['list' => $match[2]]);
		}

		if ($match[1] === 'vimeo')
			$src = "https://player.vimeo.com/video/$match[2]?" . http_build_query(['byline' => 0, 'portrait' => 0, 'title' => 0]);
		elseif ($match[1] === 'youtube' && !$match[3])
			$src = "https://www.youtube-nocookie.com/embed/$match[2]?" . http_build_query(['disablekb' => 1, 'rel' => 0, 'showinfo' => 0]);
		elseif ($match[1] === 'youtube' && $match[3] === 'PL')
			$src = 'https://www.youtube-nocookie.com/embed/videoseries?' . http_build_query(['disablekb' => 1, 'list' => $match[2], 'showinfo' => 0]);
		$video = Html::tag('iframe', null, ['allowfullscreen' => true, 'class' => 'embed-responsive-item', 'src' => $src]);
		return Html::tag('div', $video, ['class' => "embed-responsive embed-responsive-$match[4]"]);
	}
}
