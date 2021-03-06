<?php

namespace mister42\models;

use mister42\models\music\Lyrics2Albums;
use mister42\models\music\Lyrics3Tracks;
use Yii;
use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;

class Video
{
    public function checkYoutube(array $data, string $type): bool
    {
        $request = Apirequest::getYoutube(implode(',', ArrayHelper::getColumn($data, 'id')), $type);
        if (!$request->isOK || $request->data['pageInfo']['totalResults'] === 0) {
            Console::writeError('Error: Could not get response from server.', [Console::BOLD, Console::FG_RED, CONSOLE::BLINK]);
            return false;
        }
        $items = ArrayHelper::index($request->data['items'], 'id');

        foreach ($data as $listData) {
            $mediaStatus = 1;
            $status = ArrayHelper::getValue($items, "{$listData['id']}.status", false);
            if ($status === false || (ArrayHelper::getValue($status, 'privacyStatus') !== 'public' && !ArrayHelper::getValue($status, 'embeddable'))) {
                $mediaStatus = 0;
            }

            if ($listData['status'] !== (bool) $mediaStatus) {
                Console::writeError($listData['name'], [Console::FG_PURPLE]);
                Console::writeError(self::getUrl('youtube', $listData['id'], $type === 'playlists'), [Console::FG_PURPLE]);

                if ($status === false) {
                    Console::writeError('Not Found', [Console::BOLD, Console::FG_RED, CONSOLE::BLINK]);
                } elseif ($mediaStatus === 1) {
                    Console::writeError('Enabled', [Console::BOLD, Console::FG_GREEN, CONSOLE::BLINK]);
                } elseif (ArrayHelper::getValue($status, 'privacyStatus') !== 'public') {
                    Console::writeError('Not Public', [Console::BOLD, Console::FG_RED, CONSOLE::BLINK]);
                } elseif (!ArrayHelper::getValue($status, 'embeddable')) {
                    Console::writeError('Not Embeddable', [Console::BOLD, Console::FG_RED, CONSOLE::BLINK]);
                }
            }

            ($type === 'playlists')
                ? Lyrics2Albums::updateAll(['playlist_status' => $mediaStatus], ['playlist_id' => $listData['id']])
                : Lyrics3Tracks::updateAll(['video_status' => $mediaStatus], ['video_id' => $listData['id']]);
        }

        return true;
    }

    public static function getEmbed(string $source, string $id, string $ratio, bool $isPlaylist = false): string
    {
        if ($source === 'vimeo') {
            $src = "https://player.vimeo.com/video/{$id}?" . http_build_query(['byline' => 0, 'portrait' => 0, 'title' => 0]);
        } elseif ($source === 'youtube') {
            $src = $isPlaylist
                ? 'https://www.youtube-nocookie.com/embed/videoseries?' . http_build_query(['disablekb' => 1, 'list' => $id, 'showinfo' => 0])
                : "https://www.youtube-nocookie.com/embed/{$id}?" . http_build_query(['disablekb' => 1, 'rel' => 0, 'showinfo' => 0]);
        }

        if (!isset($src)) {
            return Yii::t('mr42', 'Sorry, {source} is not supported.', ['source' => $source]);
        }

        return Html::tag(
            'div',
            Html::tag('iframe', null, ['allowfullscreen' => true, 'class' => 'embed-responsive-item', 'src' => $src]),
            ['class' => "embed-responsive embed-responsive-{$ratio}"]
        );
    }

    public static function getUrl(string $source, string $id, bool $isPlaylist = false): string
    {
        if ($source === 'vimeo') {
            return $isPlaylist ? "https://vimeo.com/album/{$id}" : "https://vimeo.com/{$id}";
        } elseif ($source === 'youtube') {
            return $isPlaylist ? 'https://www.youtube.com/playlist?' . http_build_query(['list' => $id]) : "https://youtu.be/{$id}";
        }

        return Yii::t('mr42', 'Sorry, {source} is not supported.', ['source' => $source]);
    }
}
