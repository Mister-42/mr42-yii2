<?php
namespace app\controllers;
use Yii;
use app\commands\FeedController;
use yii\models\webhook\Github;
use yii\web\{Response, NotFoundHttpException, UnauthorizedHttpException};

class WebhookController extends \yii\web\Controller {
	public $enableCsrfValidation = false;

	public function actionGithub() {
		Yii::$app->response->format = Response::FORMAT_JSON;
		list($algo, $hash) = explode('=', Yii::$app->request->headers->get('X-Hub-Signature', 'sha512=1'), 2);
		if (!hash_equals($hash, hash_hmac($algo, file_get_contents('php://input'), Yii::$app->params['secrets']['github']['hook']))) :
			throw new UnauthorizedHttpException('Access denied!');
		endif;

		$github = new Github();
		switch (Yii::$app->request->headers->get('X-GitHub-Event')) :
			case 'ping':
				return ['status' => 'success', 'message' => 'Pong!'];
			case 'push':
				return $github->push(Yii::$app->request->post('payload'));
			default:
				throw new NotFoundHttpException('Action not found.');
		endswitch;

		$payload = json_decode(Yii::$app->request->post('payload'));
		$controller = new FeedController(Yii::$app->controller->id, Yii::$app);
		$controller->limit = 5;
		$controller->actionWebfeed('atom', 'Mr42Commits', "https://github.com/{$payload->repository->full_name}/commits/{$payload->repository->default_branch}.atom", 'content');

		return ['status' => 'success', 'message' => 'Successfully updated.'];
	}
}
