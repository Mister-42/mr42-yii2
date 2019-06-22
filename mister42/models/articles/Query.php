<?php

namespace app\models\articles;

use Yii;

class Query extends \yii\db\ActiveQuery {
	public function init(): self {
		parent::init();
		$alias = ($this->modelClass === Articles::class) ? 'article' : 'comment';
		$this->alias($alias);
		return (php_sapi_name() === 'cli' || (!Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin))
			? $this
			: $this->onCondition(["{$alias}.active" => true]);
	}
}
