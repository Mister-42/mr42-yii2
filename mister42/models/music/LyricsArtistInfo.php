<?php
namespace app\models\music;
use Yii;
use yii\db\ActiveQuery;

class LyricsArtistInfo extends \yii\db\ActiveRecord {
	public $summaryParsed;

	public static function tableName(): string {
		return '{{%lyrics_artistinfo}}';
	}

	public function afterFind(): void {
		parent::afterFind();
		$this->summaryParsed = Yii::$app->formatter->cleanInput($this->summary ?? '', 'gfm-comment', true);
	}

	public static function find(): ActiveQuery {
		return parent::find()->alias('info');
	}
}
