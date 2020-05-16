<?php

namespace mister42\models\tools;

use joshtronic\LoremIpsum as lipsum;
use Yii;

class LoremIpsum extends \yii\base\Model
{
    public $amount = 20;
    public $text;
    public $type = 'sentences';

    public function attributeLabels(): array
    {
        return [
            'amount' => Yii::t('mr42', 'Amount'),
            'type' => Yii::t('mr42', 'Type'),
        ];
    }

    public function generate(): bool
    {
        $lipsum = new Lipsum();
        Yii::$app->getSession()->setFlash('lorem-ipsum-success', $lipsum->{$this->type}($this->amount));
        return true;
    }

    public function getTypes(bool $rules = false): array
    {
        $types = [
            'words' => Yii::t('mr42', 'Words'),
            'sentences' => Yii::t('mr42', 'Sentences'),
            'paragraphs' => Yii::t('mr42', 'Paragraphs'),
        ];

        foreach ($types as $value => $name) {
            $list[$value] = $rules ? $value : $name;
        }
        return $list;
    }

    public function rules(): array
    {
        return [
            [['amount', 'type'], 'required'],
            ['amount', 'double', 'min' => 1, 'max' => 250],
            ['type', 'in', 'range' => $this->getTypes(true)],
        ];
    }
}
