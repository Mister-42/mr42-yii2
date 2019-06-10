<?php
namespace app\models\tools;
use Yii;
use app\models\Mailer;
use Mpdf\Barcode as BarcodeData;
use yii\bootstrap4\Html;
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
			'type' => Yii::t('mr42', 'Type of Barcode to Generate'),
			'code' => Yii::t('mr42', 'Code'),
			'height' => Yii::t('mr42', 'Height in Pixels'),
			'barWidth' => Yii::t('mr42', 'Bar Width'),
			'recipient' => Yii::t('mr42', 'Email Address'),
		];
	}

	public function generate(): bool {
		$barcode = new BarcodeData();
		$data = $barcode->getBarcodeArray($this->code, $this->type);

		$image = imagecreate($data['maxw'] * $this->barWidth, $this->height);
		imagefill($image, 0, 0, imagecolorallocate($image, 255, 255, 255));
		$fgCol = imagecolorallocate($image, 0, 0, 0);

		$location = 0;
		foreach ($data['bcode'] as $value) :
			$barWidth = (int) round($value['w'] * $this->barWidth);
			$barHeight = (int) round($value['h'] * $this->height / $data['maxh']);
			if ($value['t']) :
				$top = (int) round($value['p'] * $this->height / $data['maxh']);
				imagefilledrectangle($image, $location, $top, $location + $barWidth - 1, $top + $barHeight - 1, $fgCol);
			endif;
			$location += $barWidth;
		endforeach;

		FileHelper::createDirectory(Yii::getAlias('@assetsroot/temp'));
		$cacheFile = Yii::getAlias('@assetsroot/temp/'.uniqid('barcode').'.png');
		imagepng($image, $cacheFile);
		imagedestroy($image);

		if ($this->recipient)
			Mailer::sendFileHtml($this->recipient, 'Your barcode from '.Yii::$app->name, 'barcodeRequester', ['file' => $cacheFile, 'name' => 'Barcode.png']);

		Yii::$app->getSession()->setFlash('barcode-success', $cacheFile);
		return true;
	}

	public static function getTypes(bool $rules = false): array {
		$barcodes = [
			'ISBN / ISSN / EAN 13'													=> 'EAN13',
			'UPC-A'																	=> 'UPCA',
			'UPC-E'																	=> 'UPCE',
			'2-Digits UPC-Based Extention'											=> 'EAN2',
			'5-Digits UPC-Based Extention'											=> 'EAN5',
			'EAN 8'																	=> 'EAN8',
			'IMB - Intelligent Mail Barcode - Onecode - USPS-B-3200'				=> 'IMB',
			'RM4SCC (Royal Mail 4-state Customer Code) - CBC (Customer Bar Code)'	=> 'RM4SCC',
			'KIX (Klant index - Customer index)'									=> 'KIX',
			'POSTNET'																=> 'POSTNET',
			'PLANET'																=> 'PLANET',
			'CODE 93 - USS-93'														=> 'C93',
			'CODE 11'																=> 'CODE11',
			'MSI (Variation of Plessey code)'										=> 'MSI',
			'MSI with Checksum (modulo 11)'											=> 'MSI+',
			'CODABAR'																=> 'CODABAR',
			'CODE 128 A'															=> 'C128A',
			'CODE 128 B'															=> 'C128B',
			'CODE 128 C'															=> 'C128C',
			'EAN 128 A'																=> 'EAN128A',
			'EAN 128 B'																=> 'EAN128B',
			'EAN 128 C'																=> 'EAN128C',
			'CODE 39 - ANSI MH10.8M-1983 - USD-3 - 3 of 9.'							=> 'C39',
			'CODE 39 with Checksum'													=> 'C39+',
			'CODE 39 Extended'														=> 'C39E',
			'CODE 39 Extended with Checksum'										=> 'C39E+',
			'Standard 2 of 5'														=> 'S25',
			'Standard 2 of 5 with Checksum'											=> 'S25+',
			'Interleaved 2 of 5'													=> 'I25',
			'Interleaved 2 of 5 with Checksum'										=> 'I25+',
			'Interleaved 2 of 5 with Bearer Bars'									=> 'I25B',
			'Interleaved 2 of 5 with Bearer Bars and Checksum'						=> 'I25B+',
		];

		foreach ($barcodes as $name => $value)
			$list[$value] = $rules ? $value : $name;
		return $list;
	}
}
