<?php
namespace app\commands;
use Yii;
use app\models\Webrequest;
use app\models\feed\Feed;
use app\models\user\{Profile, RecentTracks, WeeklyArtist};
use Da\User\Model\User;
use yii\console\Controller;
use yii\helpers\ArrayHelper;

/**
 * Handles feeds.
 */
class FeedController extends Controller {
	public $defaultAction = 'webfeed';

	/**
	 * Retrieves and stores Recent Tracks from Last.fm.
	 */
	public function actionLastfmRecent(): int {
		$recentTracks = new RecentTracks;
		RecentTracks::deleteAll(['<=', 'seen', time() - 300]);
		foreach (User::find()->where(['blocked_at' => null])->all() as $user) :
			$profile = Profile::findOne(['user_id' => $user->id]);
			if (isset($profile->lastfm)) :
				$lastSeen = $recentTracks->lastSeen($user->id);

				if (!$lastSeen) :
					continue;
				endif;

				$recentTracks->updateUser($profile, $lastSeen);
				usleep(200000);
			endif;
		endforeach;

		return self::EXIT_CODE_NORMAL;
	}

	/**
	 * Retrieves and stores Weekly Artist Chart from Last.fm.
	 */
	public function actionLastfmWeeklyArtist(): int {
		$limit = 15;
		foreach (User::find()->where(['blocked_at' => null])->all() as $user) :
			$profile = Profile::findOne(['user_id' => $user->id]);
			if (isset($profile->lastfm)) :
				$response = Webrequest::getLastfmApi('user.getweeklyartistchart', $profile->lastfm, $limit);
				if (!$response->isOK) :
					return self::EXIT_CODE_ERROR;
				endif;

				WeeklyArtist::deleteAll(['userid' => $profile->user_id]);
				foreach ($response->data['weeklyartistchart']['artist'] as $artist) :
					$addArtist = new WeeklyArtist();
					$addArtist->userid = $profile->user_id;
					$addArtist->rank = (int) ArrayHelper::getValue($artist, '@attributes.rank');
					$addArtist->artist = (string) ArrayHelper::getValue($artist, 'name');
					$addArtist->count = (int) ArrayHelper::getValue($artist, 'playcount');
					$addArtist->save();

					if ((int) ArrayHelper::getValue($artist, '@attributes.rank') === $limit) :
						break;
					endif;
				endforeach;
				usleep(200000);
			endif;
		endforeach;

		return self::EXIT_CODE_NORMAL;
	}

	/**
	 * Retrieves and stores an Atom or RSS feed.
	 */
	public function actionWebfeed(string $type, string $name, string $url): int {
		$limit = is_int(Yii::$app->params['feedItemCount']) ? Yii::$app->params['feedItemCount'] : 25;
		$response = Webrequest::getUrl('', $url);
		if (!$response->isOK) :
			return self::EXIT_CODE_ERROR;
		endif;

		Feed::deleteAll(['feed' => $name]);
		$data = $type === 'rss' ? $response->data['channel']['item'] : $response->data['entry'];
		foreach ($data as $item) :
			$feedItem = new Feed();
			$feedItem->feed = $name;
			$feedItem->title = (string) trim(ArrayHelper::getValue($item, 'title'));
			$feedItem->url = (string) ArrayHelper::getValue($item, $type === 'rss' ? 'link' : 'link.@attributes.href');
			$feedItem->description = Yii::$app->formatter->cleanInput(ArrayHelper::getValue($item, $type === 'rss' ? 'description' : 'content'), false);
			$feedItem->time = strtotime(ArrayHelper::getValue($item, $type === 'rss' ? 'pubDate' : 'updated'));
			$feedItem->save();

			if (++$count === $limit) :
				break;
			endif;
		endforeach;

		return self::EXIT_CODE_NORMAL;
	}
}
