<?php
return mister42\cs\Config::create()
	->setCacheFile(__DIR__ . '/../../.cache/yii/mister42/php_cs.cache')
	->setFinder(
		PhpCsFixer\Finder::create()
			->in(__DIR__)
);
