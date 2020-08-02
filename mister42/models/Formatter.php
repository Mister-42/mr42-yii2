<?php

namespace mister42\models;

use GK\JavascriptPacker;
use voku\helper\HtmlDomParser;
use Yii;
use yii\helpers\FileHelper;
use yii\helpers\Markdown;

class Formatter extends \yii\i18n\Formatter
{
    public function asTimeDiff($start, $end, $absolute = true): int
    {
        $diff = $this->asTimestamp($start) - $this->asTimestamp($end);
        return $absolute ? abs($diff) : $diff;
    }

    public function cleanInput(string $data, string $markdown = 'original', bool $allowHtml = false): string
    {
        $data = $allowHtml ? Yii::$app->formatter->asRaw(trim($data)) : Yii::$app->formatter->asHtml(trim($data), ['HTML.Allowed' => '']);
        $data = preg_replace_callback_array([
            '/(vimeo):(()?[[:digit:]]+):(21by9|16by9|4by3|1by1)/U' => [$this, 'getVideo'],
            '/(youtube):((OL|PL){0,1}?[[:ascii:]]+):(21by9|16by9|4by3|1by1)/U' => [$this, 'getVideo'],
        ], $data);
        if ($markdown) {
            $data = Markdown::process($data, $markdown);
        }
        if (Yii::$app->request->isConsoleRequest || Yii::$app->controller->id !== 'feed') {
            $dom = HtmlDomParser::str_get_html($data);
            foreach ($dom->find('img') as $img) {
                $img->setAttribute('class', implode(' ', array_filter([$img->getAttribute('class'), 'img-fluid'])));
                $img->removeAttribute('width');
                $img->removeAttribute('height');
            }
            $data = $dom->html();
        }

        return trim($data);
    }

    public function jspack(string $file): string
    {
        if (!file_exists($fileName = Yii::getAlias("@app/assets/js/{$file}"))) {
            return "{$file} does not exist.";
        }

        $cacheFile = Yii::getAlias("@runtime/assets/js/{$file}");
        if (!file_exists($cacheFile) || filemtime($cacheFile) < filemtime($fileName)) {
            FileHelper::createDirectory(Yii::getAlias('@runtime/assets/js'));
            $jp = new JavascriptPacker(file_get_contents($fileName), 0);
            FileHelper::createDirectory(dirname($cacheFile));
            file_put_contents($cacheFile, $jp->pack());
            touch($cacheFile, filemtime($fileName));
        }

        return file_get_contents($cacheFile);
    }

    private function getVideo(array $match): string
    {
        return Video::getEmbed($match[1], $match[2], $match[4], (bool) ($match[3]));
    }
}
