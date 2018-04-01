<?php
namespace app\models\lyrics;
use Yii;
use app\models\{Pdf, Video};
use yii\behaviors\TimestampBehavior;
use yii\bootstrap\Html;
use yii\db\Expression;
use yii\helpers\{ArrayHelper, Url};

class Lyrics2Albums extends \yii\db\ActiveRecord {
	public $playlist;
	public $playlist_url;

	public static function tableName() {
		return '{{%lyrics_2_albums}}';
	}

	public function afterFind() {
		parent::afterFind();
		$this->url = $this->url ?? $this->name;
		$this->playlist_id = $this->playlist_id ? 'PL' . $this->playlist_id : null;
		$this->playlist = $this->playlist_id && $this->playlist_ratio ? Video::getEmbed('youtube', $this->playlist_id, $this->playlist_ratio, true) : null;
		$this->playlist_url = $this->playlist_id ? Video::getUrl('youtube', $this->playlist_id, true) : null;
		$this->updated = strtotime($this->updated);
		$this->active = (bool) $this->active;
	}

	public function beforeSave($insert) {
		if (parent::beforeSave($insert)) {
			$this->url = $this->name === $this->url ? null : $this->url;
			$this->active = $this->active ? 1 : 0;
			return true;
		}
		return false;
	}

	public function behaviors() {
		return [
			[
				'class' => TimestampBehavior::class,
				'createdAtAttribute' => 'updated',
				'updatedAtAttribute' => 'updated',
				'value' => new Expression('NOW()'),
			],
		];
	}

	public function albumsList($artist) {
		return self::find()
			->orderBy('year DESC, name')
			->joinWith('artist')
			->with('tracks')
			->where(['or', Lyrics1Artists::tableName().'.`name`=:artist', Lyrics1Artists::tableName().'.`url`=:artist'])
			->addParams([':artist' => $artist])
			->all();
	}

	public function lastUpdate($artist, $data = null, $max = null) {
		$data = $data ?? self::albumsList($artist);
		foreach ($data as $item) :
			$max = max($max, $item->updated);
			foreach ($item->tracks as $track)
				$max = max($max, $track->lyrics->updated);
		endforeach;
		return $max;
	}

	public function buildPdf($album, $html) {
		$pdf = new Pdf();
		return $pdf->create(
			'@runtime/PDF/lyrics/'.implode(' - ', [$album->artist->url, $album->year, $album->url]),
			$html,
			Lyrics3Tracks::lastUpdate($album->artist->url, $album->year, $album->url, (object)['item' => (object)['album' => $album]]),
			[
				'author' => $album->artist->name,
				'footer' => Html::a(Yii::$app->name, Url::to(['site/index'], true)).'|'.$album->year.'|Page {PAGENO} of {nb}',
				'header' => $album->artist->name.'|Lyrics|'.$album->name,
				'keywords' => implode(', ', [$album->artist->name, $album->name, 'lyrics']),
				'subject' => $album->artist->name.' - '.$album->name,
				'title' => implode(' - ', [$album->artist->name, $album->name, 'Lyrics']),
			]
		);
	}

	public function getCover($size, $album) {
		$fileName = null;
		if (ArrayHelper::keyExists(0, $album)) {
			$fileName = implode(' - ', [$album[0]->artist->url, $album[0]->album->year, $album[0]->album->url, $size]).'.jpg';
			$album = $album[0]->album;
		}

		if ($size !== 'cover') {
			$process = proc_open("convert -resize {$size} -strip -quality 85% -interlace Plane - jpg:-", [['pipe', 'r'], ['pipe', 'w']], $pipes);
			if (is_resource($process)) {
				fwrite($pipes[0], $album->image);
				fclose($pipes[0]);

				$album->image = stream_get_contents($pipes[1]);
				fclose($pipes[1]);

				proc_close($process);
			}
		}

		return [$fileName, $album->image];
	}

	public function getArtist() {
		return $this->hasOne(Lyrics1Artists::class, ['id' => 'parent']);
	}

	public function getTracks() {
		return $this->hasMany(Lyrics3Tracks::class, ['parent' => 'id'])
			->with('lyrics');
	}

	public function getLyrics() {
		return $this->hasOne(Lyrics4Lyrics::class, ['id' => 'lyricid']);
	}

	public static function find() {
		return parent::find()
			->onCondition(
				Yii::$app->controller->action->id !== 'sitemap'
					? ['or', [self::tableName().'.`active`' => [Lyrics1Artists::STATUS_INACTIVE, Lyrics1Artists::STATUS_ACTIVE]]]
					: [self::tableName().'.`active`' => Lyrics1Artists::STATUS_ACTIVE]
			);
	}
}
