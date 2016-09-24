<?php
namespace app\models;
use yii\db\ActiveRecord;

class Changelog extends ActiveRecord
{
	public static function tableName()
	{
		return '{{%changelog}}';
	}
}
