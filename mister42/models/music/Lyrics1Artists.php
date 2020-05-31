<?php

namespace mister42\models\music;

use yii\db\BatchQueryResult;

class Lyrics1Artists extends \yii\db\ActiveRecord
{
    public function afterFind(): void
    {
        parent::afterFind();
        $this->url = $this->url ?? $this->name;
        $this->updated = strtotime($this->updated);
        $this->active = (bool) $this->active;
    }

    public static function albumsList(): BatchQueryResult
    {
        return self::find()
            ->orderBy(['name' => SORT_ASC])
            ->with('albums')
            ->each();
    }

    public static function artistsList(): array
    {
        return self::find()
            ->orderBy(['name' => SORT_ASC])
            ->all();
    }

    public static function find(): LyricsQuery
    {
        return new LyricsQuery(get_called_class());
    }

    public function getAlbums(): LyricsQuery
    {
        return $this->hasMany(Lyrics2Albums::class, ['parent' => 'name'])
            ->orderBy(['year' => SORT_DESC, 'name' => SORT_ASC]);
    }

    public static function getLastModified(): int
    {
        $data = self::find()
            ->max('updated');
        return strtotime($data);
    }
    public static function tableName(): string
    {
        return '{{%lyrics_1_artists}}';
    }
}
