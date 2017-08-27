<?php
namespace app\models\tools;
use Yii;
use Da\QrCode\QrCode;
use phamxuanloc\jui\DateTimePicker;
use yii\bootstrap\{ActiveForm, Html};
use yii\helpers\{ArrayHelper, FileHelper};
use yii\jui\DatePicker;

class Qr extends \yii\base\Model {
	public $type;
	public $address;
	public $amount;
	public $name;
	public $title;
	public $url;
	public $lat;
	public $lng;
	public $altitude;
	public $summary;
	public $dtStart;
	public $dtEnd;
	public $email;
	public $subject;
	public $firstName;
	public $lastName;
	public $sound;
	public $phone;
	public $videoPhone;
	public $note;
	public $birthday;
	public $nickName;
	public $msg;
	public $fullName;
	public $authentication;
	public $ssid;
	public $password;
	public $hidden;
	public $videoId;
	public $size = 150;
	public $margin = 0;

	public function rules(): array {
		return [
			[['type', 'size', 'margin'], 'required'],
			['type', 'in', 'range' => self::getTypes(true)],
			[['size'], 'double', 'min' => 50, 'max' => 1500],
			[['margin'], 'double', 'min' => 0, 'max' => 10],
			[['amount'], 'safe'],
			[['lat', 'lng', 'altitude'], 'double'],
			[['address', 'name', 'title', 'summary', 'subject', 'firstName', 'lastName', 'sound', 'note', 'nickName', 'msg', 'ssid', 'password'], 'string'],
			[['email'], 'email'],
			[['url'], 'url', 'defaultScheme' => 'http', 'enableIDN' => true],
			[['dtStart', 'dtEnd'], 'date', 'format' => 'php:Y-m-d H:i'],
			['dtStart', 'compare', 'compareAttribute' => 'dtEnd', 'operator' => '<', 'enableClientValidation' => false],
			['birthday', 'date', 'format' => 'php:Y-m-d', 'max' => date('Y-m-d'), 'min' => date('Y-m-d', strtotime('-110 years'))],
			[['authentication'], 'in', 'range' => self::getAuthentication()],
			[['hidden'], 'boolean'],
			[['phone', 'videoPhone'], 'string'],
			['url', 'required', 'when' => function ($model) {
					return $model->type == 'BookMarkFormat';
				}, 'whenClient' => "function(attribute,value){return $('#qr-type').val()=='BookMarkFormat';}"],
			[['address', 'amount'], 'required', 'when' => function ($model) {
					return $model->type == 'BtcFormat';
				}, 'whenClient' => "function(attribute,value){return $('#qr-type').val()=='BtcFormat';}"],
			[['lat', 'lng', 'altitude'], 'required', 'when' => function ($model) {
					return $model->type == 'GeoFormat';
				}, 'whenClient' => "function(attribute,value){return $('#qr-type').val()=='GeoFormat';}"],
			[['dtStart', 'dtEnd'], 'required', 'when' => function ($model) {
					return $model->type == 'iCalFormat';
				}, 'whenClient' => "function(attribute,value){return $('#qr-type').val()=='iCalFormat';}"],
			[['email', 'subject', 'msg'], 'required', 'when' => function ($model) {
					return $model->type == 'MailMessageFormat';
				}, 'whenClient' => "function(attribute,value){return $('#qr-type').val()=='MailMessageFormat';}"],
			['email', 'required', 'when' => function ($model) {
					return $model->type == 'MailToFormat';
				}, 'whenClient' => "function(attribute,value){return $('#qr-type').val()=='MailToFormat';}"],
			[['fistName', 'lastName', 'email'], 'required', 'when' => function ($model) {
					return $model->type == 'MeCardFormat';
				}, 'whenClient' => "function(attribute,value){return $('#qr-type').val()=='MeCardFormat';}"],
			['phone', 'required', 'when' => function ($model) {
					return $model->type == 'MmsFormat' || $model->type == 'PhoneFormat' || $model->type == 'SmsFormat';
				}, 'whenClient' => "function(attribute,value){return $('#qr-type').val()=='MmsFormat'||$('#qr-type').val()=='PhoneFormat'||$('#qr-type').val()=='SmsFormat';}"],
			[['authentication', 'ssid'], 'required', 'when' => function ($model) {
					return $model->type == 'WifiFormat';
				}, 'whenClient' => "function(attribute,value){return $('#qr-type').val()=='WifiFormat';}"],
			['password', 'required', 'when' => function ($model) {
					return $model->type == 'WifiFormat' && $model->authentication !== 'none';
				}, 'whenClient' => "function(attribute,value){return $('#qr-type').val()=='WifiFormat'&&$('#qr-authentication').val()!='none';}"],
			['videoId', 'required', 'when' => function ($model) {
					return $model->type == 'YoutubeFormat';
				}, 'whenClient' => "function(attribute,value){return $('#qr-type').val()=='YoutubeFormat';}"],
		];
	}

	public function attributeLabels(): array {
		return [
			'type' => 'Type of QR Code to generate',
			'size' => 'Size in pixels',
			'url' => 'URL',
			'lat' => 'Latitude',
			'lng' => 'Longitude',
			'dtStart' => 'Start Date',
			'dtEnd' => 'End Date',
			'email' => 'Email Address',
			'nickName' => 'Nickname',
			'msg' => 'Message',
			'ssid' => 'SSID',
			'hidden' => 'SSID is Hidden',
			'videoId' => 'Youtube Video ID',
		];
	}

	public function generateQr(): bool {
		$rndFilename = uniqid('qr');
		$cacheFile = Yii::getAlias("@webroot/assets/temp/qr/{$rndFilename}.png");

		switch ($this->type) {
			case 'BtcFormat'			: $qrData = ['address' => $this->address, 'amount' => $this->amount, 'name' => $this->name, 'message' => $this->msg]; break;
			case 'BookMarkFormat'		: $qrData = ['title' => $this->title, 'url' => $this->url]; break;
			case 'GeoFormat'			: $qrData = ['lat' => $this->lat, 'lng' => $this->lng, 'altitude' => $this->altitude]; break;
			case 'iCalFormat'			: $qrData = ['summary' => $this->summary, 'startTimestamp' => strtotime($this->dtStart), 'endTimestamp' => strtotime($this->dtEnd)]; break;
			case 'MailMessageFormat'	: $qrData = ['email' => $this->email, 'subject' => $this->subject, 'body' => $this->msg]; break;
			case 'MailToFormat'			: $qrData = ['email' => $this->email]; break;
			case 'MeCardFormat'			: $qrData = ['firstName' => $this->firstName,
														'lastName' => $this->lastName,
														'sound' => $this->sound,
														'phone' => $this->phone,
														'videoPhone' => $this->videoPhone,
														'email' => $this->email,
														'note' => $this->note,
														'birthday' => $this->birthday,
														'address' => $this->address,
														'url' => $this->url,
														'nickName' => $this->nickName]; break;
			case 'MmsFormat'			: $qrData = ['phone' => $this->phone, 'msg' => $this->msg]; break;
			case 'PhoneFormat'			:
			case 'SmsFormat'			: $qrData = ['phone' => $this->phone]; break;
			case 'vCardFormat'			: $qrData = ['name' => $this->name, 'fullName' => $this->fullName, 'email' => $this->email]; break;
			case 'WifiFormat'			: $qrData = ['authentication' => $this->authentication === "none" ? null : $this->authentication, 'ssid' => $this->ssid, 'password' => $this->authentication === "none" ? null : $this->password, 'hidden' => $this->hidden]; break;
			case 'YoutubeFormat'		: $qrData = ['videoId' => $this->videoId]; break;
		}

		$class = 'Da\\QrCode\\Format\\' . $this->type;
		$format = new $class($qrData);
		$qrCode = (new QrCode($format))->setSize($this->size)->setMargin($this->margin);
		$qrCode->writeFile($cacheFile);

		Yii::$app->getSession()->setFlash('qr-success', $rndFilename.'.png');
		return true;
	}

	public function getAuthentication(): array {
		foreach (['none', 'WEP', 'WPA'] as $value)
			$list[$value] = $value;
		return $list;
	}

	public function getTypes($rules = false): array {
		$dir = Yii::getAlias('@vendor/2amigos/qrcode-library/src/Format');
		foreach(FileHelper::findFiles($dir, ['except' => ['AbstractFormat.php', 'vCardFormat.php'], 'only' => ['*.php']]) as $file)
			$typeList[basename($file, '.php')] = $rules ? basename($file, '.php') : basename($file, 'Format.php');

		asort($typeList, SORT_NATURAL | SORT_FLAG_CASE);
		return $typeList;
	}

	public function printFormField(ActiveForm $form, $model, $type, $name, $icon, $tab, $class = null, $values = null) {
		if ($type === 'textInput')
			return $form->field($model, $name, [
					'options' => ['class' => $class],
					'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon($icon).'</span>{input}</div>{error}',
				])->textInput(['tabindex' => $tab]);
		elseif ($type === 'otherInput')
			return $form->field($model, $name, [
					'options' => ['class' => !ArrayHelper::isIn($class, ['email', 'number', 'tel', 'url']) ? $class : null],
					'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon($icon).'</span>{input}</div>{error}',
				])->input($class, ['tabindex' => $tab]);
		elseif ($type === 'textArea')
			return $form->field($model, $name, [
					'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon($icon).'</span>{input}</div>{error}',
				])->textArea(['rows' => 6, 'tabindex' => $tab]);
		elseif ($type === 'checkBox')
			return $form->field($model, $name)->checkBox(['tabindex' => $tab]);
		elseif ($type === 'dropDownList')
			return $form->field($model, $name, [
					'options' => ['class' => $class],
					'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon($icon).'</span>{input}</div>{error}',
				])->dropDownList($values, [
					'prompt' => ($name !== 'type' || $model->load(Yii::$app->request->post())) ? null : 'Select a type',
					'tabindex' => $tab,
				]);
		elseif ($type === 'DatePicker')
			return $form->field($model, $name, [
					'options' => ['class' => $class],
					'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon($icon).'</span>{input}</div>{error}',
				])->widget(DatePicker::classname(), [
					'clientOptions' => [
						'changeMonth' => true,
						'changeYear' => true,
						'firstDay' => 1,
						'maxDate' => '-0Y',
						'minDate' => '-110Y',
						'yearRange' => '-110Y:-0Y',
					],
					'dateFormat' => 'yyyy-MM-dd',
					'language' => 'en-GB',
					'options' => ['class' => 'form-control', 'readonly' => true, 'tabindex' => $tab],
				]);
		elseif ($type === 'DateTimePicker')
			return $form->field($model, $name, [
				'options' => ['class' => $class],
				'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon($icon).'</span>{input}</div>{error}',
			])->widget(DateTimePicker::className(), [
				'clientOptions' => [
					'changeMonth' => true,
					'changeYear' => true,
					'firstDay' => 1,
					'timeFormat' => 'HH:mm',
				],
				'dateFormat' => 'yyyy-MM-dd',
				'options' => ['class' => 'form-control', 'readonly' => true, 'tabindex' => $tab],
			]);
	}
}
