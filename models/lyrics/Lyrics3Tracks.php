<?php
namespace app\models\lyrics;
use Yii;
use yii\db\ActiveRecord;

class Lyrics3Tracks extends ActiveRecord
{
	public $artistName;
	public $artistUrl;
	public $albumName;
	public $albumUrl;
	public $albumYear;
	public $trackNumber;
	public $trackName;
	public $trackLyrics;
	public $updated;
	public $active;

	public static function tableName()
	{
		return '{{%lyrics_3_tracks}}';
	}

	public static function lastUpdate($artist, $year, $name) {
		$data = self::tracksList($artist, $year, $name, 'lastUpdate');
		return $data[0]['updated'];
	}

	public static function tracksList($artist, $year, $name, $view = null) {
		$data = self::find()
			->select([
				'artistName' => Lyrics1Artists::tableName() . '.`name`',
				'artistUrl' => 'COALESCE('.Lyrics1Artists::tableName().'.`url`, ' . Lyrics1Artists::tableName().'.`name`)',
				'albumName' => Lyrics2Albums::tableName().'.`name`',
				'albumUrl' => 'COALESCE('.Lyrics2Albums::tableName().'.`url`, ' . Lyrics2Albums::tableName().'.`name`)',
				'albumYear' => Lyrics2Albums::tableName().'.`year`',
				'trackNumber' => self::tableName().'.`track`',
				'trackName' => self::tableName().'.`name`',
				'trackLyrics' => ($view == 'full') ? Lyrics4Lyrics::tableName().'.lyrics' : self::tableName().'.`lyricid`',
				'updated' => ($view == 'lastUpdate') ? 'max(UNIX_TIMESTAMP('.Lyrics4Lyrics::tableName().'.`updated`))' : 'UNIX_TIMESTAMP('.Lyrics4Lyrics::tableName().'.`updated`)',
				'active' => Lyrics2Albums::tableName().'.`active`',
			])
			->orderBy(self::tableName().'.`track`')
			->where([
				'COALESCE('.Lyrics1Artists::tableName().'.`url`, ' . Lyrics1Artists::tableName().'.`name`)' => $artist,
				Lyrics2Albums::tableName().'.`year`' => $year,
				'COALESCE('.Lyrics2Albums::tableName().'.`url`, ' . Lyrics2Albums::tableName().'.`name`)' => $name,
			])
			->join('RIGHT JOIN', Lyrics1Artists::tableName(), Lyrics2Albums::tableName().'.`parent` = ' . Lyrics1Artists::tableName().'.`id`')
				->onCondition(!Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin ? ['or', [Lyrics1Artists::tableName().'.`active`' => [Lyrics1Artists::STATUS_INACTIVE, Lyrics1Artists::STATUS_ACTIVE]]] : [Lyrics1Artists::tableName().'.`active`' => Lyrics1Artists::STATUS_ACTIVE])
			->joinWith('album')
			->joinWith('lyric')
			->all();
		return $data;
	}

	public function getAlbum()
	{
		return $this->hasOne(Lyrics2Albums::className(), ['id' => 'parent']);
	}
   
	public function getLyric()
	{
		return $this->hasOne(Lyrics4Lyrics::className(), ['id' => 'lyricid']);
	}
}
