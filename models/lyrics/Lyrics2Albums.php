<?php
namespace app\models\lyrics;
use Yii;
use app\models\Pdf;
use yii\behaviors\TimestampBehavior;
use yii\bootstrap\Html;
use yii\db\Expression;
use yii\helpers\Url;

class Lyrics2Albums extends \yii\db\ActiveRecord {
	public $video;

	public static function tableName() {
		return '{{%lyrics_2_albums}}';
	}

	public function afterFind() {
		parent::afterFind();
		$this->url = $this->url ?? $this->name;
		$this->video = $this->playlist_source && $this->playlist_id && $this->playlist_ratio ? Yii::$app->formatter->getVideo($this->playlist_source, $this->playlist_id, $this->playlist_ratio, true) : null;
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
				'class' => TimestampBehavior::className(),
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

	public function getCover($size, $tracks) {
		$image = $tracks->image ?? $tracks[0]->album->image;
		if ($size !== 'cover') {
			$process = proc_open("convert -resize {$size} -strip -quality 85% -interlace Plane - jpg:-", [0 => ['pipe', 'r'], 1 => ['pipe', 'w']], $pipes);
			if (is_resource($process)) {
				fwrite($pipes[0], $image);
				fclose($pipes[0]);

				$image = stream_get_contents($pipes[1]);
				fclose($pipes[1]);

				proc_close($process);
			}
		}

		if (!$tracks->image) {
			$fileName = implode(' - ', [$tracks[0]->artist->url, $tracks[0]->album->year, $tracks[0]->album->url]);
			$fileName .= $size === 'cover' ? '' : '-' . $size;
		}
		return $tracks->image ? [$image] : [$fileName.'.jpg', $image];
	}

	public function getArtist() {
		return $this->hasOne(Lyrics1Artists::className(), ['id' => 'parent']);
	}

	public function getTracks() {
		return $this->hasMany(Lyrics3Tracks::className(), ['parent' => 'id'])
			->with('lyrics');
	}

	public function getLyrics() {
		return $this->hasOne(Lyrics4Lyrics::className(), ['id' => 'lyricid']);
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
