<?php

namespace app\models\webhook;

use app\commands\FeedController;
use Yii;

class Github extends \yii\base\Model {
	public function push(): array {
		$payload = json_decode(Yii::$app->request->post('payload'));
		$controller = new FeedController(Yii::$app->controller->id, Yii::$app);
		$controller->limit = 5;
		$controller->actionWebfeed('Mr42Commits', "https://github.com/{$payload->repository->full_name}/commits/{$payload->repository->default_branch}.atom", 'content');

		return ['status' => 'success', 'message' => 'Successfully updated.'];
	}
}
