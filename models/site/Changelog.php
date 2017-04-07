<?php
namespace app\models\site;

class Changelog extends \yii\db\ActiveRecord {
	public static function tableName(): string {
		return '{{%changelog}}';
	}
}
