<?php
namespace app\models\music;
use Yii;
use yii\helpers\ArrayHelper;

class LyricsQuery extends \yii\db\ActiveQuery {
	public function init(): ?self {
		parent::init();
		if (php_sapi_name() === 'cli' || (!Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin) || ArrayHelper::isIn($this->modelClass::tableName(), [Lyrics3Tracks::tableName(), Lyrics4Lyrics::tableName()]))
			return null;

		return $this->onCondition([$this->modelClass::tableName().'.active' => true]);
	}
}
