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
        $data[] = 'BEGIN:VEVENT';
        $data[] = $this->getDataOrOmit('SUMMARY:', $this->summary);
        $data[] = $this->getDataOrOmit('DTSTART:', $this->start ? date('Ymd\THis\Z', strtotime($this->start)) : '');
        $data[] = $this->getDataOrOmit('DTEND:', $this->end ? date('Ymd\THis\Z', strtotime($this->end)) : '');
        $data[] = 'END:VEVENT';
        return parent::generate(implode("\n", array_filter($data)));
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
