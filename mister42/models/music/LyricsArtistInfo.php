<?php
namespace app\models\music;
use Yii;
use yii\db\ActiveQuery;

class LyricsArtistInfo extends \yii\db\ActiveRecord {
	public $bioSummaryParsed;
	public $bioFullParsed;

	public static function tableName(): string {
		return '{{%lyrics_artistinfo}}';
	}

	public function afterFind(): void {
		parent::afterFind();
		$this->bioSummaryParsed = Yii::$app->formatter->cleanInput($this->bio_summary ?? '', 'gfm-comment', true);
		$this->bioFullParsed = Yii::$app->formatter->cleanInput($this->bio_full ?? '', 'gfm-comment', true);
	}

	public static function find(): ActiveQuery {
		return parent::find()->alias('info');
	}
}
