<?php

namespace mister42\models\tools;

class PhoneticAlphabetNumeric extends \yii\db\ActiveRecord
{
    public static function tableName(): string
    {
        return 'x_phonetic_num';
    }
}
