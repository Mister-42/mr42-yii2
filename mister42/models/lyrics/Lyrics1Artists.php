<?php
namespace app\models\lyrics;
use Yii;
use yii\db\BatchQueryResult;

class Lyrics1Artists extends \yii\db\ActiveRecord {
	const STATUS_INACTIVE = '0';
	const STATUS_ACTIVE = '1';

	public static function tableName(): string {
		return '{{%lyrics_1_artists}}';
	}

	public function afterFind() {
		parent::afterFind();
		$this->url = $this->url ?? $this->name;
		$this->updated = strtotime($this->updated);
		$this->active = (bool) $this->active;
	}

	public static function artistsList(): array {
		return self::find()
			->orderBy('name')
			->all();
	}

	public static function albumsList(): BatchQueryResult {
		return self::find()
			->orderBy('name')
			->with('albums')
			->each();
	}

	public static function lastUpdate($data = null): int {
		$max = 0;
		$data = $data ?? self::artistsList();
		foreach ($data as $item) :
			$max = max($max, $item->updated);
		endforeach;
		return $max;
	}

	public function getAlbums() {
		return $this->hasMany(Lyrics2Albums::class, ['parent' => 'id'])
			->orderBy('year, name');
	}

	public static function find() {
		return parent::find()
			->onCondition(
				php_sapi_name() === 'cli' || (!Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin)
					? ['or', [self::tableName().'.`active`' => [Self::STATUS_INACTIVE, Self::STATUS_ACTIVE]]]
					: [self::tableName().'.`active`' => Self::STATUS_ACTIVE]
			);
	}
}
