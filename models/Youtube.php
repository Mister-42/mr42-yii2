<?php
namespace app\models;
use Yii;
use yii\httpclient\{Client, Response};

class Youtube {
	public function getApiRequest(string $id, string $content): Response {
		$youtube = new Client(['baseUrl' => 'https://www.googleapis.com/youtube/v3']);
		return $youtube->createRequest()
			->setUrl($content)
			->setData([
				'id' => $id,
				'key' => Yii::$app->params['GoogleAPI'],
				'part' => $content === 'videos' ? 'snippet,status' : 'contentDetails,status',
			])
			->send();
	}
}
