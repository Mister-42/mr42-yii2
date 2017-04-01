<?php
namespace app\models\tools;
use yii\bootstrap\Html;

class Country extends \yii\db\ActiveRecord {
	public $iso;
	public $source;

	public static function tableName() : string {
		return 'x_country';
	}

	public function afterFind() {
		parent::afterFind();
		$this->Continent = self::showContinent($this->Continent);
		$this->source = Html::a('Frictionless Data', 'http://data.okfn.org/data/core/country-codes');
	}

	private function showContinent($short) {
		switch ($short) {
			case 'AF'	: return 'Africa';			break;
			case 'AN'	: return 'Antarctica';		break;
			case 'AS'	: return 'Asia';			break;
			case 'EU'	: return 'Europe';			break;
			case 'NA'	: return 'North America';	break;
			case 'OC'	: return 'Oceania';			break;
			case 'SA'	: return 'South America';	break;
		};
	}
}
