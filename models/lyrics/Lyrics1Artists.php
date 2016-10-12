<?php
namespace app\models\lyrics;
use Yii;

class Lyrics1Artists extends \yii\db\ActiveRecord {
	const STATUS_INACTIVE = '0';
	const STATUS_ACTIVE = '1';

	public static function tableName() {
		return '{{%lyrics_1_artists}}';
	}

	public function afterFind() {
		parent::afterFind();
		$this->url = $this->url ?? $this->name;
		$this->updated = strtotime($this->updated);
		$this->active = (bool) $this->active;
	}

	protected function baseList() {
		return self::find()
			->orderBy('name');
	}

	public function artistsList() {
		return self::baseList()
			->all();
	}

	public function albumsList() {
		return self::baseList()
			->joinWith('albums')
			->all();
	}

	public function lastUpdate($data, $max = null) {
		$data = $data ?? self::artistsList();
		foreach ($data as $item)
			$max = max($max, $item->updated);
		return $max;
	}

	public function getAlbums() {
		return $this->hasMany(Lyrics2Albums::className(), ['parent' => 'id'])
			->orderBy('year ASC, name');
	}

	public static function find() {
		return parent::find()
			->onCondition(
				php_sapi_name() !== 'cli' && !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin
					? ['or', [self::tableName().'.`active`' => [Self::STATUS_INACTIVE, Self::STATUS_ACTIVE]]]
					: [self::tableName().'.`active`' => Self::STATUS_ACTIVE]
			);
	}
}
