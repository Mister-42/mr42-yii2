<?php

namespace app\models;

class Articles extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    public function afterFind(): void
    {
        parent::afterFind();
        $this->url = $this->url ?? $this->title;
    }

    public static function find()
    {
        return parent::find()
            ->onCondition(['active' => Self::STATUS_ACTIVE]);
    }

    public static function tableName()
    {
        return '{{%articles}}';
    }
}
