<?php
namespace app\models\webhook;
use app\commands\FeedController;

class Github extends \yii\base\Model {
	public function push(string $payload): array {
		$controller = new FeedController(Yii::$app->controller->id, Yii::$app);
		$controller->limit = 5;
		$controller->actionWebfeed('atom', 'Mr42Commits', "https://github.com/{$payload->repository->full_name}/commits/{$payload->repository->default_branch}.atom", 'content');

		return ['status' => 'success', 'message' => 'Successfully updated.'];
	}
}