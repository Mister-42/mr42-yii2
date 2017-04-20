<?php
namespace app\models\lyrics;
use Yii;

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
		$this->video = $this->video_source && $this->video_id && $this->video_ratio ? Yii::$app->formatter->cleanInput("@{$this->video_source}:{$this->video_id}:{$this->video_ratio}", 'original', true) : null;
	}

	public function tracksList($artist, $year, $name) {
		return self::find()
			->orderBy('track')
			->joinWith('artist', 'lyrics')
			->with('album')
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
				$max = max($max, $track->lyrics->updated);
		endforeach;
		return $max;
	}

	public function getArtist() {
		return $this->hasOne(Lyrics1Artists::className(), ['id' => 'parent'])
			->via('album');
	}

	public function getAlbum() {
		return $this->hasOne(Lyrics2Albums::className(), ['id' => 'parent']);
	}

	public function getLyrics() {
		return $this->hasOne(Lyrics4Lyrics::className(), ['id' => 'lyricid']);
	}
}
