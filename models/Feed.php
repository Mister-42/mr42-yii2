<?php
namespace app\models;
use yii\db\ActiveRecord;

class Feed extends ActiveRecord
{
	public static function tableName()
	{
		return '{{%feed}}';
	}
}
