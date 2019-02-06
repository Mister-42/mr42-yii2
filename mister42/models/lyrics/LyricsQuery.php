<?php
namespace app\models\lyrics;
use Yii;

class LyricsQuery extends \yii\db\ActiveQuery {
	const STATUS_INACTIVE = '0';
	const STATUS_ACTIVE = '1';

	public function active(string $table): self {
		if (php_sapi_name() === 'cli' || (!Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin))
			return $this->onCondition(['or', [$table.'.active' => [Self::STATUS_INACTIVE, Self::STATUS_ACTIVE]]]);

		return $this->onCondition([$table.'.active' => Self::STATUS_ACTIVE]);
	}
}
