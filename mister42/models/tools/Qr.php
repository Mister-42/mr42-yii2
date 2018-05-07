<?php
namespace app\models\tools;
use Yii;
use app\models\{Icon, Mailer};
use app\widgets\TimePicker;
use Mpdf\QrCode\QrCode;
use yii\bootstrap4\{ActiveForm, Html};
use yii\helpers\{FileHelper, StringHelper};

class Qr extends \yii\base\Model {
	public $type;
	public $size = 150;
	public $recipient;

	public function rules(): array {
		return [
			[['type', 'size'], 'required'],
			['type', 'in', 'range' => self::getTypes(true)],
			['size', 'double', 'min' => 50, 'max' => 1500],
			['recipient', 'email', 'checkDNS' => true, 'enableIDN' => true],
		];
	}

	public function attributeLabels(): array {
		return [
			'type' => 'Type of QR Code to generate',
			'size' => 'Size in pixels',
			'recipient' => 'Email Address'
		];
	}

	public function formName(): string {
		return 'qr';
	}

	public function getBirthdayCalendar(ActiveForm $form, Qr $model, int $tab): string {
		return $form->field($model, 'birthday')->widget(TimePicker::class, [
				'clientOptions' => [
					'changeMonth' => true,
					'changeYear' => true,
					'dateFormat' => 'yy-mm-dd',
					'firstDay' => 1,
					'maxDate' => '-0Y',
					'minDate' => '-110Y',
					'yearRange' => '-110Y:-0Y',
				],
				'mode' => 'date',
				'options' => ['class' => 'form-control', 'readonly' => true, 'tabindex' => $tab],
			]);
	}

	public function getFormFooter(ActiveForm $form, int $tab): string {
		$footer[] = Html::tag('div',
			$form->field($this, 'size', [
				'options' => ['class' => 'form-group col-md-6'],
				'template' => '{label}<div class="input-group">'.Icon::fieldAddon('arrows-alt').'{input}</div>{error}',
			])->input('number', ['tabindex' => ++$tab]) .
			$form->field($this, 'recipient', [
				'options' => ['class' => 'form-group col-md-6'],
				'template' => '{label} (optional)<div class="input-group">'.Icon::fieldAddon('at').'{input}</div>{hint} {error}',
			])->input('email', ['tabindex' => ++$tab])
			->hint('If you enter your email address the ' . Html::tag('span', 'QR Code', ['class' => 'text-nowrap']) . ' will be mailed to that address.')
		, ['class' => 'row form-group']);

		$footer[] = Html::tag('div',
			Html::submitButton('Generate QR Code', ['class' => 'btn btn-primary ml-1', 'tabindex' => ++$tab])
		, ['class' => 'btn-toolbar float-right form-group']);

		return implode($footer);
	}

	public function generate($qrData): bool {
		if (!file_exists(Yii::getAlias('@assetsroot/temp')))
			FileHelper::createDirectory(Yii::getAlias('@assetsroot/temp'));

		$size = StringHelper::byteLength($qrData);
		if ($size > 2746) {
			Yii::$app->getSession()->setFlash('qr-size', $size - 2746);
			return false;
		}

		$cacheFile = Yii::getAlias('@assetsroot/temp/' . uniqid('qr') . '.png');
		$qrcode = new QrCode(utf8_encode($qrData), 'L');
		$qrcode->disableBorder();
		$qrcode->displayPNG($this->size, [255,255,255], [0,0,0], $cacheFile, 6);

		if ($this->recipient)
			Mailer::sendFileHtml($this->recipient, 'Your QR Code from ' . Yii::$app->name, 'qrRequester', ['file' => $cacheFile, 'name' => 'QRcode.png']);
		Yii::$app->getSession()->setFlash('qr-success', $cacheFile);
		return true;
	}

	public function getWifiAuthentication(): array {
		foreach (['none', 'WEP', 'WPA'] as $value)
			$list[$value] = $value;
		return $list;
	}

	public function getTypes(bool $rules = false): array {
		$dir = Yii::getAlias('@app/models/tools/qr');
		$rename = ['FreeInput' => 'Free Input', 'EmailMessage' => 'Email Message', 'Ical' => 'iCal', 'MailTo' => 'Mail To', 'Vcard' => 'vCard'];
		foreach(FileHelper::findFiles($dir, ['only' => ['*.php']]) as $file)
			$typeList[basename($file, '.php')] = $rules ? basename($file, '.php') : strtr(basename($file, '.php'), $rename);

		asort($typeList, SORT_NATURAL | SORT_FLAG_CASE);
		return $typeList;
	}

	public function getDataOrOmit(string $label, string $value, string $glue = '') {
		if ($value)
			return $label . $value . $glue;
		return false;
	}
}
