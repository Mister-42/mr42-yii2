<?php
namespace app\models\articles;
use Yii;

class Query extends \yii\db\ActiveQuery {
	public function init(): ?self {
		parent::init();
		if (php_sapi_name() === 'cli' || (!Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin))
			return null;

		return $this->onCondition([$this->modelClass::tableName().'.active' => true]);
	}
}
