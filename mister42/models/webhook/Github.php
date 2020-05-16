<?php

namespace mister42\models\webhook;

use mister42\commands\FeedController;
use Yii;

class Github extends \yii\base\Model
{
    public function push(): array
    {
        $payload = json_decode(Yii::$app->request->post('payload'));
        $controller = new FeedController(Yii::$app->controller->id, Yii::$app);
        $controller->limit = 5;
        $payload->repository->name = 'Mr42Commits';
        $controller->actionWebfeed('github', $payload->repository);

        return ['status' => 'success', 'message' => 'Successfully updated.'];
    }
}
