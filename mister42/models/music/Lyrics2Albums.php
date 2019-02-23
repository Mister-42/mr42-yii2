<?php
namespace app\models\music;
use Yii;
use app\models\{Image, Pdf, Video};
use yii\behaviors\TimestampBehavior;
use yii\bootstrap4\Html;
use yii\db\Expression;
use yii\helpers\{ArrayHelper, Url};

class Lyrics2Albums extends \yii\db\ActiveRecord {
	public $playlist_embed;
	public $playlist_url;

	public static function tableName(): string {
		return '{{%lyrics_2_albums}}';
	}

	public function afterFind(): void {
		parent::afterFind();
		$this->url = $this->url ?? $this->name;
		$this->playlist_id = $this->playlist_id ? 'PL'.$this->playlist_id : null;
		$this->playlist_embed = $this->playlist_id && $this->playlist_ratio ? Video::getEmbed('youtube', $this->playlist_id, $this->playlist_ratio, true) : null;
		$this->playlist_url = $this->playlist_id ? Video::getUrl('youtube', $this->playlist_id, true) : null;
		$this->updated = Yii::$app->formatter->asTimestamp($this->updated);
		$this->active = boolval($this->active);
	}

	public function beforeSave($insert): bool {
		if (parent::beforeSave($insert)) :
			$this->url = $this->name === $this->url ? null : $this->url;
			$this->playlist_id = $this->playlist_id ? substr($this->playlist_id, 2) : null;
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

	public static function albumsList(string $artist): array {
		return self::find()
			->orderBy(['year' => SORT_DESC, 'name' => SORT_ASC])
			->innerJoinWith('artist', 'tracks')
			->where(['or', Lyrics1Artists::tableName().'.`name`=:artist', Lyrics1Artists::tableName().'.`url`=:artist'])
			->addParams([':artist' => $artist])
			->all();
	}

	public static function buildPdf(self $album): string {
		$pdf = new Pdf();
		return $pdf->create(
			'@runtime/PDF/lyrics/'.implode(' - ', [$album->artist->url, $album->year, $album->url]),
			Yii::$app->controller->renderPartial('@app/views/music/lyrics-album-pdf', ['tracks' => $album->tracks]),
			Lyrics3Tracks::getLastModified($album->artist->url, $album->year, $album->url),
			[
				'author' => $album->artist->name,
				'footer' => implode('|', [Html::a(Yii::$app->name, Url::to(['site/index'], true)), $album->year, 'Page {PAGENO} of {nb}']),
				'header' => implode('|', [$album->artist->name, 'Lyrics', $album->name]),
				'keywords' => implode(', ', [$album->artist->name, $album->name, 'lyrics']),
				'subject' => implode(' - ', [$album->artist->name, $album->name]),
				'title' => implode(' - ', [$album->artist->name, $album->name, 'Lyrics']),
			]
		);
	}

	public static function getCover(int $size, array $album): array {
		$fileName = null;
		if (ArrayHelper::keyExists(0, $album)) :
			$fileName = implode(' - ', [$album[0]->artist->url, $album[0]->album->year, $album[0]->album->url, $size]).'.jpg';
			$album = $album[0]->album;
		endif;

		return [$fileName, Image::resize($album->image, $size)];
	}

	public static function getLastModified(string $artist): int {
		$data = self::find()
			->innerJoinWith('artist')
			->where(['or', Lyrics1Artists::tableName().'.`name`=:artist', Lyrics1Artists::tableName().'.`url`=:artist'])
			->addParams([':artist' => $artist])
			->max(self::tableName().'.updated');
		return (int) Yii::$app->formatter->asTimestamp($data);
	}

	public function getArtist(): LyricsQuery {
		return $this->hasOne(Lyrics1Artists::className(), ['id' => 'parent']);
	}

	public function getTracks(): LyricsQuery {
		return $this->hasMany(Lyrics3Tracks::className(), ['parent' => 'id']);
	}

	public static function find(): LyricsQuery {
		return new LyricsQuery(get_called_class());
	}
}
