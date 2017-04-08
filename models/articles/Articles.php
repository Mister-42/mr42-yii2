<?php
namespace app\models\articles;
use Yii;
use yii\bootstrap\Html;

class Articles extends BaseArticles {
	public function afterFind() {
		parent::afterFind();
		$this->url = $this->url ?? $this->title;
		if (!empty($this->content)) $this->content = Yii::$app->formatter->cleanInput($this->content, 'gfm', true);

		if ($this->source) {
			$link = Html::a('Source', $this->source, ['class' => 'btn btn-default pull-right']);
			$this->content .= Html::tag('div', $link, ['class' => 'clearfix']);
		}
	}
}
