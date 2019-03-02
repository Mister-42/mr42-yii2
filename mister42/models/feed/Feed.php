<?php
namespace app\models\feed;

class Feed extends \yii\db\ActiveRecord {
	public static function tableName(): string {
		return '{{x_feed}}';
	}
}
