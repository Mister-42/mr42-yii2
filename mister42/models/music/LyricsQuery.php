<?php

namespace app\models\music;

use Yii;

class LyricsQuery extends \yii\db\ActiveQuery {
	public function init(): self {
		parent::init();
		$alias = ($this->modelClass === Lyrics1Artists::class) ? 'artist' : 'album';
		$this->alias($alias);
		return (php_sapi_name() === 'cli' || (!Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin))
			? $this
			: $this->onCondition(["{$alias}.active" => true]);
	}
}
