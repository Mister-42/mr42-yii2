<?php
namespace app\models\tools;
use Yii;
use app\models\Mailer;
use app\widgets\TimePicker;
use Mpdf\QrCode\{Output, QrCode};
use yii\bootstrap4\{ActiveForm, Html};
use yii\helpers\{FileHelper, StringHelper};

class Qr extends \yii\base\Model {
	public $type;
	public $size = 150;
	public $recipient;

	public function rules(): array {
		return [
			[['type', 'size'], 'required'],
			['type', 'in', 'range' => static::getTypes(true)],
			['size', 'double', 'min' => 50, 'max' => 1500],
			['recipient', 'email', 'checkDNS' => true, 'enableIDN' => true],
		];
	}

	public function attributeLabels(): array {
		return [
			'type' => Yii::t('mr42', 'Type of QR Code to Generate'),
			'size' => Yii::t('mr42', 'Size in Pixels'),
			'recipient' => Yii::t('mr42', 'Email Address'),
		];
	}

	public function formName(): string {
		return 'qr';
	}

	public static function getBirthdayCalendar(ActiveForm $form, Qr $model, int $tab): string {
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
				'template' => '{label}<div class="input-group">'.Yii::$app->icon->fieldAddon('arrows-alt').'{input}</div>{error}',
			])->input('number', ['tabindex' => ++$tab]).
			$form->field($this, 'recipient', [
				'options' => ['class' => 'form-group col-md-6'],
				'template' => '{label} '.Yii::t('mr42', '(optional)').'<div class="input-group">'.Yii::$app->icon->fieldAddon('at').'{input}</div>{hint} {error}',
			])->input('email', ['tabindex' => ++$tab])
			->hint(Yii::t('mr42', 'If you enter your email address the image will be mailed to that address.'))
		, ['class' => 'row form-group']);

		$footer[] = Html::tag('div',
			Html::submitButton(Yii::t('mr42', 'Generate QR Code'), ['class' => 'btn btn-primary ml-1', 'tabindex' => ++$tab])
		, ['class' => 'btn-toolbar float-right form-group']);

		return implode($footer);
	}

	public function generate($qrData): bool {
		$size = StringHelper::byteLength($qrData);
		if ($size > 2746) :
			Yii::$app->getSession()->setFlash('qr-size', $size - 2746);
			return false;
		endif;

		FileHelper::createDirectory(Yii::getAlias('@assetsroot/temp'));
		$cacheFile = Yii::getAlias('@assetsroot/temp/'.uniqid('qr').'.png');
		$qrCode = new QrCode($qrData);
		$qrCode->disableBorder();
		$output = new Output\Png();
		file_put_contents($cacheFile, $output->output($qrCode, $this->size, [255, 255, 255], [0, 0, 0], 9));

		if ($this->recipient)
			Mailer::sendFileHtml($this->recipient, 'Your QR Code from '.Yii::$app->name, 'qrRequester', ['file' => $cacheFile, 'name' => 'QRcode.png']);

		Yii::$app->getSession()->setFlash('qr-success', $cacheFile);
		return true;
	}

	public static function getWifiAuthentication(bool $rules = false): array {
		return $rules ? ['none', 'wep', 'wpa'] : ['none' => Yii::t('mr42', 'none'), 'wep' => Yii::t('mr42', 'WEP'), 'wpa' => Yii::t('mr42', 'WPA')];
	}

	public static function getTypes(bool $rules = false): array {
		$qrCodes = [
			'Bitcoin' 		=> Yii::t('mr42', 'Bitcoin'),
			'Bookmark' 		=> Yii::t('mr42', 'Bookmark'),
			'EmailMessage'	=> Yii::t('mr42', 'Email Message'),
			'FreeInput'		=> Yii::t('mr42', 'Free Input'),
			'Geographic'	=> Yii::t('mr42', 'Geographic'),
			'Ical'	 		=> Yii::t('mr42', 'iCal'),
			'MailTo' 		=> Yii::t('mr42', 'Mail To'),
			'MeCard' 		=> Yii::t('mr42', 'MeCard'),
			'MMS' 			=> Yii::t('mr42', 'MMS'),
			'Phone' 		=> Yii::t('mr42', 'Phone'),
			'SMS' 			=> Yii::t('mr42', 'SMS'),
			'Vcard' 		=> Yii::t('mr42', 'vCard'),
			'WiFi' 			=> Yii::t('mr42', 'WiFi'),
			'YouTube' 		=> Yii::t('mr42', 'YouTube'),
		];

		$dir = Yii::getAlias('@app/models/tools/qr');
		foreach (FileHelper::findFiles($dir, ['only' => ['*.php']]) as $file)
			$typeList[basename($file, '.php')] = $rules ? basename($file, '.php') : strtr(basename($file, '.php'), $qrCodes);

		natcasesort($typeList);
		return $typeList;
	}

	public function getDataOrOmit(string $label, string $value, string $glue = ''): ?string {
		if ($value)
			return $label.$value.$glue;
		return null;
	}
}
