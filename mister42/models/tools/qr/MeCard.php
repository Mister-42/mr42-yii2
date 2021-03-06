<?php

namespace mister42\models\tools\qr;

use Yii;

class MeCard extends \mister42\models\tools\Qr
{
    public $address;
    public $birthday;
    public $email;
    public $firstName;
    public $firstSound;
    public $lastName;
    public $lastSound;
    public $nickname;
    public $note;
    public $phone;
    public $videoPhone;
    public $website;

    public function attributeLabels(): array
    {
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

    public function generateQr(): bool
    {
        $data = [];
        $this->addData($data, 'N:', implode(',', [$this->lastName, $this->firstName]), ';');
        $this->addData($data, 'SOUND:', implode(',', [$this->lastSound, $this->firstSound]), ';');
        $this->addData($data, 'TEL:', $this->phone, ';');
        $this->addData($data, 'TEL-AV:', $this->videoPhone, ';');
        $this->addData($data, 'EMAIL:', $this->email, ';');
        $this->addData($data, 'NOTE:', $this->note, ';');
        $this->addData($data, 'BDAY:', $this->birthday ? date('Ymd', strtotime($this->birthday)) : null, ';');
        $this->addData($data, 'ADR:', $this->address, ';');
        $this->addData($data, 'URL:', $this->website, ';');
        $this->addData($data, 'NICKNAME:', $this->nickname, ';');
        return parent::generate('MECARD:' . implode($data) . ';');
    }

    public function rules(): array
    {
        $rules = parent::rules();

        $rules[] = ['firstName', 'required'];
        $rules[] = [['email'], 'email', 'enableIDN' => true];
        $rules[] = [['phone', 'videoPhone'], 'string', 'length' => [1, 24]];
        $rules[] = ['birthday', 'date', 'format' => 'php:Y-m-d'];
        $rules[] = ['website', 'url', 'defaultScheme' => 'http', 'enableIDN' => true];
        return $rules;
    }
}
