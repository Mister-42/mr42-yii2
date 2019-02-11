<?php
namespace app\models\calculator;
use DateTime;
use Yii;

class Duration extends \yii\base\Model {
	public $fromDate;
	public $toDate;

	public function rules(): array {
		return [
			[['fromDate', 'toDate'], 'date', 'format' => 'php:Y-m-d'],
			['fromDate', 'default', 'value' => date('Y-m-d')],
			['toDate', 'default', 'value' => date('Y-m-d', strtotime('+42 days'))],
		];
	}

	public function attributeLabels(): array {
		return [
			'fromDate' => Yii::t('mr42', 'Start Date'),
			'toDate' => Yii::t('mr42', 'End Date'),
		];
	}

	public function calculate(): bool {
		if (!$this->validate())
			return false;

		$diff = (new DateTime($this->fromDate))->diff(new DateTime($this->toDate));
		Yii::$app->getSession()->setFlash('duration-success', $diff);
		return true;
	}
}
