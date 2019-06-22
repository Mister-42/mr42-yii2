<?php

namespace app\controllers;

use app\models\webhook\Github;
use mister42\Secrets;
use Yii;
use yii\web\{NotFoundHttpException, Response, UnauthorizedHttpException};

class WebhookController extends \yii\web\Controller {
	public $enableCsrfValidation = false;

	public function actionGithub(): array {
		Yii::$app->response->format = Response::FORMAT_JSON;
		[$algo, $hash] = explode('=', Yii::$app->request->headers->get('X-Hub-Signature', 'sha512=1'), 2);
		$secrets = (new Secrets())->getValues();
		if (!hash_equals($hash, hash_hmac($algo, file_get_contents('php://input'), $secrets['github']['hook']))) {
			throw new UnauthorizedHttpException('Access denied!');
		}
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
