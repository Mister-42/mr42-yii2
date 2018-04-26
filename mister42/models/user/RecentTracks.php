<?php
namespace app\models\user;
use Yii;
use app\models\{Icon, Webrequest};
use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;

class RecentTracks extends \yii\db\ActiveRecord {
	public $limit = 20;

	public function init() {
		$this->limit = is_int(Yii::$app->params['recentTracksCount']) ? Yii::$app->params['recentTracksCount'] : $this->limit;
		parent::init();
	}

	public static function tableName() {
		 return '{{%lastfm_recenttracks}}';
	}

	public function display($userid) {
		if (time() - self::lastSeen($userid) > 300) {
			$profile = Profile::find()->where(['user_id' => $userid])->one();
			self::updateUser(time(), $profile);
		}
		self::updateAll(['seen' => time()], 'userid = '.$userid);

		$tracks = self::find()
			->where(['userid' => $userid])
			->orderBy('count DESC')
			->limit($this->limit)
			->all();

		foreach ($tracks as $track) :
			$data .= '<div class="clearfix">';
				$data .= Html::tag('span', $track['artist'], ['class' => 'float-left']);
				if ($track['time'] === 0)
					$data .= Icon::show('volume-up', ['title' => 'Currently playing']);
				$data .= Html::tag('span', $track['track'], ['class' => 'float-right text-right']);
			$data .= '</div>';
		endforeach;

		$data .= empty($tracks)
			? Html::tag('p', 'No items to display.')
			: Html::tag('div',
				Html::tag('span', Html::tag('b', 'Total tracks played:'), ['class' => 'float-left']) .
				Html::tag('span', Html::tag('b', Yii::$app->formatter->asInteger($tracks[0]['count'])), ['class' => 'float-right'])
			);
		return $data;
	}

	public function lastSeen($userid) {
		$lastSeen = self::find()
			->select(['seen' => 'max(seen)'])
			->where(['userid' => $userid])
			->one();
		return $lastSeen->seen;
	}

	public function updateUser($lastSeen, $profile) {
		if (isset($profile->lastfm)) {
			$response = Webrequest::getLastfmApi('user.getrecenttracks', $profile->lastfm, $this->limit);
			if (!$response->isOK)
				return false;

			$playcount = (int) $response->data['recenttracks']['@attributes']['total'];
			foreach($response->data['recenttracks']['track'] as $track) {
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
			}

			$delete = self::find()->where(['userid' => $profile->user_id])->orderBy('count DESC')->limit(999)->offset($this->limit)->all();
			foreach ($delete as $item)
				$item->delete();
		}
	}
}
