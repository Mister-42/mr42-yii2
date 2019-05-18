<?php
namespace app\models\music;
use Yii;
use app\models\Video;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

class Lyrics3Tracks extends \yii\db\ActiveRecord {
	public $max;
	public $video;

	public static function tableName(): string {
		return '{{%lyrics_3_tracks}}';
	}

	public function afterFind(): void {
		parent::afterFind();
		$this->track = sprintf('%02d', $this->track);
		$this->disambiguation = $this->disambiguation ? " ({$this->disambiguation})" : null;
		$this->feat = $this->feat ? " (feat. {$this->feat})" : null;
		$this->video_status = boolval($this->video_status);
		$this->wip = boolval($this->wip);
		$this->video = $this->video_source && $this->video_id && $this->video_ratio && $this->video_status ? Video::getEmbed($this->video_source, $this->video_id, $this->video_ratio) : null;
	}

	public function beforeSave($insert): bool {
		if (parent::beforeSave($insert)) :
			$this->video_status = $this->video_status ? 1 : 0;
			$this->wip = $this->wip ? 1 : 0;
			return true;
		endif;
		return false;
	}

	public static function tracksList(string $artist, string $year, string $name): array {
		return self::find()
			->orderBy(['track' => SORT_ASC])
			->joinWith('artist')
			->joinWith('lyrics')
			->where(['or', 'artist.name=:artist', 'artist.url=:artist'])
			->andWhere('album.year=:year')
			->andWhere(['or', 'album.name=:album', 'album.url=:album'])
			->addParams([':artist' => $artist, ':year' => $year, ':album' => $name])
			->all();
	}

	public static function getLastModified(string $artist, string $year, string $name): int {
		$max = self::find()
			->select('GREATEST(MAX(album.updated), MAX(lyric.updated)) AS `max`')
			->joinWith('artist')
			->joinWith('lyrics')
			->where(['or', 'artist.name=:artist', 'artist.url=:artist'])
			->andWhere('album.year=:year')
			->andWhere(['or', 'album.name=:album', 'album.url=:album'])
			->addParams([':artist' => $artist, ':year' => $year, ':album' => $name])
			->one();
		return $max->max ? Yii::$app->formatter->asTimestamp($max->max) : time();
	}

	public function getArtist(): ActiveQuery {
		return $this->hasOne(Lyrics1Artists::class, ['id' => 'parent'])
			->via('album');
	}

	public function getAlbum(): ActiveQuery {
		return $this->hasOne(Lyrics2Albums::class, ['id' => 'parent']);
	}

	public function getLyrics(): ActiveQuery {
		return $this->hasOne(Lyrics4Lyrics::class, ['id' => 'lyricid']);
	}

	public static function find(): ActiveQuery {
		return parent::find()->alias('track');
	}
}
