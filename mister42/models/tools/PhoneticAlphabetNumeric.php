<?php
namespace app\models\tools;
use Yii;

class PhoneticAlphabetNumeric extends \yii\db\ActiveRecord {
	public static function tableName(): string {
		return 'x_phonetic_num';
	}
}
