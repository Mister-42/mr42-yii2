<?php
namespace app\models\tools;
use yii\bootstrap\Html;

class Country extends \yii\db\ActiveRecord {
	public $iso;
	public $source;

	public static function tableName(): string {
		return 'x_country';
	}

	public function afterFind() {
		parent::afterFind();
		$this->source = Html::a('Frictionless Data', 'http://data.okfn.org/data/core/country-codes');
	}
}
