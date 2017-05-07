<?php
namespace app\commands;
use Yii;
use app\models\Webrequest;
use app\models\feed\Feed;
use app\models\user\{RecentTracks, WeeklyArtist};
use dektrium\user\models\{Profile, User};
use yii\console\Controller;

/**
 * Handles feeds.
 */
class FeedController extends Controller {
	public $defaultAction = 'rss';

	/**
	 * Retrieves and stores an RSS feed.
	*/
	public function actionRss($name, $url, $urlField = 'link') {
		$limit = is_int(Yii::$app->params['feedItemCount']) ? Yii::$app->params['feedItemCount'] : 25;
		$response = Webrequest::getUrl('', $url);
		if (!$response->isOK)
			return self::EXIT_CODE_ERROR;

		$count = 1;
		Feed::deleteAll(['feed' => $name]);
		foreach($response->data['channel']['item'] as $item) :
			$rssItem = new Feed();
			$rssItem->feed = $name;
			$rssItem->title = (string) $item->title;
			$rssItem->url = (string) $item->$urlField;
			$rssItem->description = Yii::$app->formatter->cleanInput($item->description, false);
			$rssItem->time = strtotime($item->pubDate);
			$rssItem->save();

			if ($count++ === $limit)
				break;
		endforeach;

		return self::EXIT_CODE_NORMAL;
	}

	/**
	 * Retrieves and stores Recent Tracks from Last.fm.
	*/
	public function actionLastfmRecent() {
		RecentTracks::deleteAll(['<=', 'seen', time() - 300]);
		foreach (User::find()->where(['blocked_at' => null])->all() as $user) :
			$profile = Profile::find()->where(['user_id' => $user->id])->one();
			if (isset($profile->lastfm)) {
				$lastSeen = RecentTracks::lastSeen($user->id);

				if (!$lastSeen)
					continue;

				RecentTracks::updateUser($lastSeen, $profile);
				usleep(200000);
			}
		endforeach;

		return self::EXIT_CODE_NORMAL;
	}

	/**
	 * Retrieves and stores Weekly Artist Chart from Last.fm.
	*/
	public function actionLastfmWeeklyArtist() {
		$limit = 15;
		foreach (User::find()->where(['blocked_at' => null])->all() as $user) :
			$profile = Profile::find()->where(['user_id' => $user->id])->one();
			if (isset($profile->lastfm)) {
				$response = Webrequest::getLastfmApi('user.getweeklyartistchart', $profile->lastfm, $limit);
				if (!$response->isOK)
					return false;

				$count = 1;
				foreach($response->data['weeklyartistchart']['artist'] as $artist) :
					$addTrack = WeeklyArtist::findOne(['userid' => $profile->user_id, 'artist' => $artist->name]) ?? new WeeklyArtist();
					$addTrack->userid = $profile->user_id;
					$addTrack->artist = (string) $artist->name;
					$addTrack->count = $artist->playcount;
					$addTrack->save();

					if ($count++ === $limit)
						break;
				endforeach;

				$delete = WeeklyArtist::find()->where(['userid' => $profile->user_id])->orderBy('count DESC')->limit(999)->offset($limit)->all();
				foreach ($delete as $item)
					$item->delete();
				usleep(200000);
			}
		endforeach;

		return self::EXIT_CODE_NORMAL;
	}
}
