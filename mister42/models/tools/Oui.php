<?php

namespace app\models\tools;

use Yii;

class Oui extends \yii\db\ActiveRecord
{
    public $oui;

    public function attributeLabels(): array
    {
        return [
            'oui' => Yii::t('mr42', 'OUI, MAC address, or name'),
        ];
    }

    public function rules(): array
    {
        return [
            [['oui'], 'required'],
        ];
    }

    public static function tableName(): string
    {
        return '{{%oui}}';
    }
}
