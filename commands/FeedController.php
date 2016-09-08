<?php
namespace app\commands;
use SimpleXMLElement;
use Yii;
use app\models\General;
use app\models\Feed;
use app\models\user\RecentTracks;
use dektrium\user\models\Profile;
use dektrium\user\models\User;
use yii\console\Controller;

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
		$limit = (isset(Yii::$app->params['feedItemCount']) && is_int(Yii::$app->params['feedItemCount'])) ? Yii::$app->params['feedItemCount'] : 25;
		$file = @file_get_contents($url);

		if ($file === false)
			return Controller::EXIT_CODE_ERROR;

		$xml = new SimpleXMLElement($file);

		$count = 0;
		Feed::deleteAll(['feed' => $name]);
		foreach($xml->channel->item as $item) {
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
		$limit = (isset(Yii::$app->params['recentTracksCount']) && is_int(Yii::$app->params['recentTracksCount'])) ? Yii::$app->params['recentTracksCount'] : 25;
		$users = User::find()->where(['blocked_at' => null])->all();
		foreach ($users as $user) {
			$profile = Profile::find()->where(['user_id' => $user->id])->one();
			if (isset($profile->lastfm)) {
				$file = @file_get_contents('https://ws.audioscrobbler.com/2.0/?method=user.getrecenttracks&user=' . $profile->lastfm . '&limit=' . $limit . '&api_key=' . Yii::$app->params['LastFMAPI']);
				if ($file === false)
					continue;

				$xml = new SimpleXMLElement($file);
				$playcount = (int) $xml->recenttracks['total'];

				$count = 0;
				RecentTracks::deleteAll(['userid' => $user->id]);
				foreach($xml->recenttracks->track as $track) {
					$addTrack = new RecentTracks();
					$addTrack->userid = $user->id;
					$addTrack->artist = (string) $track->artist;
					$addTrack->track = (string) $track->name;
					$addTrack->count = $playcount--;
					$addTrack->time = ($track['nowplaying']) ? 0 : $track->date['uts'];
					$addTrack->save();

					$count++;
					if ($count === $limit)
						break;
				}
				sleep(1);
			}
		}
		return Controller::EXIT_CODE_NORMAL;
	}
}
