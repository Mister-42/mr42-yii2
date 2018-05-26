<?php
namespace app\models\tools\qr;
use Yii;

class MeCard extends \app\models\tools\Qr {
	public $firstName;
	public $lastName;
	public $firstSound;
	public $lastSound;
	public $phone;
	public $videoPhone;
	public $email;
	public $note;
	public $birthday;
	public $address;
	public $website;
	public $nickname;

	public function rules(): array {
		$rules = parent::rules();

		$rules[] = ['firstName', 'required'];
		$rules[] = [['email'], 'email', 'enableIDN' => true];
		$rules[] = [['phone', 'videoPhone'], 'string', 'length' => [1, 24]];
		$rules[] = ['birthday', 'date', 'format' => 'php:Y-m-d'];
		$rules[] = ['website', 'url', 'defaultScheme' => 'http', 'enableIDN' => true];
		return $rules;
	}

	public function attributeLabels(): array {
		$labels = parent::attributeLabels();

		$labels['firstName'] = Yii::t('mr42', 'First Name');
		$labels['lastName'] = Yii::t('mr42', 'Last Name');
		$labels['firstSound'] = Yii::t('mr42', 'First Name (phonetic)');
		$labels['lastSound'] = Yii::t('mr42', 'Last Name (phonetic)');
		$labels['phone'] = Yii::t('mr42', 'Telephone Number');
		$labels['videoPhone'] = Yii::t('mr42', 'Video Phone');
		$labels['email'] = Yii::t('mr42', 'Email Address');
		$labels['note'] = Yii::t('mr42', 'Note');
		$labels['birthday'] = Yii::t('mr42', 'Birthday');
		$labels['address'] = Yii::t('mr42', 'Address');
		$labels['website'] = Yii::t('mr42', 'Website URL');
		$labels['nickname'] = Yii::t('mr42', 'Nickname');
		return $labels;
	}

	public function generateQr(): bool {
		$data[] = $this->getDataOrOmit('N:', implode(',', [$this->lastName, $this->firstName]), ';');
		$data[] = $this->getDataOrOmit('SOUND:', implode(',', [$this->lastSound, $this->firstSound]), ';');
		$data[] = $this->getDataOrOmit('TEL:', $this->phone, ';');
		$data[] = $this->getDataOrOmit('TEL-AV:', $this->videoPhone, ';');
		$data[] = $this->getDataOrOmit('EMAIL:', $this->email, ';');
		$data[] = $this->getDataOrOmit('NOTE:', $this->note, ';');
		$data[] = $this->getDataOrOmit('BDAY:', $this->birthday ? date('Ymd', strtotime($this->birthday)) : '', ';');
		$data[] = $this->getDataOrOmit('ADR:', $this->address, ';');
		$data[] = $this->getDataOrOmit('URL:', $this->website, ';');
		$data[] = $this->getDataOrOmit('NICKNAME:', $this->nickname, ';');
		return parent::generate('MECARD:'.implode($data).';');
	}
}
