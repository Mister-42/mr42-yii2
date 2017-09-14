<?php
namespace app\models\tools\qr;
use Yii;

class Vcard extends \app\models\tools\Qr {
	public $firstName;
	public $lastName;
	public $fullName;
	public $homeAddress;
	public $homePhone;
	public $organization;
	public $title;
	public $role;
	public $workAddress;
	public $workPhone;
	public $email;
	public $website;
	public $birthday;
	public $note;

	public function rules(): array {
		$rules = parent::rules();

		$rules[] = ['firstName', 'required'];
		$rules[] = ['email', 'email', 'enableIDN' => true];
		$rules[] = ['website', 'url', 'defaultScheme' => 'http', 'enableIDN' => true];
		$rules[] = ['birthday', 'date', 'format' => 'php:Y-m-d'];
		return $rules;
	}

	public function attributeLabels(): array {
		$labels = parent::attributeLabels();

		$labels['email'] = 'Email Address';
		return $labels;
	}

	public function generateQr(): bool {
		$data[] = 'BEGIN:VCARD';
		$data[] = 'VERSION:4.0';
		$data[] = "N:{$this->firstName};{$this->lastName}";
		$data[] = "FN:{$this->fullName}";
		$data[] = "ADR;TYPE=home:;;{$this->homeAddress}";
		$data[] = "TEL;TYPE=home,voice;{$this->homePhone}";
		$data[] = "ORG:{$this->organization}";
		$data[] = "TITLE:{$this->title}";
		$data[] = "ROLE:{$this->role}";
		$data[] = "ADR;TYPE=work:;;{$this->workAddress}";
		$data[] = "TEL;TYPE=work,voice;{$this->workPhone}";
		$data[] = "EMAIL:{$this->email}";
		$data[] = "URL:{$this->website}";
		$data[] = 'BDAY:' . date('Ymd', strtotime($this->birthday));
		$data[] = "NOTE:{$this->note}";
		$data[] = 'REV:' . date('Ymd\THis\Z');
		$data[] = "END:VCARD";
		return parent::generate(implode("\n", $data));
	}
}
