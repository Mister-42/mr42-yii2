<?php
namespace app\models;
use Yii;
use yii\helpers\Url;
use yii\httpclient\{Client, Response};

class Webrequest {
	public static function getDiscogsApi(string $content): Response {
		return self::getUrl('https://api.discogs.com/', $content);
	}

	public static function getLastfmApi(string $method, string $user, int $limit): Response {
		return self::getUrl('https://ws.audioscrobbler.com/2.0/', '', [
			'api_key' => Yii::$app->params['secrets']['last.fm']['API'],
			'limit' => $limit,
			'method' => $method,
			'user' => $user,
		]);
	}

	public static function getYoutubeApi(string $id, string $content): Response {
		return self::getUrl('https://www.googleapis.com/youtube/v3', $content, [
			'id' => $id,
			'key' => Yii::$app->params['secrets']['google']['API'],
			'part' => $content === 'videos' ? 'snippet,status' : 'contentDetails,status',
		]);
	}

	public static function getUrl(string $base, string $url, array $data = []): Response {
		$client = new Client(['baseUrl' => $base]);
		return $client->createRequest()
			->addHeaders(['user-agent' => Yii::$app->name.' (+'.Yii::$app->params['shortDomain'].')'])
			->setData($data)
			->setUrl($url)
			->send();
	}

	public static function saveUrl(string $url, string $file): void {
		$ch = curl_init();
		$download = fopen($file, 'w');
		curl_setopt($ch, CURLOPT_POST, 0);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FILETIME, true);
		fwrite($download, curl_exec($ch));
		curl_close($ch);
		fclose($download);
	}
}
