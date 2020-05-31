<?php

namespace mister42\models\tools\qr;

use Yii;

class Vcard extends \mister42\models\tools\Qr
{
    public $birthday;
    public $email;
    public $firstName;
    public $fullName;
    public $homeAddress;
    public $homePhone;
    public $lastName;
    public $note;
    public $organization;
    public $role;
    public $title;
    public $website;
    public $workAddress;
    public $workPhone;

    public function attributeLabels(): array
    {
        $labels = parent::attributeLabels();

        $labels['firstName'] = Yii::t('mr42', 'First Name');
        $labels['lastName'] = Yii::t('mr42', 'Last Name');
        $labels['fullName'] = Yii::t('mr42', 'Full Name');
        $labels['homeAddress'] = Yii::t('mr42', 'Home Address');
        $labels['homePhone'] = Yii::t('mr42', 'Home Telephone Number');
        $labels['organization'] = Yii::t('mr42', 'Organization');
        $labels['title'] = Yii::t('mr42', 'Title');
        $labels['role'] = Yii::t('mr42', 'Role');
        $labels['workAddress'] = Yii::t('mr42', 'Work Address');
        $labels['workPhone'] = Yii::t('mr42', 'Work Telephone Number');
        $labels['email'] = Yii::t('mr42', 'Email Address');
        $labels['website'] = Yii::t('mr42', 'Website URL');
        $labels['birthday'] = Yii::t('mr42', 'Birthday');
        $labels['note'] = Yii::t('mr42', 'Note');
        return $labels;
    }

    public function generateQr(): bool
    {
        $data = [];
        $this->addData($data, 'BEGIN:', 'VCARD');
        $this->addData($data, 'VERSION:', '4.0');
        $this->addData($data, 'N:', implode(';', [$this->firstName, $this->lastName]));
        $this->addData($data, 'FN:', $this->fullName);
        $this->addData($data, 'ADR;TYPE=home:;;', $this->homeAddress);
        $this->addData($data, 'TEL;TYPE=home,voice;', $this->homePhone);
        $this->addData($data, 'ORG:', $this->organization);
        $this->addData($data, 'TITLE:', $this->title);
        $this->addData($data, 'ROLE:', $this->role);
        $this->addData($data, 'ADR;TYPE=work:;;', $this->workAddress);
        $this->addData($data, 'TEL;TYPE=work,voice;', $this->workPhone);
        $this->addData($data, 'EMAIL:', $this->email);
        $this->addData($data, 'URL:', $this->website);
        $this->addData($data, 'BDAY:', $this->birthday ? date('Ymd', strtotime($this->birthday)) : null);
        $this->addData($data, 'NOTE:', $this->note);
        $this->addData($data, 'REV:', date('Ymd\THis\Z'));
        $this->addData($data, 'END:', 'VCARD');
        return parent::generate(implode("\n", $data));
    }

    public function rules(): array
    {
        $rules = parent::rules();

        $rules[] = ['firstName', 'required'];
        $rules[] = ['email', 'email', 'enableIDN' => true];
        $rules[] = ['website', 'url', 'defaultScheme' => 'http', 'enableIDN' => true];
        $rules[] = ['birthday', 'date', 'format' => 'php:Y-m-d'];
        return $rules;
    }
}
