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
		$rules[] = [['email'], 'email', 'enableIDN' => true];
		$rules[] = [['subject, message'], 'string'];
		return $rules;
	}

	public function attributeLabels(): array {
		$labels = parent::attributeLabels();

		$labels['email'] = 'Email Address';
		return $labels;
	}

	public function generateQr(): bool {
		$post = Yii::$app->request->post('qr');
		return parent::generate("MATMSG:TO:{$post['email']};SUB:{$post['subject']};BODY:{$post['message']};;");
	}
}
