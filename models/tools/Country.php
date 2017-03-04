<?php
namespace app\models\tools;

class Country extends \yii\db\ActiveRecord {
	public $iso;

	public static function tableName() {
		return 'x_country';
	}

	public function afterFind() {
		parent::afterFind();
		$this->Continent = self::showContinent($this->Continent);
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
