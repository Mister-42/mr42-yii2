<?php
namespace app\models\lyrics;
use Yii;
use app\models\Pdf;
use yii\bootstrap\Html;
use yii\helpers\Url;

class Lyrics2Albums extends \yii\db\ActiveRecord {
	public static function tableName() {
		return '{{%lyrics_2_albums}}';
	}

	public function afterFind() {
		parent::afterFind();
		$this->url = $this->url ?? $this->name;
		$this->updated = strtotime($this->updated);
		$this->active = (bool) $this->active;
	}

	public function albumsList($artist) {
		return self::find()
			->orderBy('year DESC, name')
			->joinWith('artist')
			->with('tracks')
			->where([Lyrics1Artists::tableName().'.name' => $artist])
			->orWhere([Lyrics1Artists::tableName().'.url' => $artist])
			->all();
	}

	public function lastUpdate($artist, $data = null, $max = null) {
		$data = $data ?? self::albumsList($artist);
		foreach ($data as $item)
			$max = max($max, $item->updated);
		return $max;
	}

	public function buildPdf($tracks, $html) {
		$pdf = new Pdf();
		return $pdf->create(
			'@runtime/PDF/lyrics/'.implode(' - ', [$tracks[0]->artist->url, $tracks[0]->album->year, $tracks[0]->album->url]),
			$html,
			Lyrics3Tracks::lastUpdate($tracks[0]->artist->url, $tracks[0]->album->year, $tracks[0]->album->url, $tracks),
			[
				'author' => $tracks[0]->artist->name,
				'footer' => Html::a(Yii::$app->name, Url::to(['site/index'], true)).'|'.$tracks[0]->album->year.'|Page {PAGENO} of {nb}',
				'header' => $tracks[0]->artist->name.'||'.$tracks[0]->album->name,
				'keywords' => implode(', ', [$tracks[0]->artist->name, $tracks[0]->album->name, 'lyrics']),
				'subject' => $tracks[0]->artist->name.' - '.$tracks[0]->album->name,
				'title' => implode(' - ', [$tracks[0]->artist->name, $tracks[0]->album->name]),
			]
		);
	}

	public function getArtist() {
		return $this->hasOne(Lyrics1Artists::className(), ['id' => 'parent']);
	}

	public function getTracks() {
		return $this->hasMany(Lyrics3Tracks::className(), ['parent' => 'id']);
	}

	public static function find() {
		return parent::find()
			->onCondition(
				php_sapi_name() !== 'cli' && !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin
					? ['or', [self::tableName().'.`active`' => [Lyrics1Artists::STATUS_INACTIVE, Lyrics1Artists::STATUS_ACTIVE]]]
					: [self::tableName().'.`active`' => Lyrics1Artists::STATUS_ACTIVE]
			);
	}
}
