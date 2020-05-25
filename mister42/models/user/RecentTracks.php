<?php

namespace mister42\models\user;

use mister42\models\Apirequest;
use mister42\widgets\Item;
use mister42\widgets\RecentTracks as RecentTracksWidget;
use Yii;
use yii\helpers\ArrayHelper;

class RecentTracks extends \yii\db\ActiveRecord
{
    public $limit = 20;

    public function display(int $userid): string
    {
        if (time() - $this->lastSeen($userid, true) > 300) {
            $this->updateUser(Profile::findOne(['user_id' => $userid]), time());
        }

        return Item::widget([
            'body' => RecentTracksWidget::widget(['tracks' => self::find()->where(['userid' => $userid])->orderBy(['count' => SORT_DESC])->limit($this->limit)->all()]),
            'header' => Yii::$app->icon->name('lastfm-square', 'brands')->class('mr-1') . Yii::t('mr42', 'Recently Played Tracks'),
        ]);
    }

    public function lastSeen(int $userid, bool $update = false): int
    {
        $lastSeen = self::find()
            ->where(['userid' => $userid])
            ->max('seen');

        if ($update) {
            self::updateAll(['seen' => time()], ['userid' => $userid]);
        }

        return $lastSeen ?? 0;
    }

    public function rules(): array
    {
        return [
            [['userid', 'time'], 'unique', 'targetAttribute' => ['userid', 'time']],
        ];
    }

    public static function tableName(): string
    {
        return '{{%lastfm_recenttracks}}';
    }

    public function updateUser(Profile $profile, int $lastSeen)
    {
        if (isset($profile->lastfm)) {
            $response = Apirequest::getLastfm('user.getrecenttracks', ['limit' => $this->limit, 'user' => $profile->lastfm]);
            $playcount = (int) ArrayHelper::getValue($response->data, 'recenttracks.@attributes.total');

            $count = 0;
            foreach ($response->data['recenttracks']['track'] as $track) {
                $nowPlaying = (bool) ArrayHelper::getValue($track, '@attributes.nowplaying', false);
                $time = $nowPlaying ? 0 : ArrayHelper::getValue($track, 'date');

                $addTrack = self::findOne(['userid' => $profile->user_id, 'time' => $time]) ?? new self();
                $addTrack->userid = $profile->user_id;
                $addTrack->artist = (string) ArrayHelper::getValue($track, 'artist');
                $addTrack->track = (string) ArrayHelper::getValue($track, 'name');
                $addTrack->count = $playcount--;
                $addTrack->time = strtotime($time);
                $addTrack->seen = $lastSeen;
                $addTrack->save();

                if (++$count === $this->limit) {
                    break;
                }
            }

            $this->cleanDb($profile->user_id);
        }
    }

    private function cleanDb(int $userid): void
    {
        $items = self::find()->where(['userid' => $userid])->orderBy(['count' => SORT_DESC])->limit(999)->offset($this->limit)->all();
        foreach ($items as $item) {
            $item->delete();
        }
    }
}
