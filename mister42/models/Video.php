<?php
namespace app\models;
use Yii;
use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;

class Video {
	public static function getEmbed(string $source, string $id, string $ratio, bool $isPlaylist = false): string {
		if ($source === 'vimeo') :
			$src = "https://player.vimeo.com/video/{$id}?".http_build_query(['byline' => 0, 'portrait' => 0, 'title' => 0]);
		elseif ($source === 'youtube') :
			$src = $isPlaylist
				? 'https://www.youtube-nocookie.com/embed/videoseries?'.http_build_query(['disablekb' => 1, 'list' => $id, 'showinfo' => 0])
				: "https://www.youtube-nocookie.com/embed/{$id}?".http_build_query(['disablekb' => 1, 'rel' => 0, 'showinfo' => 0]);
		endif;

		if (!isset($src))
			return Yii::t('mr42', 'Sorry, {source} is not supported.', ['source' => $source]);

		return Html::tag('div',
			Html::tag('iframe', null, ['allowfullscreen' => true, 'class' => 'embed-responsive-item', 'src' => $src])
		, ['class' => "embed-responsive embed-responsive-{$ratio}"]);
	}

	public static function getUrl(string $source, string $id, bool $isPlaylist = false): string {
		if ($source === 'vimeo') :
			return $isPlaylist ? "https://vimeo.com/album/{$id}" : "https://vimeo.com/{$id}";
		elseif ($source === 'youtube') :
			return $isPlaylist ? 'https://www.youtube.com/playlist?'.http_build_query(['list' => $id]) : "https://youtu.be/{$id}";
		endif;

		return Yii::t('mr42', 'Sorry, {source} is not supported.', ['source' => $source]);
	}

	public function checkYoutube(array $data, string $type) {
		$result = true;
		$request = Webrequest::getYoutubeApi(implode(',', ArrayHelper::getColumn($data, 'id')), $type);
		if (!$request->isOK || $request->data['pageInfo']['totalResults'] === 0) :
			Console::writeError('Error: Could not get response from server', [Console::BOLD, Console::FG_RED, CONSOLE::BLINK]);
			return false;
		endif;
		$items = ArrayHelper::index($request->data['items'], 'id');

		foreach ($data as $listData) :
			$status = ArrayHelper::getValue($items, "{$listData['id']}.status");
			if (!ArrayHelper::keyExists($listData['id'], $items, false) || (ArrayHelper::getValue($status, 'privacyStatus') !== 'public' && !ArrayHelper::getValue($status, 'embeddable'))) :
				Console::write($listData['name'], [Console::FG_PURPLE], 5);
				Console::write(self::getUrl('youtube', $listData['id'], $type === 'playlists'), [Console::FG_PURPLE], 9);

				$result = false;
				if (!ArrayHelper::keyExists($listData['id'], $items, false))
					Console::writeError('Not Found', [Console::BOLD, Console::FG_RED, CONSOLE::BLINK]);
				elseif (ArrayHelper::getValue($status, 'privacyStatus') !== 'public')
					Console::writeError('Not Public', [Console::BOLD, Console::FG_RED, CONSOLE::BLINK]);
				elseif (!ArrayHelper::getValue($status, 'embeddable'))
					Console::writeError('Not embeddable', [Console::BOLD, Console::FG_RED, CONSOLE::BLINK]);

				continue;
			endif;
		endforeach;

		return $result;
	}
}
