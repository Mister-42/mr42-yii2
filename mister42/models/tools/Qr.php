<?php
namespace app\models\tools;
use Yii;
use app\models\Mailer;
use Mpdf\QrCode\QrCode;
use yii\bootstrap\{ActiveForm, Html};
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

	public function getFormFooter(ActiveForm $form): string {
		$footer[] = Html::tag('div',
			$form->field($this, 'size', [
				'options' => ['class' => 'col-sm-6'],
				'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('move').'</span>{input}</div>{error}',
			])->input('number', ['tabindex' => 96]) .
			$form->field($this, 'recipient', [
				'options' => ['class' => 'col-sm-6'],
				'template' => '{label} (optional)<div class="input-group"><span class="input-group-addon">'.Html::icon('envelope').'</span>{input}</div>{hint} {error}',
			])->input('email', ['tabindex' => 97])
			->hint('If you enter your email address the ' . Html::tag('span', 'QR Code', ['class' => 'text-nowrap']) . ' will be mailed to that address.')
		, ['class' => 'row']);

		$footer[] = Html::tag('div',
			Html::submitButton('Generate QR Code', ['class' => 'btn btn-primary', 'tabindex' => 98])
		, ['class' => 'form-group field-qr-buttons text-right']);

		return implode($footer);
	}

	public function generate($qrData): bool {
		$size = StringHelper::byteLength($qrData);
		if ($size > 2746) {
			Yii::$app->getSession()->setFlash('qr-size', $size - 2746);
			return false;
		}

		$rndFilename = uniqid('qr');
		$cacheFile = Yii::getAlias("@assetsroot/temp/{$rndFilename}.png");

		$qrcode = new QrCode(utf8_encode($qrData), 'L');
		$qrcode->disableBorder();
		$qrcode->displayPNG($this->size, [255,255,255], [0,0,0], $cacheFile, 6);

		if ($this->recipient)
			Mailer::sendFileHtml($this->recipient, 'Your QR Code from '.Yii::$app->name, 'qrRequester', ['file' => $cacheFile, 'name' => 'QRcode.png']);
		Yii::$app->getSession()->setFlash('qr-success', $rndFilename.'.png');
		return true;
	}

	public function getAuthentication(): array {
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
