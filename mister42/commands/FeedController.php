<?php
namespace app\commands;
use Yii;
use app\models\Webrequest;
use app\models\feed\Feed;
use app\models\tools\Oui;
use app\models\user\{Profile, RecentTracks, User, WeeklyArtist};
use yii\console\Controller;
use yii\helpers\{ArrayHelper, FileHelper};

/**
 * Handles feeds.
 */
class FeedController extends Controller {
	public $defaultAction = 'lastfm-recent';
	public $limit = 25;

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

				if (!$lastSeen)
					continue;

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
				if (!$response->isOK)
					continue;

				WeeklyArtist::deleteAll(['userid' => $profile->user_id]);
				foreach ($response->data['weeklyartistchart']['artist'] as $artist) :
					$addArtist = new WeeklyArtist();
					$addArtist->userid = $profile->user_id;
					$addArtist->rank = (int) ArrayHelper::getValue($artist, '@attributes.rank');
					$addArtist->artist = (string) ArrayHelper::getValue($artist, 'name');
					$addArtist->count = (int) ArrayHelper::getValue($artist, 'playcount');
					$addArtist->save();

					if ((int) ArrayHelper::getValue($artist, '@attributes.rank') === $this->limit)
						break;
				endforeach;
				usleep(200000);
			endif;
		endforeach;

		return self::EXIT_CODE_NORMAL;
	}

	/**
	 * Retrieves and stores the latest IEEE MA-L Assignments
	 */
	public function actionOui(): int {
		$file = Yii::getAlias('@runtime/oui.csv');
		Webrequest::saveUrl('http://standards-oui.ieee.org/oui/oui.csv', $file);

		Oui::deleteAll();
		Yii::$app->db->createCommand('LOAD DATA LOCAL INFILE "'.$file.'" IGNORE INTO TABLE '.Oui::tableName().' FIELDS TERMINATED BY "," ENCLOSED BY "\"" IGNORE 1 ROWS (@void, assignment, name) SET name=TRIM(name);')->execute();
		FileHelper::unlink($file);
		return self::EXIT_CODE_NORMAL;
	}

	/**
	 * Retrieves and stores an Atom or RSS feed.
	 */
	public function actionWebfeed(string $type, string $name, string $url, string $desc): int {
		$count = 0;
		$response = Webrequest::getUrl('', $url);
		if (!$response->isOK)
			return self::EXIT_CODE_ERROR;

		Feed::deleteAll(['feed' => $name]);
		$data = $type === 'rss' ? $response->data['channel']['item'] : $response->data['entry'];
		foreach ($data as $item) :
			$time = strtotime(ArrayHelper::getValue($item, 'pubDate') ?? ArrayHelper::getValue($item, 'updated'));

			$feedItem = Feed::findOne(['feed' => $name, 'time' => $time]) ?? new Feed();
			$feedItem->feed = $name;
			$feedItem->title = (string) trim(ArrayHelper::getValue($item, 'title'));
			$feedItem->url = (string) ArrayHelper::getValue($item, $type === 'rss' ? 'link' : 'link.@attributes.href');
			$feedItem->description = Yii::$app->formatter->cleanInput(ArrayHelper::getValue($item, $desc), false);
			$feedItem->time = $time;
			$feedItem->save();

			if (++$count === $this->limit)
				break;
		endforeach;

		return self::EXIT_CODE_NORMAL;
	}
}
