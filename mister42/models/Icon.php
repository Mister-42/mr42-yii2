<?php

namespace app\models;

use Yii;

class Icon extends \thoulah\fontawesome\IconComponent {
	public function instrumental(): self {
		$this->icon['fontAwesomeFolder'] = '@assetsroot';
		$this->icon['style'] = '';
		$this->icon['name'] = 'images/instrumental';
		$this->icon['title'] = Yii::t('mr42', 'Instrumental');
		return $this;
	}
}
