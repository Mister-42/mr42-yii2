<?php
namespace app\controllers;
use Yii;
use app\models\site\Changelog;
use yii\web\{Controller, Response, NotFoundHttpException, UnauthorizedHttpException};

class WebhookController extends Controller {
	public function actionIndex() {
		return $this->goHome();
	}

	public function actionChangelog() {
		Yii::$app->response->format = Response::FORMAT_JSON;
		list($algo, $hash) = explode('=', $_SERVER['HTTP_X_HUB_SIGNATURE'], 2);
		if (!hash_equals($hash, hash_hmac($algo, file_get_contents('php://input'), Yii::$app->params['GitHubHook'])))
			throw new UnauthorizedHttpException('Access denied!');

		if ($_SERVER['HTTP_X_GITHUB_EVENT'] === 'ping')
			return ['status' => 'success', 'message' => 'Pong!'];

		if ($_SERVER['HTTP_X_GITHUB_EVENT'] !== 'push')
			throw new NotFoundHttpException('Action not found.');

		$payload = json_decode(Yii::$app->request->post('payload'));
		foreach($payload->commits as $item) :
			if (empty(Changelog::find()->where(['time' => strtotime($item->timestamp)])->all())) {
				$rssItem = new Changelog();
				$rssItem->id = $item->id;
				$rssItem->description = $item->message;
				$rssItem->time = strtotime($item->timestamp);
				$rssItem->save();
			}
		endforeach;
		touch(Yii::getAlias('@app/views/site/changelog.php'));
		return ['status' => 'success', 'message' => 'Successfully updated.'];
	}

	public function beforeAction($action) {
		$this->enableCsrfValidation = false;
		return parent::beforeAction($action);
	}
}
