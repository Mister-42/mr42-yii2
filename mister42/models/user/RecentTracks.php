<?php
namespace app\models\user;
use Yii;
use app\models\Webrequest;
use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;

class RecentTracks extends \yii\db\ActiveRecord {
	public $limit = 20;

	public static function tableName(): string {
		 return '{{%lastfm_recenttracks}}';
	}

	public function display(int $userid): string {
		if (time() - $this->lastSeen($userid, true) > 300) :
			$this->updateUser(Profile::findOne(['user_id' => $userid]), time());
		endif;

		$tracks = self::find()->where(['userid' => $userid])->orderBy('count DESC')->limit($this->limit)->all();

		foreach ($tracks as $track) :
			$data[] = Html::beginTag('div', ['class' => 'clearfix']);
				$data[] = Html::beginTag('div', ['class' => 'd-flex justify-content-between']);
					$data[] = Html::beginTag('span', ['class' => 'text-truncate']);
						$data[] = $track['artist'];
						if ($track['time'] === 0) :
							$data[] = Yii::$app->icon->show('volume-up', ['class' => 'ml-1', 'title' => 'Currently playing']);
						endif;
					$data[] = Html::endTag('span');
					$data[] = Html::tag('span', $track['track'], ['class' => 'text-truncate text-right']);
				$data[] = Html::endTag('div');
			$data[] = Html::endTag('div');
		endforeach;

		$data[] = empty($tracks)
			? Html::tag('div', 'No items to display.', ['class' => 'ml-2'])
			: Html::tag('div',
				Html::tag('span', 'Total tracks played:', ['class' => 'font-weight-bold float-left']).
				Html::tag('span', Yii::$app->formatter->asInteger($tracks[0]['count']), ['class' => 'font-weight-bold float-right'])
			);
		return implode($data);
	}

	public function lastSeen(int $userid, bool $update = false): int {
		$lastSeen = self::find()
			->select(['seen' => 'max(seen)'])
			->where(['userid' => $userid])
			->one();

		if ($update) :
			self::updateAll(['seen' => time()], 'userid = '.$userid);
		endif;

		return $lastSeen->seen ?? 0;
	}

	public function updateUser(Profile $profile, int $lastSeen) {
		if (isset($profile->lastfm)) :
			$response = Webrequest::getLastfmApi('user.getrecenttracks', $profile->lastfm, $this->limit);
			if (!$response->isOK) :
				return false;
			endif;

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

				if (++$count === $this->limit) :
					break;
				endif;
			endforeach;

			$this->cleanDb($profile->user_id);
		endif;
	}

	private function cleanDb(int $userid) {
		$items = self::find()->where(['userid' => $userid])->orderBy('count DESC')->limit(999)->offset($this->limit)->all();
		foreach ($items as $item) :
			$item->delete();
		endforeach;
	}
}
