<?php

namespace mister42\models\user;

class WeeklyArtist extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return '{{%lastfm_weeklyartist}}';
    }
}
