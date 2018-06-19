<?php
namespace app\commands;
use Yii;
use app\models\Webrequest;
use app\models\music\Collection;
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
	public $limit = 25;

	/**
	 * Retrieves and stores Discogs collection
	 */
	public function actionDiscogsCollection(): int {
		$discogs = new Collection();
		foreach (User::find()->where(['blocked_at' => null])->all() as $user) :
			$profile = Profile::findOne(['user_id' => $user->id]);
			if (isset($profile->discogs)) :
				$response = Webrequest::getDiscogsApi("/users/{$profile->discogs}/collection/folders/0/releases");
				if (!$response->isOK) :
					continue;
				endif;
				Collection::deleteAll(['user_id' => $profile->user_id]);
				$discogs->saveCollection($profile->user_id, $response->data['releases']);

				for ($x = 2; $x < (int) ArrayHelper::getValue($response->data, 'pagination.pages'); $x++) :
					$response = Webrequest::getDiscogsApi("/users/{$profile->discogs}/collection/folders/0/releases?".http_build_query(['page' => $x]));
					if (!$response->isOK) :
						continue;
					endif;
					$discogs->saveCollection($profile->user_id, $response->data['releases']);
				endfor;
			endif;
		endforeach;

		return self::EXIT_CODE_NORMAL;
	}

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
		foreach (User::find()->where(['blocked_at' => null])->all() as $user) :
			$profile = Profile::findOne(['user_id' => $user->id]);
			if (isset($profile->lastfm)) :
				$response = Webrequest::getLastfmApi('user.getweeklyartistchart', $profile->lastfm, $this->limit);
				if (!$response->isOK) :
					continue;
				endif;

				WeeklyArtist::deleteAll(['userid' => $profile->user_id]);
				foreach ($response->data['weeklyartistchart']['artist'] as $artist) :
					$addArtist = new WeeklyArtist();
					$addArtist->userid = $profile->user_id;
					$addArtist->rank = (int) ArrayHelper::getValue($artist, '@attributes.rank');
					$addArtist->artist = (string) ArrayHelper::getValue($artist, 'name');
					$addArtist->count = (int) ArrayHelper::getValue($artist, 'playcount');
					$addArtist->save();

					if ((int) ArrayHelper::getValue($artist, '@attributes.rank') === $this->limit) :
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
	public function actionWebfeed(string $type, string $name, string $url, string $desc): int {
		$count = 0;
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
			$feedItem->description = Yii::$app->formatter->cleanInput(ArrayHelper::getValue($item, $desc), false);
			$feedItem->time = strtotime(ArrayHelper::getValue($item, 'pubDate') ?? ArrayHelper::getValue($item, 'updated'));
			$feedItem->save();

			if (++$count === $this->limit) :
				break;
			endif;
		endforeach;

		return self::EXIT_CODE_NORMAL;
	}
}
