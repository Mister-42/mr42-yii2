<?php
namespace app\models;
use Yii;
use Mpdf\{Mpdf, HTMLParserMode};
use Mpdf\Pdf\Protection;
use Mpdf\Pdf\Protection\UniqidGenerator;
use Mpdf\Writer\BaseWriter;
use yii\helpers\FileHelper;

class Pdf {
	public function create(string $filename, string $content, int $updated, array $params): string {
		$filename = Yii::getAlias($filename.'.pdf');
		$created = $params['created'] ?? $updated;

		if (!file_exists($filename) || filemtime($filename) < $updated) :
			FileHelper::createDirectory(dirname($filename));

			$pdf = new Mpdf();
			$pdf->SetCreator(Yii::$app->name);

			foreach (['author', 'footer', 'header', 'keywords', 'subject', 'title'] as $x) :
				if (isset($params[$x])) :
					$function = 'Set'.ucfirst($x);
					$pdf->$function($params[$x]);
				endif;
			endforeach;

			$cssFile = Yii::getAlias('@runtime/assets/css/site.css');
			$pdf->WriteHTML(file_get_contents($cssFile), HTMLParserMode::HEADER_CSS);
			$pdf->WriteHTML($content, HTMLParserMode::HTML_BODY);
			$pdf->Output($filename, \Mpdf\Output\Destination::FILE);

			$writer = new BaseWriter($pdf, new Protection(new UniqidGenerator()));
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
			if (stristr($line, $search))
				$line = $search.' '.$replace.PHP_EOL;
			fputs($writing, $line);
		endwhile;
		fclose($reading); fclose($writing);
		rename($filename.'.tmp', $filename);
	}
}
