<?php
namespace app\models\user;
use Yii;
use app\models\Webrequest;
use app\widgets\{Item, RecentTracks as RecentTracksWidget};
use yii\helpers\ArrayHelper;

class RecentTracks extends \yii\db\ActiveRecord {
	public $limit = 20;

	public static function tableName(): string {
		 return '{{%lastfm_recenttracks}}';
	}

	public function display(int $userid): string {
		if (time() - $this->lastSeen($userid, true) > 300)
			$this->updateUser(Profile::findOne(['user_id' => $userid]), time());

		return Item::widget([
			'body' => RecentTracksWidget::widget(['tracks' => self::find()->where(['userid' => $userid])->orderBy(['count' => SORT_DESC])->limit($this->limit)->all()]),
			'header' => Yii::$app->icon->show('lastfm-square', ['class' => 'mr-1', 'style' => 'brands']).Yii::t('mr42', 'Recently Played Tracks'),
		]);
	}

	public function lastSeen(int $userid, bool $update = false): int {
		$lastSeen = self::find()
			->where(['userid' => $userid])
			->max('seen');

		if ($update)
			self::updateAll(['seen' => time()], ['userid' => $userid]);

		return $lastSeen ?? 0;
	}

	public function updateUser(Profile $profile, int $lastSeen) {
		if (isset($profile->lastfm)) :
			$response = Webrequest::getLastfmApi('user.getrecenttracks', ['limit' => $this->limit, 'user' => $profile->lastfm]);
			if (!$response->isOK)
				return false;

			$count = 0;
			$playcount = (int) $response->data['recenttracks']['@attributes']['total'];
			foreach ($response->data['recenttracks']['track'] as $track) :
				$time = (bool) ArrayHelper::getValue($track, '@attributes.nowplaying', false) ? 0 : (int) strtotime($track['date']);
				$addTrack = self::findOne(['userid' => $profile->user_id, 'time' => $time]) ?? new RecentTracks();
				$addTrack->userid = $profile->user_id;
				$addTrack->artist = (string) ArrayHelper::getValue($track, 'artist');
				$addTrack->track = (string) ArrayHelper::getValue($track, 'name');
				$addTrack->count = $playcount--;
				$addTrack->time = $time;
				$addTrack->seen = $lastSeen;
				$addTrack->save();

				if (++$count === $this->limit)
					break;
			endforeach;

			$this->cleanDb($profile->user_id);
		endif;
	}

	private function cleanDb(int $userid): void {
		$items = self::find()->where(['userid' => $userid])->orderBy(['count' => SORT_DESC])->limit(999)->offset($this->limit)->all();
		foreach ($items as $item)
			$item->delete();
	}
}
