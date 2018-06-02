<?php
use yii\helpers\Url;

echo 'User-agent: *'.PHP_EOL;
echo 'Disallow: /*?'.PHP_EOL;
echo 'Allow: /'.PHP_EOL;
echo 'Noarchive: /'.PHP_EOL;
echo PHP_EOL;
echo 'User-agent: ia_archiver'.PHP_EOL;
echo 'Disallow: /'.PHP_EOL;
echo PHP_EOL;
echo 'Host: '.Yii::$app->request->serverName.PHP_EOL;
echo 'Sitemap: '.Url::to(['feed/sitemap'], true).PHP_EOL;
echo 'Sitemap: '.Url::to(['feed/sitemap-articles'], true).PHP_EOL;
echo 'Sitemap: '.Url::to(['feed/sitemap-lyrics'], true);
