<?php
namespace app\controllers;
use Yii;
use app\models\Feed;
use yii\web\Controller;
use yii\web\Response;
use yii\web\NotFoundHttpException;
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

		$payload = json_decode(Yii::$app->request->post('payload'));
		foreach($payload->commits as $item) {
			if (empty(Feed::find()->where(['feed' => 'changelog', 'time' => strtotime($item->timestamp)])->all())) {
				$rssItem = new Feed();
				$rssItem->feed = 'changelog';
				$rssItem->title = $item->id;
				$rssItem->url = $item->url;
				$rssItem->description = $item->message;
				$rssItem->time = strtotime($item->timestamp);
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
