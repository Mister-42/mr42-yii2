<?php
namespace app\models\lyrics;
use Yii;
use app\models\Pdf;
use app\models\lyrics\Lyrics3Tracks;
use yii\bootstrap\Html;
use yii\db\ActiveRecord;
use yii\helpers\Url;

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
				'active' => self::tableName().'.`active`',
			])
			->orderBy($albumSort)
			->where($artistSelect)
			->joinWith('artist')
			->with('tracks')
			->all();
	}

	public function buildPdf($tracks, $html)
	{
		$pdf = new Pdf();
		return $pdf->create(
			'@runtime/PDF/lyrics/'.implode(' - ', [$tracks[0]['artistUrl'], $tracks[0]['albumYear'], $tracks[0]['albumUrl']]),
			$html,
			Lyrics3Tracks::lastUpdate($tracks[0]['artistUrl'], $tracks[0]['albumYear'], $tracks[0]['albumUrl']),
			[
				'author' => $tracks[0]['artistName'],
				'footer' => Html::a(Yii::$app->name, Url::to(['site/index'], true)).'|'.$tracks[0]['albumYear'].'|Page {PAGENO} of {nb}',
				'header' => $tracks[0]['artistName'].'||'.$tracks[0]['albumName'],
				'keywords' => implode(', ', [$tracks[0]['artistName'], $tracks[0]['albumName'], 'lyrics']),
				'subject' => $tracks[0]['artistName'].' - '.$tracks[0]['albumName'],
				'title' => implode(' - ', [$tracks[0]['artistName'], $tracks[0]['albumName']]),
			]
		);
	}

	public function getArtist()
	{
		return $this->hasOne(Lyrics1Artists::className(), ['id' => 'parent']);
	}

	public function getTracks()
	{
		return $this->hasMany(Lyrics3Tracks::className(), ['parent' => 'id']);
	}

	public static function lastUpdate($artist) {
		$lastUpdate = self::find()
			->select(['updated' => 'max(UNIX_TIMESTAMP('.Lyrics2Albums::tableName().'.updated))'])
			->where(['COALESCE('.Lyrics1Artists::tableName().'.`url`,' . Lyrics1Artists::tableName().'.`name`)' => $artist])
			->joinWith('artist')
			->one();
		return $lastUpdate->updated;
	}

	public static function find()
	{
		return parent::find()
			->onCondition(
				php_sapi_name() !== 'cli' && !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin
					? ['or', [self::tableName().'.`active`' => [Lyrics1Artists::STATUS_INACTIVE, Lyrics1Artists::STATUS_ACTIVE]]]
					: [self::tableName().'.`active`' => Lyrics1Artists::STATUS_ACTIVE]
			)
		;
	}
}
