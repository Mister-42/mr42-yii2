<?php
$doc = new XMLWriter();
$doc->openMemory();
$doc->startDocument('1.0');
$doc->startElement('users');
$doc->writeElement('user', Yii::$app->params['secrets']['bing']['SiteAuth']);
$doc->endElement();
$doc->endDocument();
echo $doc->outputMemory();
