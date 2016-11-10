<?php
namespace app\models\articles;
use Yii;
use yii\bootstrap\Html;

class Articles extends BaseArticles {
	public function afterFind() {
		parent::afterFind();
		$this->url = $this->url ?? $this->title;
		$this->content = Yii::$app->formatter->cleanInput($this->content, 'gfm', true);

		if ($this->source) {
			$this->content .= '<div class="clearfix">';
			$this->content .= Html::a('Source', $this->source, ['class' => 'btn btn-default pull-right']);
			$this->content .= '</div>';
		}

	}
}
