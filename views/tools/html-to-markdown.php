<?php
use app\assets\Html2MarkdownAsset;
use yii\bootstrap\Html;

$this->title = 'HTML to Markdown Converter';
$this->params['breadcrumbs'][] = 'Tools';
$this->params['breadcrumbs'][] = $this->title;

Html2MarkdownAsset::register($this);
?>
<div class="row">
	<div class="col-md-12"><?= Html::tag('h1', Html::encode($this->title)); ?></div>
</div>

<form class="html2markdown">
	<div class="row">
		<div class="col-md-6">
			<?= Html::tag('h3', 'HTML') ?>
			<textarea id="input"><?= $lastPost->content ?></textarea>
		</div>

		<div class="col-md-6">
			<?= Html::tag('h3', 'Markdown') ?>
			<textarea id="output" readonly>JavaScript is disabled in your web browser. This tool does not work without JavaScript.</textarea>
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<label><input type="checkbox" id="gfm"> GitHub Flavored Markdown</label>
		</div>
	</div>
</form>
