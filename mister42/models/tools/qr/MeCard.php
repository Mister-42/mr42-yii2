<?php
namespace app\models\tools\qr;
use Yii;

class MeCard extends \app\models\tools\Qr {
	public $firstName;
	public $lastName;
	public $sound;
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

		$labels['sound'] = 'Name (Phonetic)';
		$labels['phone'] = 'Telephone Number';
		$labels['email'] = 'Email Address';
		return $labels;
	}

	public function generateQr(): bool {
		$data[] = "N:{$this->lastName},{$this->firstName};";
		$data[] = "SOUND:{$this->sound};";
		$data[] = "TEL:{$this->phone};";
		$data[] = "TEL-AV:{$this->videoPhone};";
		$data[] = "EMAIL:{$this->email};";
		$data[] = "NOTE:{$this->note};";
		$data[] = 'BDAY:' . date('Ymd', strtotime($this->birthday)) . ';';
		$data[] = "ADR:{$this->address};";
		$data[] = "URL:{$this->website};";
		$data[] = "NICKNAME:{$this->nickname};";
		return parent::generate('MECARD:' . implode($data) . ';');
	}
}
