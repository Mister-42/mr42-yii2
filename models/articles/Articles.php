<?php
namespace app\models\articles;
use app\models\Formatter;
use yii\bootstrap\Html;

class Articles extends BaseArticles {
	public function afterFind() {
		parent::afterFind();
		$this->url = $this->url ?? $this->title;
		$this->content = Formatter::cleanInput($this->content, 'gfm', true);
		$this->sourceName = $this->sourceName ?? 'Source';

		if ($this->sourceUrl) {
			$this->content .= '<div class="clearfix">';
			$this->content .= Html::a($this->sourceName, $this->sourceUrl, ['class' => 'btn btn-default pull-right']);
			$this->content .= '</div>';
		}

	}
}
