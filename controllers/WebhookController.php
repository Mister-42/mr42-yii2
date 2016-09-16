<?php
namespace app\controllers;
use Yii;
use app\models\Feed;
use yii\httpclient\Client;
use yii\web\Controller;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\UnauthorizedHttpException;

class WebhookController extends Controller
{
	public function actionChangelog()
	{
		list($algo, $hash) = explode('=', $_SERVER['HTTP_X_HUB_SIGNATURE'], 2);
		if (!hash_equals($hash, hash_hmac($algo, file_get_contents('php://input'), Yii::$app->params['GitHubHook'])))
			throw new UnauthorizedHttpException('Access denied!');

		Yii::$app->response->format = Response::FORMAT_JSON;
		if ($_SERVER['HTTP_X_GITHUB_EVENT'] === 'ping')
			return ['status' => 'success', 'message' => 'Pong!'];

		if ($_SERVER['HTTP_X_GITHUB_EVENT'] !== 'push')
			throw new NotFoundHttpException('Action not found.');

		$client = new Client();
		$payload = json_decode(Yii::$app->request->post('payload'));
		$response = $client->createRequest()
			->addHeaders(['user-agent' => Yii::$app->name])
			->setMethod('get')
			->setUrl(str_replace('{/sha}', '', $payload->repository->commits_url))
			->send();

		if (!$response->isOK)
			throw new ServerErrorHttpException('Unknown error');

		foreach($response->data as $item) {
			if (empty(Feed::find()->where(['feed' => 'changelog', 'time' => strtotime($item['commit']['committer']['date'])])->all())) {
				$rssItem = new Feed();
				$rssItem->feed = 'changelog';
				$rssItem->title = $item['sha'];
				$rssItem->url = $item['html_url'];
				$rssItem->description = $item['commit']['message'];
				$rssItem->time = strtotime($item['commit']['committer']['date']);
				$rssItem->save();
			}
		}

		return ['status' => 'success', 'message' => 'Successfully updated.'];
	}

	public function beforeAction($action)
	{
		$this->enableCsrfValidation = false;
		return parent::beforeAction($action);
	}
}
