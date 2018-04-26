<?php
namespace app\commands;
use Yii;
use app\models\Webrequest;
use app\models\feed\Feed;
use app\models\user\{RecentTracks, WeeklyArtist};
use Da\User\Model\{Profile, User};
use yii\console\Controller;
use yii\helpers\ArrayHelper;

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
			$rssItem->title = (string) ArrayHelper::getValue($item, 'title');
			$rssItem->url = (string) ArrayHelper::getValue($item, $urlField);
			$rssItem->description = Yii::$app->formatter->cleanInput(ArrayHelper::getValue($item, 'description'), false);
			$rssItem->time = strtotime(ArrayHelper::getValue($item, 'pubDate'));
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
		$recentTracks = new RecentTracks;
		RecentTracks::deleteAll(['<=', 'seen', time() - 300]);
		foreach (User::find()->where(['blocked_at' => null])->all() as $user) :
			$profile = Profile::find()->where(['user_id' => $user->id])->one();
			if (isset($profile->lastfm)) {
				$lastSeen = $recentTracks->lastSeen($user->id);

				if (!$lastSeen)
					continue;

				$recentTracks->updateUser($lastSeen, $profile);
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

				WeeklyArtist::deleteAll(['userid' => $profile->user_id]);
				foreach($response->data['weeklyartistchart']['artist'] as $artist) :
					$addArtist = new WeeklyArtist();
					$addArtist->userid = $profile->user_id;
					$addArtist->rank = (int) ArrayHelper::getValue($artist, '@attributes.rank');
					$addArtist->artist = (string) ArrayHelper::getValue($artist, 'name');
					$addArtist->count = (int) ArrayHelper::getValue($artist, 'playcount');
					$addArtist->save();

					if ((int) ArrayHelper::getValue($artist, '@attributes.rank') === $limit)
						break;
				endforeach;
				usleep(200000);
			}
		endforeach;

		return self::EXIT_CODE_NORMAL;
	}
}
