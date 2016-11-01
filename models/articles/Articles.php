<?php
namespace app\models\articles;
use app\models\Formatter;

class Articles extends BaseArticles {
	public function afterFind() {
		parent::afterFind();
		$this->url = $this->url ?? $this->title;
		$this->content = Formatter::cleanInput($this->content, 'gfm', true);
	}
}
