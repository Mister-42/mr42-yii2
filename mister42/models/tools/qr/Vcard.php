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
		$data[] = $this->getDataOrOmit('N:', implode(';', [$this->firstName, $this->lastName]));
		$data[] = $this->getDataOrOmit('FN:', $this->fullName);
		$data[] = $this->getDataOrOmit('ADR;TYPE=home:;;', $this->homeAddress);
		$data[] = $this->getDataOrOmit('TEL;TYPE=home,voice;', $this->homePhone);
		$data[] = $this->getDataOrOmit('ORG:', $this->organization);
		$data[] = $this->getDataOrOmit('TITLE:', $this->title);
		$data[] = $this->getDataOrOmit('ROLE:', $this->role);
		$data[] = $this->getDataOrOmit('ADR;TYPE=work:;;', $this->workAddress);
		$data[] = $this->getDataOrOmit('TEL;TYPE=work,voice;', $this->workPhone);
		$data[] = $this->getDataOrOmit('EMAIL:', $this->email);
		$data[] = $this->getDataOrOmit('URL:', $this->website);
		$data[] = $this->getDataOrOmit('BDAY:', $this->birthday ? date('Ymd', strtotime($this->birthday)) : '');
		$data[] = $this->getDataOrOmit('NOTE:', $this->note);
		$data[] = 'REV:'.date('Ymd\THis\Z');
		$data[] = "END:VCARD";
		return parent::generate(implode("\n", array_filter($data)));
	}
}
