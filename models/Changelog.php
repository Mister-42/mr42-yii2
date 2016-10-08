<?php
namespace app\models;

class Changelog extends \yii\db\ActiveRecord {
	public static function tableName() {
		return '{{%changelog}}';
	}
}
