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
        $data[] = 'REV:' . date('Ymd\THis\Z');
        $data[] = 'END:VCARD';
        return parent::generate(implode("\n", array_filter($data)));
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
