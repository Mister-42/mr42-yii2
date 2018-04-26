<?php
namespace app\models\tools;
use yii\bootstrap4\Html;

class Country extends \yii\db\ActiveRecord {
	public $iso;
	public $source;

	public static function tableName(): string {
		return 'x_country';
	}

	public function afterFind() {
		parent::afterFind();
		$this->{'Geoname ID'} = Html::a($this->{'Geoname ID'}, 'http://geonames.org/' . $this->{'Geoname ID'});
		$this->is_independent = $this->is_independent === 'Yes' ? 'Independent' : $this->is_independent;
		$this->source = Html::a('Frictionless Data', 'http://data.okfn.org/data/core/country-codes');
	}
}
