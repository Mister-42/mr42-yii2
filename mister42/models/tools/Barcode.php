<?php
namespace app\models\tools;
use Yii;
use app\models\Mailer;
use Mpdf\Barcode as BarcodeData;
use yii\bootstrap4\{ActiveForm, Html};
use yii\helpers\FileHelper;

class Barcode extends \yii\base\Model {
	public $type;
	public $code;
	public $height = 150;
	public $barWidth = 2;
	public $recipient;

	public function rules(): array {
		return [
			[['type', 'code', 'height', 'barWidth'], 'required'],
			['type', 'in', 'range' => self::getTypes(true)],
			['code', 'string', 'max' => 25],
			['code', 'double'],
			['height', 'double', 'min' => 50, 'max' => 750],
			['barWidth', 'double', 'min' => 1, 'max' => 5],
			['recipient', 'email', 'checkDNS' => true, 'enableIDN' => true],
		];
	}

	public function attributeLabels(): array {
		return [
			'type' => 'Type of Barcode to generate',
			'height' => 'Height in Pixels',
			'recipient' => 'Email Address'
		];
	}

	public function generate(): bool {
		$barcode = new BarcodeData();
		$data = $barcode->getBarcodeArray($this->code, $this->type);

		$image = imagecreate($data['maxw'] * $this->barWidth, $this->height);
		imagefill($image, 0, 0, imagecolorallocate($image, 255, 255, 255));
		$fgCol = imagecolorallocate($image, 0, 0, 0);

		foreach ($data['bcode'] as $value) :
			$barWidth = round($value['w'] * $this->barWidth, 3);
			$barHeight = round($value['h'] * $this->height / $data['maxh'], 3);
			if ($value['t']) {
				$top = round($value['p'] * $this->height / $data['maxh'], 3);
				imagefilledrectangle($image, $location, $top, $location + $barWidth - 1, $top + $barHeight - 1, $fgCol);
			}
			$location += $barWidth;
		endforeach;

		FileHelper::createDirectory(Yii::getAlias('@assetsroot/temp'));
		$cacheFile = Yii::getAlias('@assetsroot/temp/'.uniqid('barcode').'.png');
		imagepng($image, $cacheFile);
		imagedestroy($image);

		if ($this->recipient) {
					Mailer::sendFileHtml($this->recipient, 'Your barcode from '.Yii::$app->name, 'barcodeRequester', ['file' => $cacheFile, 'name' => 'Barcode.png']);
		}
		Yii::$app->getSession()->setFlash('barcode-success', $cacheFile);
		return true;
	}

	public function getTypes(bool $rules = false): array {
		foreach (require(Yii::getAlias('@app/data/barcodeTypes.php')) as $name => $value) {
					$list[$value] = $rules ? $value : $name;
		}
		return $list;
	}
}
