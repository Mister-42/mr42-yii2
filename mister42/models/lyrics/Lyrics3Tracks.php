<?php
namespace app\models\lyrics;
use Yii;
use app\models\Video;

class Lyrics3Tracks extends \yii\db\ActiveRecord {
	public $hasLyrics;
	public $video;

	public static function tableName() {
		return '{{%lyrics_3_tracks}}';
	}

	public function afterFind() {
		parent::afterFind();
		$this->track = sprintf('%02d', $this->track);
		$this->disambiguation = $this->disambiguation ? ' (' . $this->disambiguation . ')' : null;
		$this->feat = $this->feat ? ' (feat. ' . $this->feat . ')' : null;
		$this->hasLyrics = $this->lyricid && $this->lyricid != "00000000000000000000000000000000";
		$this->video = $this->video_id && $this->video_ratio ? Video::getVideo('youtube', $this->video_id, $this->video_ratio) : null;
	}

	public function tracksList($artist, $year, $name) {
		return self::find()
			->orderBy('track')
			->joinWith('artist')
			->with('album', 'lyrics')
			->where(['or', Lyrics1Artists::tableName().'.name=:artist', Lyrics1Artists::tableName().'.url=:artist'])
			->andWhere(Lyrics2Albums::tableName().'.year=:year')
			->andWhere(['or', Lyrics2Albums::tableName().'.name=:album', Lyrics2Albums::tableName().'.url=:album'])
			->addParams([':artist' => $artist, ':year' => $year, ':album' => $name])
			->all();
	}

	public function lastUpdate($artist, $year, $name, $data = null, $max = null) {
		$data = $data ?? self::tracksList($artist, $year, $name);
		foreach ($data as $item) :
			$max = max($max, $item->album->updated);
			foreach ($item->album->tracks as $track)
				if ($track->lyrics)
					$max = max($max, $track->lyrics->updated);
		endforeach;
		return $max;
	}

	public function getArtist() {
		return $this->hasOne(Lyrics1Artists::class, ['id' => 'parent'])
			->via('album');
	}

	public function getAlbum() {
		return $this->hasOne(Lyrics2Albums::class, ['id' => 'parent']);
	}

	public function getLyrics() {
		return $this->hasOne(Lyrics4Lyrics::class, ['id' => 'lyricid']);
	}
}
