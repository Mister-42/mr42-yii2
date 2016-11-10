<?php
namespace app\models\lyrics;

class Lyrics4Lyrics extends \yii\db\ActiveRecord {
	public static function tableName() {
		return '{{%lyrics_4_lyrics}}';
	}

	public function afterFind() {
		parent::afterFind();
		$this->lyrics = Yii::$app->formatter->cleanInput($this->lyrics, 'gfm-comment');
	}
}
