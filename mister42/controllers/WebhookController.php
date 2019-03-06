<?php
namespace app\controllers;
use Yii;
use app\models\webhook\Github;
use yii\web\{Response, NotFoundHttpException, UnauthorizedHttpException};

class WebhookController extends \yii\web\Controller {
	public $enableCsrfValidation = false;

	public function actionGithub(): array {
		Yii::$app->response->format = Response::FORMAT_JSON;
		[$algo, $hash] = explode('=', Yii::$app->request->headers->get('X-Hub-Signature', 'sha512=1'), 2);
		if (!hash_equals($hash, hash_hmac($algo, file_get_contents('php://input'), Yii::$app->params['secrets']['github']['hook'])))
			throw new UnauthorizedHttpException('Access denied!');

		$github = new Github();
		switch (Yii::$app->request->headers->get('X-GitHub-Event')) :
			case 'ping':
				return ['status' => 'success', 'message' => 'Pong!'];
			case 'push':
				return $github->push();
			default:
				throw new NotFoundHttpException('Action not found.');
		endswitch;
	}
}
