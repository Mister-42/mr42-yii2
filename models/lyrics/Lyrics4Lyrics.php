<?php
namespace app\models\lyrics;
use app\models\Formatter;

class Lyrics4Lyrics extends \yii\db\ActiveRecord {
	public static function tableName() {
		return '{{%lyrics_4_lyrics}}';
	}

	public function afterFind() {
		parent::afterFind();
		$this->lyrics = Formatter::cleanInput($this->lyrics, 'gfm-comment');
	}
}
