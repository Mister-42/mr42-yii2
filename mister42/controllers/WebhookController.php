<?php
namespace app\controllers;
use Yii;
use app\commands\FeedController;
use yii\web\{Response, NotFoundHttpException, UnauthorizedHttpException};

class WebhookController extends \yii\web\Controller {
	public function actionGitPush() {
		if (!Yii::$app->request->post()) :
			throw new UnauthorizedHttpException('Access denied!');
		endif;

		Yii::$app->response->format = Response::FORMAT_JSON;
		list($algo, $hash) = explode('=', Yii::$app->request->headers->get('X-Hub-Signature'), 2);
		if (!hash_equals($hash, hash_hmac($algo, file_get_contents('php://input'), Yii::$app->params['secrets']['github']['hook']))) :
			throw new UnauthorizedHttpException('Access denied!');
		elseif (Yii::$app->request->headers->get('X-GitHub-Event') === 'ping') :
			return ['status' => 'success', 'message' => 'Pong!'];
		elseif (Yii::$app->request->headers->get('X-GitHub-Event') !== 'push') :
			throw new NotFoundHttpException('Action not found.');
		endif;

		$payload = json_decode(Yii::$app->request->post('payload'));
		$controller = new FeedController(Yii::$app->controller->id, Yii::$app);
		$controller->actionWebfeed('atom', 'Mr42Commits', "https://github.com/{$payload->repository->full_name}/commits/{$payload->repository->default_branch}.atom");

		return ['status' => 'success', 'message' => 'Successfully updated.'];
	}

	public function beforeAction($action) {
		$this->enableCsrfValidation = false;
		return parent::beforeAction($action);
	}
}
