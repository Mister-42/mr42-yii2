<?php
namespace app\models\lyrics;
use yii\db\ActiveRecord;

class Lyrics4Lyrics extends ActiveRecord
{
	public static function tableName()
	{
		return '{{%lyrics_4_lyrics}}';
	}
}
