<?php
namespace app\models\tools;
use Yii;
use yii\bootstrap4\Html;

class Oui extends \yii\db\ActiveRecord {
	public $oui;

	public static function tableName(): string {
		return '{{%oui}}';
	}

	public function rules(): array {
		return [
			[['oui'], 'required'],
		];
	}

	public function attributeLabels(): array {
		return [
			'oui' => Yii::t('mr42', 'OUI, MAC address, or name'),
		];
	}
}
