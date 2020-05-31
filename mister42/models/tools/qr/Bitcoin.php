<?php

namespace mister42\models\tools\qr;

use Yii;

class Bitcoin extends \mister42\models\tools\Qr
{
    public $address;
    public $amount;
    public $message;
    public $name;

    public function attributeLabels(): array
    {
        $labels = parent::attributeLabels();

        $labels['address'] = Yii::t('mr42', 'Address');
        $labels['amount'] = Yii::t('mr42', 'Amount');
        $labels['name'] = Yii::t('mr42', 'Name');
        $labels['message'] = Yii::t('mr42', 'Message');
        return $labels;
    }

    public function generateQr(): bool
    {
        $data = [];
        $this->addData($data, 'amount', $this->amount);
        $this->addData($data, 'label', $this->name);
        $this->addData($data, 'message', $this->message);
        $query = http_build_query($data);
        return parent::generate("bitcoin:{$this->address}?{$query}");
    }

    public function rules(): array
    {
        $rules = parent::rules();

        $rules[] = [['address', 'amount'], 'required'];
        $rules[] = [['address', 'name', 'message'], 'string'];
        return $rules;
    }
}
