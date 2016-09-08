<?php
namespace app\models\lyrics;
use Yii;
use yii\db\ActiveRecord;

class Lyrics2Albums extends ActiveRecord
{
	public $artistName;
	public $artistUrl;
	public $albumName;
	public $albumUrl;
	public $albumYear;

	public static function tableName()
	{
		return '{{%lyrics_2_albums}}';
	}

	public static function lastUpdate($artist) {
		$lastUpdate = self::find()
			->select(['updated' => 'max(UNIX_TIMESTAMP('.Lyrics2Albums::tableName().'.updated))'])
			->where(['COALESCE('.Lyrics1Artists::tableName().'.`url`,' . Lyrics1Artists::tableName().'.`name`)' => $artist])
			->joinWith('artist')
			->one();
		return $lastUpdate->updated;
	}

	public static function albumsList($artist = null) {
		$albumSort = ($artist === null) ? 'artistName ASC, albumYear ASC, albumName' : 'albumYear DESC, albumName';
		$artistSelect = ($artist === null) ? null : ['COALESCE('.Lyrics1Artists::tableName().'.`url`,' . Lyrics1Artists::tableName().'.`name`)' => $artist];
		return self::find()
			->select([
				'artistName' => Lyrics1Artists::tableName().'.`name`',
				'artistUrl' => 'COALESCE('.Lyrics1Artists::tableName().'.`url`,' . Lyrics1Artists::tableName().'.`name`)',
				'albumName' => self::tableName().'.`name`',
				'albumUrl' => 'COALESCE('.self::tableName().'.`url`, ' . self::tableName().'.`name`)',
				'albumYear' => self::tableName().'.`year`',
#				'updated' => 'UNIX_TIMESTAMP('.self::tableName().'.`updated`)',
				'active' => self::tableName().'.`active`',
			])
			->orderBy($albumSort)
			->where($artistSelect)
			->joinWith('artist')
			->with('tracks')
#			->join('JOIN', Lyrics4Lyrics::tableName(), Lyrics4Lyrics::tableName().'.`id` = ' . Lyrics3Tracks::tableName().'.`lyricid`')
			->all();
	}

	public function getArtist()
	{
		return $this->hasOne(Lyrics1Artists::className(), ['id' => 'parent']);
	}

	public function getTracks()
	{
		return $this->hasMany(Lyrics3Tracks::className(), ['parent' => 'id'])
#			->join('JOIN', Lyrics4Lyrics::tableName(), Lyrics4Lyrics::tableName().'.`id` = ' . Lyrics3Tracks::tableName().'.`lyricid`')
		;
	}

	public static function find()
	{
		if (php_sapi_name() == 'cli') {
			return parent::find();
		}

		return parent::find()
			->onCondition(!Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin ? ['or', [self::tableName().'.`active`' => [Lyrics1Artists::STATUS_INACTIVE, Lyrics1Artists::STATUS_ACTIVE]]] : [self::tableName().'.`active`' => Lyrics1Artists::STATUS_ACTIVE])
		;
	}
}
