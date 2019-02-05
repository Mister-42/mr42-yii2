<?php
namespace app\models\lyrics;
use Yii;
use yii\db\BatchQueryResult;

class Lyrics1Artists extends \yii\db\ActiveRecord {
	public static function tableName(): string {
		return '{{%lyrics_1_artists}}';
	}

	public function afterFind(): void {
		parent::afterFind();
		$this->url = $this->url ?? $this->name;
		$this->updated = strtotime($this->updated);
		$this->active = (bool) $this->active;
	}

	public function artistsList(): array {
		return self::find()
			->active(self::tableName())
			->orderBy('name')
			->all();
	}

	public static function albumsList(): BatchQueryResult {
		return self::find()
			->orderBy('name')
			->with('albums')
			->each();
	}

	public static function lastModified(): int {
		$data = self::find()
			->active(self::tableName())
			->max('updated');
		return Yii::$app->formatter->asTimestamp($data);
	}

	public function getAlbums(): LyricsQuery {
		return $this->hasMany(Lyrics2Albums::className(), ['parent' => 'id'])
			->active(Lyrics2Albums::tableName());
	}

	public static function find(): LyricsQuery {
		return new LyricsQuery(get_called_class());
	}
}
