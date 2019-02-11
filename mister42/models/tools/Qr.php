<?php
namespace app\models\tools;
use Yii;
use app\models\Mailer;
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
		$qrcode = new QrCode(utf8_encode($qrData), 'L');
		$qrcode->disableBorder();
		$qrcode->displayPNG($this->size, [255, 255, 255], [0, 0, 0], $cacheFile, 6);

		if ($this->recipient)
			Mailer::sendFileHtml($this->recipient, 'Your QR Code from '.Yii::$app->name, 'qrRequester', ['file' => $cacheFile, 'name' => 'QRcode.png']);

		Yii::$app->getSession()->setFlash('qr-success', $cacheFile);
		return true;
	}

	public static function getWifiAuthentication(bool $rules = false): array {
		return $rules ? ['none', 'wep', 'wpa'] : ['none' => Yii::t('mr42', 'none'), 'wep' => Yii::t('mr42', 'WEP'), 'wpa' => Yii::t('mr42', 'WPA')];
	}

	public static function getTypes(bool $rules = false): array {
		$dir = Yii::getAlias('@app/models/tools/qr');
		$rename = require(Yii::getAlias('@app/data/qrTypes.php'));
		foreach (FileHelper::findFiles($dir, ['only' => ['*.php']]) as $file)
			$typeList[basename($file, '.php')] = $rules ? basename($file, '.php') : strtr(basename($file, '.php'), $rename);

		natcasesort($typeList);
		return $typeList;
	}

	public function getDataOrOmit(string $label, string $value, string $glue = '') {
		if ($value)
			return $label.$value.$glue;
		return false;
	}
}
