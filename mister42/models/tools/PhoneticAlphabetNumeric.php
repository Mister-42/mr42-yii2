<?php
namespace app\models\tools;

class PhoneticAlphabetNumeric extends \yii\db\ActiveRecord {
	public static function tableName(): string {
		return 'x_phonetic_num';
	}
}
