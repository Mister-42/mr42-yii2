<?php

namespace app\models\music;

use app\models\Video;
use Yii;
use yii\db\ActiveQuery;

class Lyrics3Tracks extends \yii\db\ActiveRecord
{
    public $icons;
    public $max;
    public $nameExtra;
    public $video;

    public function afterFind(): void
    {
        parent::afterFind();
        $this->track = sprintf('%02d', $this->track);
        $this->nameExtra = ($this->disambiguation ? " ({$this->disambiguation})" : null) . ($this->feat ? " (feat. {$this->feat})" : null);
        $this->video_status = (bool) ($this->video_status);
        $this->wip = (bool) ($this->wip);
        $this->video = $this->video_source && $this->video_id && $this->video_ratio && $this->video_status ? Video::getEmbed($this->video_source, $this->video_id, $this->video_ratio) : null;

        $icons = [];
        if ($this->video) {
            $icons[] = (string) Yii::$app->icon->name($this->video_source, 'brands')->class('text-muted ml-1')->title(Yii::t('mr42', 'Video'));
        }
        if ($this->wip) {
            $icons[] = (string) Yii::$app->icon->name('plus')->class('text-muted ml-1')->title(Yii::t('mr42', 'Work in Progress'));
        } elseif (!$this->lyricid) {
            $icons[] = (string) Yii::$app->icon->name('music')->class('text-muted ml-1')->title(Yii::t('mr42', 'Instrumental'));
        }
        $this->icons = implode($icons);
    }

    public function beforeSave($insert): bool
    {
        if (parent::beforeSave($insert)) {
            $this->video_status = $this->video_status ? 1 : 0;
            $this->wip = $this->wip ? 1 : 0;
            return true;
        }
        return false;
    }

    public static function find(): ActiveQuery
    {
        return parent::find()->alias('track');
    }

    public function getAlbum(): ActiveQuery
    {
        return $this->hasOne(Lyrics2Albums::class, ['id' => 'parent']);
    }

    public function getArtist(): ActiveQuery
    {
        return $this->hasOne(Lyrics1Artists::class, ['name' => 'parent'])
            ->via('album');
    }

    public static function getLastModified(string $artist, string $year, string $name): int
    {
        $max = self::find()
            ->select('GREATEST(MAX(album.updated), MAX(lyric.updated)) AS `max`')
            ->joinWith(['artist', 'lyrics'])
            ->where(['or', 'artist.name=:artist', 'artist.url=:artist'])
            ->andWhere('album.year=:year')
            ->andWhere(['or', 'album.name=:album', 'album.url=:album'])
            ->addParams([':artist' => $artist, ':year' => $year, ':album' => $name])
            ->scalar();
        return $max ? Yii::$app->formatter->asTimestamp($max) : time();
    }

    public function getLyrics(): ActiveQuery
    {
        return $this->hasOne(Lyrics4Lyrics::class, ['id' => 'lyricid']);
    }

    public static function tableName(): string
    {
        return '{{%lyrics_3_tracks}}';
    }

    public static function tracksList(string $artist, string $year, string $name): array
    {
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
}
