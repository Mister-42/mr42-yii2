<?php

namespace mister42\commands;

use mister42\models\feed\Feed;
use mister42\models\feed\FeedData;
use mister42\models\tools\Oui;
use mister42\models\user\Profile;
use mister42\models\user\RecentTracks;
use mister42\models\user\User;
use mister42\models\user\WeeklyArtist;
use mister42\models\Webrequest;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;

/**
 * Handles feeds.
 */
class FeedController extends Controller
{
    public $defaultAction = 'lastfm-recent';
    public int $limit = 25;

    /**
     * Retrieves and stores Recent Tracks from Last.fm.
     */
    public function actionLastfmRecent(): int
    {
        $recentTracks = new RecentTracks();
        RecentTracks::deleteAll(['<=', 'seen', time() - 300]);
        foreach (User::find()->where(['blocked_at' => null])->all() as $user) {
            $profile = Profile::findOne(['user_id' => $user->id]);
            if (isset($profile->lastfm)) {
                $lastSeen = $recentTracks->lastSeen($user->id);

                if (!$lastSeen) {
                    continue;
                }
                $recentTracks->updateUser($profile, $lastSeen);
                usleep(200000);
            }
        }

        return ExitCode::OK;
    }

    /**
     * Retrieves and stores Weekly Artist Chart from Last.fm.
     */
    public function actionLastfmWeeklyArtist(): int
    {
        foreach (User::find()->where(['blocked_at' => null])->all() as $user) {
            $profile = Profile::findOne(['user_id' => $user->id]);
            if (isset($profile->lastfm)) {
                $response = Webrequest::getLastfmApi('user.getweeklyartistchart', ['user' => $profile->lastfm, 'limit' => $this->limit]);
                if (!$response->isOK) {
                    continue;
                }
                WeeklyArtist::deleteAll(['userid' => $profile->user_id]);
                foreach ($response->data['weeklyartistchart']['artist'] as $artist) {
                    $addArtist = new WeeklyArtist();
                    $addArtist->userid = $profile->user_id;
                    $addArtist->rank = (int) ArrayHelper::getValue($artist, '@attributes.rank');
                    $addArtist->artist = (string) ArrayHelper::getValue($artist, 'name');
                    $addArtist->count = (int) ArrayHelper::getValue($artist, 'playcount');
                    $addArtist->save();

                    if ((int) ArrayHelper::getValue($artist, '@attributes.rank') === $this->limit) {
                        break;
                    }
                }
                usleep(200000);
            }
        }

        return ExitCode::OK;
    }

    /**
     * Retrieves and stores the latest IEEE MA-L Assignments.
     */
    public function actionOui(): int
    {
        $file = Yii::getAlias('@runtime/oui.csv');
        $response = Webrequest::saveUrl('http://standards-oui.ieee.org/oui/oui.csv', $file);
        if (!$response->isOK) {
            return ExitCode::IOERR;
        }
        $csv = array_map('str_getcsv', file($file));
        $csv = array_map(function ($x) {
            return [$x[1], trim($x[2])];
        }, $csv);
        array_shift($csv);

        Oui::deleteAll();
        Yii::$app->db->createCommand()->batchInsert(Oui::tableName(), ['assignment', 'name'], $csv)->execute();
        FileHelper::unlink($file);
        return ExitCode::OK;
    }

    /**
     * Retrieves and stores Atom or RSS feed.
     */
    public function actionWebfeed(string $type = null, object $data = null): int
    {
        $feeds = Feed::find()->where(['not', ['type' => null]])->andWhere(['not', ['type' => 'github']]);
        if ($type !== null) {
            $feeds = Feed::find()->where(['type' => $type])->andWhere(['name' => $data->name]);
        }

        foreach ($feeds->all() as $feed) {
            $count = 0;
            if ($type === 'github') {
                $feed->url = str_replace('{name}', $data->full_name, $feed->url);
                $feed->url = str_replace('{branch}', $data->default_branch, $feed->url);
            }

            $response = Webrequest::getUrl('', $feed->url)->send();
            if (!$response->isOK) {
                return ExitCode::IOERR;
            }

            $xml = simplexml_load_string($response->content);
            FeedData::deleteAll(['feed' => $feed->name]);
            foreach (($xml->getName() === 'rss') ? $response->data['channel']['item'] : $response->data['entry'] as $item) {
                $time = strtotime(ArrayHelper::getValue($item, 'pubDate') ?? ArrayHelper::getValue($item, 'updated'));

                $feedItem = new FeedData();
                $feedItem->feed = $feed->name;
                $feedItem->title = (string) trim(ArrayHelper::getValue($item, 'title'));
                $feedItem->url = (string) ArrayHelper::getValue($item, $xml->getName() === 'rss' ? 'link' : 'link.@attributes.href');
                $feedItem->description = Yii::$app->formatter->cleanInput(ArrayHelper::getValue($item, $feed->description), false);
                $feedItem->time = $time;
                $feedItem->save();

                if (++$count === $this->limit) {
                    break;
                }
            }
        }

        return ExitCode::OK;
    }
}
