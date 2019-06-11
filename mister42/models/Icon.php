<?php
namespace app\models;
use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;

class Icon extends \thoulah\fontawesome\Icon {
	public function instrumental(array $options = []): string {
		$svg = $this->loadSvg('@assetsroot/images/instrumental.svg');
		ArrayHelper::setValue($options, 'title', 'Instrumental');
		return $this->processSvg($svg, $options);
	}
}
