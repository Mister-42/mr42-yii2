<?php
namespace app\models\tools\qr;
use Yii;

class EmailMessage extends \app\models\tools\Qr {
	public $email;
	public $subject;
	public $message;

	public function rules(): array {
		$rules = parent::rules();

		$rules[] = [['email', 'subject', 'message'], 'required'];
		$rules[] = ['email', 'email', 'enableIDN' => true];
		$rules[] = [['subject', 'message'], 'string'];
		return $rules;
	}

	public function attributeLabels(): array {
		$labels = parent::attributeLabels();

		$labels['email'] = Yii::t('mr42', 'Email Address');
		$labels['subject'] = Yii::t('mr42', 'Subject');
		$labels['message'] = Yii::t('mr42', 'Message');
		return $labels;
	}

	public function generateQr(): bool {
		$data[] = $this->getDataOrOmit('TO:', $this->email, ';');
		$data[] = $this->getDataOrOmit('SUB:', $this->subject, ';');
		$data[] = $this->getDataOrOmit('BODY:', $this->message, ';');
		return parent::generate('MATMSG:'.implode($data).';');
	}
}
