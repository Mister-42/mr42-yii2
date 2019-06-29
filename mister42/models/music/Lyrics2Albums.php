<?php

namespace app\models\music;

use app\models\Image;
use app\models\Pdf;
use app\models\Video;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\bootstrap4\Html;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class Lyrics2Albums extends \yii\db\ActiveRecord
{
    public $playlist_embed;
    public $playlist_url;

    public function afterFind(): void
    {
        parent::afterFind();
        $this->url = $this->url ?? $this->name;
        $this->playlist_status = (bool) ($this->playlist_status);
        $this->playlist_embed = $this->playlist_source && $this->playlist_id && $this->playlist_ratio && $this->playlist_status ? Video::getEmbed($this->playlist_source, $this->playlist_id, $this->playlist_ratio, true) : null;
        $this->playlist_url = $this->playlist_source && $this->playlist_id ? Video::getUrl($this->playlist_source, $this->playlist_id, true) : null;
        $this->created = Yii::$app->formatter->asTimestamp($this->created);
        $this->updated = Yii::$app->formatter->asTimestamp($this->updated);
        $this->active = (bool) ($this->active);
    }

    public static function albumsList(string $artist): array
    {
        return self::find()
            ->orderBy(['year' => SORT_DESC, 'name' => SORT_ASC])
            ->innerJoinWith('artist', 'tracks')
            ->with('artistInfo')
            ->where(['or', 'artist.name=:artist', 'artist.url=:artist'])
            ->addParams([':artist' => $artist])
            ->all();
    }

    public function beforeSave($insert): bool
    {
        if (parent::beforeSave($insert)) {
            $this->url = $this->name === $this->url ? null : $this->url;
            $this->playlist_status = $this->playlist_status ? 1 : 0;
            $this->created = $this->oldAttributes['created'];
            $this->active = $this->active ? 1 : 0;
            return true;
        }
        return false;
    }

    public function behaviors(): array
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created',
                'updatedAtAttribute' => 'updated',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    public static function buildPdf(self $album): string
    {
        $pdf = new Pdf();
        return $pdf->create(
            '@runtime/PDF/lyrics/' . implode(' - ', [$album->artist->url, $album->year, $album->url]),
            Yii::$app->controller->renderPartial('@app/views/music/lyrics-album-pdf', ['tracks' => $album->tracks]),
            Lyrics3Tracks::getLastModified($album->artist->url, $album->year, $album->url),
            [
                'author' => $album->artist->name,
                'created' => $album->created,
                'footer' => implode('|', [Html::a(Yii::$app->name, Yii::$app->params['shortDomain']), $album->year, 'Page {PAGENO} of {nb}']),
                'header' => implode('|', [$album->artist->name, 'Lyrics', $album->name]),
                'keywords' => implode(', ', [$album->artist->name, $album->name, 'lyrics']),
                'subject' => implode(' - ', [$album->artist->name, $album->name]),
                'title' => implode(' - ', [$album->artist->name, $album->name, 'Lyrics']),
            ]
        );
    }

    public static function find(): LyricsQuery
    {
        return new LyricsQuery(get_called_class());
    }

    public function getArtist(): LyricsQuery
    {
        return $this->hasOne(Lyrics1Artists::class, ['id' => 'parent']);
    }

    public function getArtistInfo(): ActiveQuery
    {
        return $this->hasOne(LyricsArtistInfo::class, ['parent' => 'id'])
            ->via('artist');
    }

    public static function getCover(int $size, array $album): array
    {
        $fileName = null;
        if (ArrayHelper::keyExists(0, $album)) {
            $fileName = implode(' - ', [$album[0]->artist->url, $album[0]->album->year, $album[0]->album->url, $size]) . '.jpg';
            $album = $album[0]->album;
        }

        return [$fileName, Image::resize($album->image, $size)];
    }

    public static function getLastModified(string $artist): int
    {
        $data = self::find()
            ->innerJoinWith('artist')
            ->where(['or', 'artist.name=:artist', 'artist.url=:artist'])
            ->addParams([':artist' => $artist])
            ->max('album.updated');
        return (int) Yii::$app->formatter->asTimestamp($data);
    }

    public function getTracks(): ActiveQuery
    {
        return $this->hasMany(Lyrics3Tracks::class, ['parent' => 'id']);
    }

    public static function tableName(): string
    {
        return '{{%lyrics_2_albums}}';
    }
}
