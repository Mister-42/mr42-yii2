<?php
namespace app\models;
use Yii;
use kartik\mpdf\Pdf as PdfCreator;
use Mpdf\Pdf\Protection;
use Mpdf\Pdf\Protection\UniqidGenerator;
use Mpdf\Writer\BaseWriter;
use yii\helpers\FileHelper;

class Pdf {
	protected $this;

	public function create(string $filename, string $content, string $updated, array $params): string {
		$filename = Yii::getAlias($filename.'.pdf');
		$created = $params['created'] ?? $updated;
		if (!file_exists($filename) || filemtime($filename) < $updated) :
			FileHelper::createDirectory(dirname($filename));

			$pdf = new PdfCreator();
			$pdf->api->SetCreator(Yii::$app->name);
			$pdf->content = $content;
			$pdf->filename = $filename;
			$pdf->destination = PdfCreator::DEST_FILE;

			foreach (['author', 'footer', 'header', 'keywords', 'subject', 'title'] as $x) :
				if (isset($params[$x])) :
					$function = 'Set'.ucfirst($x);
					$pdf->api->$function($params[$x]);
				endif;
			endforeach;
			$pdf->render();

			$writer = new BaseWriter($pdf->api, new Protection(new UniqidGenerator()));
			$this->replaceLine($filename, '/Producer', $writer->utf16BigEndianTextString('Yii Framework'));
			$this->replaceLine($filename, '/CreationDate', $writer->string(date('YmdHis', $created).substr(date('O', $created), 0, 3)."'".substr(date('O', $created), 3, 2)."'"));
			$this->replaceLine($filename, '/ModDate', $writer->string(date('YmdHis', $updated).substr(date('O', $updated), 0, 3)."'".substr(date('O', $updated), 3, 2)."'"));
			touch($filename, $updated);
		endif;
		return $filename;
	}

	private function replaceLine(string $filename, string $search, string $replace) {
		$reading = fopen($filename, 'r');
		$writing = fopen($filename.'.tmp', 'w');

		while (!feof($reading)) :
			$line = fgets($reading);
			if (stristr($line, $search)) :
				$line = $search.' '.$replace.PHP_EOL;
			endif;
			fputs($writing, $line);
		endwhile;
		fclose($reading); fclose($writing);
		rename($filename.'.tmp', $filename);
	}
}
