<?php

namespace mister42\models;

use Mpdf\HTMLParserMode;
use Mpdf\Mpdf;
use Mpdf\Pdf\Protection;
use Mpdf\Pdf\Protection\UniqidGenerator;
use Mpdf\Writer\BaseWriter;
use Yii;
use yii\helpers\FileHelper;

class Pdf
{
    public function create(string $filename, string $content, int $updated, array $params): string
    {
        $filename = Yii::getAlias($filename . '.pdf');
        $created = Yii::$app->formatter->asTimestamp($params['created'] ?? $updated);

        if (!file_exists($filename) || filemtime($filename) < $updated) {
            FileHelper::createDirectory(dirname($filename));

            $pdf = new Mpdf();
            $pdf->SetCreator(Yii::$app->name);

            foreach (['author', 'footer', 'header', 'keywords', 'subject', 'title'] as $x) {
                if (isset($params[$x])) {
                    $function = 'Set' . ucfirst($x);
                    $pdf->$function($params[$x]);
                }
            }

            $cssFile = Yii::getAlias('@runtime/assets/css/site.css');
            $pdf->WriteHTML(file_get_contents($cssFile), HTMLParserMode::HEADER_CSS);
            $pdf->WriteHTML('body{font-size:.8rem}', HTMLParserMode::HEADER_CSS);
            $pdf->WriteHTML($content, HTMLParserMode::HTML_BODY);
            $pdf->Output($filename, \Mpdf\Output\Destination::FILE);

            $writer = new BaseWriter($pdf, new Protection(new UniqidGenerator()));
            $this->replaceLine($filename, '/Producer', $writer->utf16BigEndianTextString('Yii Framework'));
            $this->replaceLine($filename, '/CreationDate', $writer->string(date('YmdHis', $created) . mb_substr(date('O', $created), 0, 3) . "'" . mb_substr(date('O', $created), 3, 2) . "'"));
            $this->replaceLine($filename, '/ModDate', $writer->string(date('YmdHis', $updated) . mb_substr(date('O', $updated), 0, 3) . "'" . mb_substr(date('O', $updated), 3, 2) . "'"));
            touch($filename, $updated);
        }
        return $filename;
    }

    private function replaceLine(string $filename, string $search, string $replace): void
    {
        $reading = fopen($filename, 'r');
        $writing = fopen($filename . '.tmp', 'w');

        while (!feof($reading)) {
            $line = fgets($reading);
            if (mb_stristr($line, $search)) {
                $line = $search . ' ' . $replace . PHP_EOL;
            }
            fwrite($writing, $line);
        }
        fclose($reading);
        fclose($writing);
        rename($filename . '.tmp', $filename);
    }
}
