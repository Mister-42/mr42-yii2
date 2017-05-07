<?php
namespace app\models;
use Yii;
use yii\httpclient\{Client, Response};
use yii\bootstrap\Html;

class Video {
	public function getVideo(string $source, string $id, string $ratio, bool $isPlaylist = false, bool $getEmbed = true): string {
		if (!$getEmbed) {
			if ($source === 'vimeo' && !$isPlaylist)
				return "https://vimeo.com/$id";
			elseif ($source === 'vimeo' && $isPlaylist)
				return "https://vimeo.com/album/$id";
			elseif ($source === 'youtube' && !$isPlaylist)
				return "https://youtu.be/$id";
			elseif ($source === 'youtube' && ($isPlaylist || $isPlaylist === 'PL'))
				return 'https://www.youtube.com/playlist?' . http_build_query(['list' => $id]);
		}

		if ($source === 'vimeo')
			$src = "https://player.vimeo.com/video/$id?" . http_build_query(['byline' => 0, 'portrait' => 0, 'title' => 0]);
		elseif ($source === 'youtube' && !$isPlaylist)
			$src = "https://www.youtube-nocookie.com/embed/$id?" . http_build_query(['disablekb' => 1, 'rel' => 0, 'showinfo' => 0]);
		elseif ($source === 'youtube' && ($isPlaylist || $isPlaylist === 'PL'))
			$src = 'https://www.youtube-nocookie.com/embed/videoseries?' . http_build_query(['disablekb' => 1, 'list' => $id, 'showinfo' => 0]);
		$video = Html::tag('iframe', null, ['allowfullscreen' => true, 'class' => 'embed-responsive-item', 'src' => $src]);
		return Html::tag('div', $video, ['class' => "embed-responsive embed-responsive-$ratio"]);
	}
}
