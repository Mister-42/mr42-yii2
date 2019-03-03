<?php
namespace app\models\music;
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
		$this->active = boolval($this->active);
	}

	public static function artistsList(): array {
		return self::find()
			->orderBy(['name' => SORT_ASC])
			->all();
	}

	public static function albumsList(): BatchQueryResult {
		return self::find()
			->orderBy(['name' => SORT_ASC])
			->with('albums')
			->each();
	}

	public function getAlbums(): LyricsQuery {
		return $this->hasMany(Lyrics2Albums::class, ['parent' => 'id'])
			->orderBy(['year' => SORT_DESC, 'name' => SORT_ASC]);
	}

	public static function getLastModified(): int {
		$data = self::find()
			->max('updated');
		return strtotime($data);
	}

	public static function find(): LyricsQuery {
		return new LyricsQuery(get_called_class());
	}
}
