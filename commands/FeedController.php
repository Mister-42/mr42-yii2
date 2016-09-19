<?php
namespace app\commands;
use Yii;
use app\models\General;
use app\models\Feed;
use app\models\user\RecentTracks;
use dektrium\user\models\Profile;
use dektrium\user\models\User;
use yii\console\Controller;
use yii\helpers\Url;
use yii\httpclient\Client;

/**
 * Handles feeds
 */
class FeedController extends Controller
{
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
		foreach($response->data['channel']['item'] as $item) {
			$rssItem = new Feed();
			$rssItem->feed = $name;
			$rssItem->title = (string) $item->title;
			$rssItem->url = (string) $item->$urlField;
			$rssItem->description = General::cleanInput($item->description, false);
			$rssItem->time = strtotime($item->pubDate);
			$rssItem->save();

			$count++;
			if ($count === $limit)
				break;
		}

		return Controller::EXIT_CODE_NORMAL;
	}

	/**
	 * Retrieves and stores Recent Tracks from Last.fm.
	*/
	public function actionLastfmRecent() {
		$client = new Client(['baseUrl' => 'https://ws.audioscrobbler.com/2.0/']);
		$limit = (isset(Yii::$app->params['recentTracksCount']) && is_int(Yii::$app->params['recentTracksCount'])) ? Yii::$app->params['recentTracksCount'] : 25;

		foreach (User::find()->where(['blocked_at' => null])->all() as $user) {
			$profile = Profile::find()->where(['user_id' => $user->id])->one();
			if (isset($profile->lastfm)) {
				$response = $client->createRequest()
					->addHeaders(['user-agent' => Yii::$app->name.' (+'.Url::to(['site/index'], true).')'])
					->setMethod('get')
					->setUrl('?method=user.getrecenttracks&user=' . $profile->lastfm . '&limit=' . $limit . '&api_key=' . Yii::$app->params['LastFMAPI'])
					->send();

				if (!$response->isOK)
					continue;

				$playcount = (int) $response->data['recenttracks']['@attributes']['total'];
				$count = 0;
				RecentTracks::deleteAll(['userid' => $user->id]);
				foreach($response->data['recenttracks']['track'] as $track) {
					$addTrack = new RecentTracks();
					$addTrack->userid = $user->id;
					$addTrack->artist = (string) $track->artist;
					$addTrack->track = (string) $track->name;
					$addTrack->count = $playcount--;
					$addTrack->time = ((bool) $track['nowplaying']) ? 0 : (int) $track->date['uts'];
					$addTrack->save();

					$count++;
					if ($count === $limit)
						break;
				}
				usleep(200000);
			}
		}
		return Controller::EXIT_CODE_NORMAL;
	}
}
