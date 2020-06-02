<?php

namespace mister42\commands;

use mister42\models\Apirequest;
use mister42\models\Console;
use mister42\models\Image;
use mister42\models\music\Lyrics;
use mister42\models\music\Lyrics1Artists;
use mister42\models\music\Lyrics2Albums;
use mister42\models\music\Lyrics3Tracks;
use mister42\models\music\LyricsArtistInfo;
use mister42\models\Video;
use yii\console\ExitCode;
use yii\helpers\ArrayHelper;

/**
 * Handles all actions related to music.
 */
class LyricsController extends \yii\console\Controller
{
    const ALBUM_IMAGE_DIMENSIONS = 1000;

    public $defaultAction = 'index';

    /**
     * Resizes all album covers to the default dimensions if they exceed this limit.
     */
    public function actionAlbumImage(): int
    {
        $count = (int) Lyrics2Albums::find()->count();
        Console::startProgress($x = 0, $count, 'Processing Images: ');
        foreach (Lyrics1Artists::albumsList() as $artist) {
            foreach ($artist->albums as $album) {
                Console::updateProgress(++$x, $count);
                [$width, $height] = ($album->image) ? getimagesizefromstring($album->image) : [0, 0];
                if (empty(array_diff([$width, $height], [self::ALBUM_IMAGE_DIMENSIONS])) && $album->image_color !== null) {
                    continue;
                }
                Console::write($artist->name, [Console::FG_PURPLE], 3);
                Console::write($album->year, [Console::FG_GREEN]);
                Console::write($album->name, [Console::FG_GREEN], 8);

                if (!$album->image) {
                    Console::writeError('Missing', [Console::BOLD, Console::FG_RED, CONSOLE::BLINK]);
                    continue;
                }

                Console::write("{$width}x{$height}", [Console::FG_RED], 2);

                if (min($width, $height) > self::ALBUM_IMAGE_DIMENSIONS) {
                    $album->image = Image::resize($album->image, self::ALBUM_IMAGE_DIMENSIONS);
                    $album->image_color = null;
                    [$width, $height] = getimagesizefromstring($album->image);
                    Console::write("{$width}x{$height}", [Console::BOLD, Console::FG_GREEN]);
                }

                $album->image_color = $album->image_color ?? Image::getAverageImageColor($album->image);

                $album->save();
                Console::newLine();
            }
        }
        Console::endProgress(true);

        return ExitCode::OK;
    }

    /**
     * Builds all albums PDF files, unless already cached and up-to-date.
     */
    public function actionAlbumPdf(): int
    {
        $lyrics = new Lyrics($this);
        $artists = $lyrics->getArtists();
        $count = count($artists);

        Console::startProgress($x = 0, $count, 'Processing PDFs: ');
        foreach ($artists as $artist) {
            $albums = $lyrics->getAlbums($artist->url);
            $count += count($albums) - 1;
            foreach ($albums as $album) {
                Console::updateProgress(++$x, $count);
                $lyrics->getAlbumPdf($album->artist->url, $album->year, $album->url);
            }
        }
        Console::endProgress(true);

        return ExitCode::OK;
    }

    /**
     * Retrieves and stores artist information.
     */
    public function actionArtistInfo(): void
    {
        $query = LyricsArtistInfo::find()->where(['not', ['mbid' => null]]);
        foreach ($query->each() as $artist) {
            $response = Apirequest::getLastfm('artist.getInfo', ['mbid' => $artist->mbid]);

            if (!$response->isOK || ArrayHelper::getValue($response->data, '@attributes.status') !== 'ok') {
                Console::write($artist->parent, [Console::FG_GREEN], 5);
                Console::writeError('ERROR!', [Console::BOLD, Console::FG_RED, CONSOLE::BLINK]);
                continue;
            }

            $artistInfo = LyricsArtistInfo::findOne(['parent' => $artist->parent]);
            $artistInfo->mbid = ArrayHelper::getValue($response->data, 'artist.mbid');
            $artistInfo->bio_summary = trim(ArrayHelper::getValue($response->data, 'artist.bio.summary')) ?? null;
            $bioFull = ArrayHelper::getValue($response->data, 'artist.bio.content');
            $artistInfo->bio_full = (!is_array($bioFull)) ? trim($bioFull) : null;
            $artistInfo->save();
        }
    }

    /**
     * Perform image & PDF actions.
     */
    public function actionIndex(): void
    {
        $this->actionAlbumImage();
        $this->actionAlbumPdf();
    }

    /**
     * Checking status of album playlists and track videos.
     */
    public function actionVideos(): int
    {
        $video = new Video();

        $query = Lyrics1Artists::find()->orderBy(['name' => SORT_ASC])->with(['albums' => function ($q): void {
            $q->where(['not', ['playlist_source' => null, 'playlist_id' => null]]);
        }]);
        foreach ($query->each() as $artist) {
            foreach ($artist->albums as $album) {
                $media['playlists'][$album->playlist_source][] = ['id' => $album->playlist_id, 'artist' => $artist->name, 'year' => $album->year, 'name' => $album->name, 'status' => $album->playlist_status];
            }
        }

        $query = Lyrics3Tracks::find()->where(['not', ['video_source' => null, 'video_id' => null]]);
        foreach ($query->each() as $track) {
            $media['videos'][$track->video_source][] = ['id' => $track->video_id, 'name' => $track->name, 'status' => $track->video_status];
        }

        foreach ($media as $type => $data) {
            foreach ($data as $source => $payload) {
                foreach (array_chunk($payload, 50) as $media) {
                    switch ($source) :
                        case 'youtube':
                            $result[$type][] = $video->checkYoutube($media, $type);
                    break;
                    default:
                        Console::writeError("Checking {$source} {$type} is not supported.", [Console::BOLD, Console::FG_RED]);
                    return self::EXIT_CODE_ERROR;
                    endswitch;
                }
            }

            if ((bool) array_product($result[$type]) === true) {
                Console::write("Completed checking {$type}", [Console::BOLD, Console::FG_GREEN]);
                Console::newLine();
            }
        }

        if ((bool) array_product($result) === true) {
            return ExitCode::OK;
        }
        return ExitCode::UNSPECIFIED_ERROR;
    }
}
