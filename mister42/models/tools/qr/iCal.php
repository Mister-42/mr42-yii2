<?php
namespace app\models\tools\qr;
use Yii;

class iCal extends \app\models\tools\Qr {
	public $start;
	public $end;
	public $summary;

	public function rules(): array {
		$rules = parent::rules();

		$rules[] = [['start', 'end'], 'required'];
		$rules[] = [['start', 'end'], 'date', 'format' => 'php:Y-m-d H:i'];
		$rules[] = [['summary'], 'string'];
		return $rules;
	}

	public function attributeLabels(): array {
		$labels = parent::attributeLabels();

		$labels['start'] = 'Start Date';
		$labels['end'] = 'End Date';
		return $labels;
	}

	public function generateQr(): bool {
		$data[] = "BEGIN:VEVENT";
		$data[] = "SUMMARY:{$this->summary}";
		$data[] = 'DTSTART:' . date('Ymd\THis\Z', $this->start);
		$data[] = 'DTEND:' . date('Ymd\THis\Z', $this->end);
		$data[] = "END:VEVENT";
        return parent::generate(implode("\n", $data));
	}
}
