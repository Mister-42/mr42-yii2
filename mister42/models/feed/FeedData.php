<?php

namespace mister42\models\feed;

class FeedData extends \yii\db\ActiveRecord
{
    public function rules(): array
    {
        return [
            [['feed', 'time'], 'unique', 'targetAttribute' => ['feed', 'time']],
        ];
    }

    public static function tableName(): string
    {
        return '{{x_feed_data}}';
    }
}
