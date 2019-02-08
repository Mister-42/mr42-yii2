<?php
namespace app\models\lyrics;
use Yii;

class LyricsQuery extends \yii\db\ActiveQuery {
	public function active(): self {
		if (php_sapi_name() === 'cli' || (!Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin))
			return $this->onCondition([]);

		return $this->onCondition([$this->modelClass::tableName().'.active' => true]);
	}
}
