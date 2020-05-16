<?php

namespace mister42\models\music;

use Yii;
use yii\db\ActiveQuery;

class Lyrics4Lyrics extends \yii\db\ActiveRecord
{
    public function afterFind(): void
    {
        parent::afterFind();
        $this->lyrics = Yii::$app->formatter->cleanInput($this->lyrics, 'gfm-comment');
        $this->updated = Yii::$app->formatter->asTimestamp($this->updated);
    }

    public static function find(): ActiveQuery
    {
        return parent::find()->alias('lyric');
    }
    public static function tableName(): string
    {
        return '{{%lyrics_4_lyrics}}';
    }
}
