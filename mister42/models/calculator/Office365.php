<?php
namespace app\models\calculator;
use DateTime;
use Yii;

class Office365 extends \yii\base\Model {
	public $sourcedate;
	public $sourcecount;
	public $targetdate;
	public $targetcount;
	public $action;

	public function rules(): array {
		return [
			[['sourcedate', 'sourcecount', 'targetcount', 'action'], 'required'],
			[['sourcedate', 'targetdate'], 'date', 'format' => 'php:Y-m-d'],
			[['sourcecount', 'targetcount'], 'double', 'min' => 1],
			['targetdate', 'default', 'value' => date('Y-m-d')],
		];
	}

	public function attributeLabels(): array {
		return [
			'sourcedate' => Yii::t('mr42', 'Current End Date'),
			'sourcecount' => Yii::t('mr42', 'Current Amount of Licenses'),
			'targetdate' => Yii::t('mr42', 'Date of Product Key Redemption'),
			'targetcount' => Yii::t('mr42', 'Amount of Licenses You Are Activating'),
			'action' => Yii::t('mr42', 'Action'),
		];
	}

	public function calculate(): bool {
		if (!$this->validate()) :
			return false;
		endif;

		$diff = (new DateTime($this->sourcedate))->diff(new DateTime($this->targetdate));

		$redeemDate = ($diff->invert === 0 && $diff->days <= 30) ? $this->sourcedate : $this->targetdate;
		if ($diff->invert === 0) :
			$diff = (new DateTime($this->targetdate))->diff(new DateTime($this->targetdate));
		endif;

		$upcomingYear = (new DateTime($redeemDate))->diff((new DateTime($redeemDate))->modify('1 year'));
		$targetCount = $this->action == 'renew' ? $this->targetcount : $this->sourcecount + $this->targetcount;
		$dateCalc = (($diff->days * $this->sourcecount) + ($upcomingYear->days * $this->targetcount)) / $targetCount;

		$newDate = new DateTime($redeemDate);
		$newDate->modify(ceil($dateCalc).' days');

		if ($newDate > (new DateTime($redeemDate))->modify('3 years')) :
			Yii::$app->getSession()->setFlash('office365-error', ['date' => $newDate, 'count' => $targetCount]);
			return false;
		endif;

		Yii::$app->getSession()->setFlash('office365-success', ['date' => $newDate, 'count' => $targetCount]);
		return true;
	}
}
