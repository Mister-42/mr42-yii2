<?php
namespace app\models;
use Yii;
use kartik\mpdf\Pdf as PdfCreator;
use yii\helpers\FileHelper;

class Pdf
{
	public function create($filename, $content, $updated, $params)
	{
		$filename = Yii::getAlias($filename.'.pdf');
		$created = (isset($params['created'])) ? $params['created'] : $updated;
		if (!file_exists($filename) || filemtime($filename) < $updated) {
			FileHelper::createDirectory(dirname($filename));

			$pdf = new PdfCreator();
			$pdf->api->SetCreator(Yii::$app->name);
			$pdf->content = $content;
			$pdf->filename = $filename;
			$pdf->destination = PdfCreator::DEST_FILE;
			foreach(['author', 'footer', 'header', 'keywords', 'subject', 'title'] as $x) {
				if (isset($params[$x])) {
					$function = 'Set' . ucfirst($x);
					$pdf->api->$function($params[$x]);			
				}
			}
			$pdf->render();
			$this->replaceLine($filename, '/Producer', $pdf->api->_UTF16BEtextstring('Yii Framework'));
			$this->replaceLine($filename, '/CreationDate', $pdf->api->_textstring(date('YmdHis', $created) . substr(date('O', $created), 0, 3) . "'" . substr(date('O', $created), 3, 2) . "'"));
			$this->replaceLine($filename, '/ModDate', $pdf->api->_textstring(date('YmdHis', $updated) . substr(date('O', $updated), 0, 3) . "'" . substr(date('O', $updated), 3, 2) . "'"));
			touch($filename, $updated);
		}
		return $filename;
	}

	public function replaceLine($fileName, $search, $replace)
	{
		$reading = fopen($fileName, 'r');
		$writing = fopen($fileName.'.tmp', 'w');

		while (!feof($reading)) {
			$line = fgets($reading);
			if (stristr($line, $search)) {
				$line = $search . ' ' . $replace . PHP_EOL;
			}
			fputs($writing, $line);
		}
		fclose($reading); fclose($writing);
		rename($fileName.'.tmp', $fileName);
	}
}
