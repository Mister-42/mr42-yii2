<?php
namespace app\commands;
use Yii;
use app\models\{Feed, Formatter};
use app\models\user\RecentTracks;
use dektrium\user\models\{Profile, User};
use yii\console\Controller;
use yii\helpers\Url;
use yii\httpclient\Client;

/**
 * Handles feeds.
 */
class FeedController extends Controller {
	public $defaultAction = 'rss';

	/**
	 * Retrieves and stores an RSS feed.
	*/
	public function actionRss($name, $url, $urlField = 'link') {
		$client = new Client();
		$limit = (isset(Yii::$app->params['feedItemCount']) && is_int(Yii::$app->params['feedItemCount'])) ? Yii::$app->params['feedItemCount'] : 25;

		$response = $client->createRequest()
			->addHeaders(['user-agent' => Yii::$app->name.' (+'.Url::to(['site/index'], true).')'])
			->setMethod('get')
			->setUrl($url)
			->send();

		if (!$response->isOK)
			return Controller::EXIT_CODE_ERROR;

		$count = 0;
		Feed::deleteAll(['feed' => $name]);
		foreach($response->data['channel']['item'] as $item) :
			$rssItem = new Feed();
			$rssItem->feed = $name;
			$rssItem->title = (string) $item->title;
			$rssItem->url = (string) $item->$urlField;
			$rssItem->description = Formatter::cleanInput($item->description, false);
			$rssItem->time = strtotime($item->pubDate);
			$rssItem->save();

			$count++;
			if ($count === $limit)
				break;
		endforeach;

		return Controller::EXIT_CODE_NORMAL;
	}

	/**
	 * Retrieves and stores Recent Tracks from Last.fm.
	*/
	public function actionLastfmRecent() {
		RecentTracks::deleteAll(['<=', 'seen', time()-300]);
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

		return Controller::EXIT_CODE_NORMAL;
	}
}
