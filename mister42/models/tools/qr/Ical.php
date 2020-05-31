<?php

namespace mister42\models\tools\qr;

use Yii;

class Ical extends \mister42\models\tools\Qr
{
    public $end;
    public $start;
    public $summary;

    public function attributeLabels(): array
    {
        $labels = parent::attributeLabels();

        $labels['start'] = Yii::t('mr42', 'Start Date');
        $labels['end'] = Yii::t('mr42', 'End Date');
        $labels['summary'] = Yii::t('mr42', 'Summary');
        return $labels;
    }

    public function generateQr(): bool
    {
        $data = [];
        $this->addData($data, 'BEGIN:', 'VEVENT');
        $this->addData($data, 'SUMMARY:', $this->summary);
        $this->addData($data, 'DTSTART:', date('Ymd\THis\Z', strtotime($this->start)));
        $this->addData($data, 'DTEND:', date('Ymd\THis\Z', strtotime($this->end)));
        $this->addData($data, 'END:', 'VEVENT');
        return parent::generate(implode("\n", $data));
    }

    public function rules(): array
    {
        $rules = parent::rules();

        $rules[] = [['start', 'end'], 'required'];
        $rules[] = [['start', 'end'], 'date', 'format' => 'php:Y-m-d H:i'];
        $rules[] = ['summary', 'string'];
        return $rules;
    }
}
