<?php
namespace app\models\lyrics;
use Yii;
use app\models\{Image, Pdf, Video};
use yii\behaviors\TimestampBehavior;
use yii\bootstrap4\Html;
use yii\db\Expression;
use yii\helpers\{ArrayHelper, Url};

class Lyrics2Albums extends \yii\db\ActiveRecord {
	const STATUS_INACTIVE = '0';
	const STATUS_ACTIVE = '1';

	public $playlist_embed;
	public $playlist_url;

	public static function tableName(): string {
		return '{{%lyrics_2_albums}}';
	}

	public function afterFind() {
		parent::afterFind();
		$this->url = $this->url ?? $this->name;
		$this->playlist_id = $this->playlist_id ? 'PL'.$this->playlist_id : null;
		$this->playlist_embed = $this->playlist_id && $this->playlist_ratio ? Video::getEmbed('youtube', $this->playlist_id, $this->playlist_ratio, true) : null;
		$this->playlist_url = $this->playlist_id ? Video::getUrl('youtube', $this->playlist_id, true) : null;
		$this->updated = strtotime($this->updated);
		$this->active = (bool) $this->active;
	}

	public function beforeSave($insert): bool {
		if (parent::beforeSave($insert)) :
			$this->url = $this->name === $this->url ? null : $this->url;
			$this->playlist_id = substr($this->playlist_id, 2);
			$this->active = $this->active ? 1 : 0;
			return true;
		endif;
		return false;
	}

	public function behaviors(): array {
		return [
			[
				'class' => TimestampBehavior::class,
				'createdAtAttribute' => 'updated',
				'updatedAtAttribute' => 'updated',
				'value' => new Expression('NOW()'),
			],
		];
	}

	public static function albumsList(string $artist) {
		return self::find()
			->orderBy('year DESC, name')
			->joinWith('artist')
			->with('tracks')
			->where(['or', Lyrics1Artists::tableName().'.`name`=:artist', Lyrics1Artists::tableName().'.`url`=:artist'])
			->addParams([':artist' => $artist])
			->all();
	}

	public static function lastUpdate(string $artist, $data = null): int {
		$data = $data ?? self::albumsList($artist);
		$max = 0;
		foreach ($data as $item) :
			$max = max($max, $item->updated);
			foreach ($item->tracks as $track) :
				$max = max($max, $track->lyrics->updated ?? 0);
			endforeach;
		endforeach;
		return $max;
	}

	public static function buildPdf($album, string $html): string {
		$pdf = new Pdf();
		return $pdf->create(
			'@runtime/PDF/lyrics/'.implode(' - ', [$album->artist->url, $album->year, $album->url]),
			$html,
			Lyrics3Tracks::lastUpdate($album->artist->url, $album->year, $album->url, (object) ['item' => (object) ['album' => $album]]),
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

	public static function getCover(int $size, $album): array {
		$fileName = null;
		if (ArrayHelper::keyExists(0, $album)) :
			$fileName = implode(' - ', [$album[0]->artist->url, $album[0]->album->year, $album[0]->album->url, $size]).'.jpg';
			$album = $album[0]->album;
		endif;

		if ($size !== 'cover') :
			$album->image = Image::resize($album->image, $size);
		endif;

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
				php_sapi_name() === 'cli' || (!Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin)
					? ['or', [self::tableName().'.`active`' => [self::STATUS_INACTIVE, self::STATUS_ACTIVE]]]
					: [self::tableName().'.`active`' => self::STATUS_ACTIVE]
			);
	}
}
