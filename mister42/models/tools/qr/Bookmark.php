<?php

namespace mister42\models\tools\qr;

use Yii;

class Bookmark extends \mister42\models\tools\Qr
{
    public $title;
    public $url;

    public function attributeLabels(): array
    {
        $labels = parent::attributeLabels();

        $labels['title'] = Yii::t('mr42', 'Title');
        $labels['url'] = Yii::t('mr42', 'URL');
        return $labels;
    }

    public function generateQr(): bool
    {
        $data = [];
        $this->addData($data, 'TITLE:', $this->title, ';');
        $this->addData($data, 'URL:', $this->url, ';');
        return parent::generate('MEBKM:' . implode($data) . ';');
    }

    public function rules(): array
    {
        $rules = parent::rules();

        $rules[] = ['url', 'required'];
        $rules[] = ['title', 'string'];
        $rules[] = ['url', 'url', 'defaultScheme' => 'http', 'enableIDN' => true];
        return $rules;
    }
}
