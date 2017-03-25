<?php
namespace app\models\tools;
use yii\bootstrap\Html;

class Country extends \yii\db\ActiveRecord {
	public $iso;
	public $source;

	public static function tableName() {
		return 'x_country';
	}

	public function afterFind() {
		parent::afterFind();
		$this->Continent = self::showContinent($this->Continent);
		$this->source = Html::a('Frictionless Data', 'http://data.okfn.org/data/core/country-codes');
	}

	private function showContinent($short) {
		switch ($short) {
			case 'AF' : return 'Africa';
			case 'AN' : return 'Antarctica';
			case 'AS' : return 'Asia';
			case 'EU' : return 'Europe';
			case 'NA' : return 'North America';
			case 'OC' : return 'Oceania';
			case 'SA' : return 'South America';
		};
	}
}
