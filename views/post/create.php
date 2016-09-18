<?php
use yii\bootstrap\Html;

$this->title = 'Create Article';
$this->params['breadcrumbs'][] = ['label' => 'Articles', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="article-create">
	<h1><?= Html::encode($this->title) ?></h1>

	<?php
	echo $this->render('_formPost', [
		'model' => $model,
	]);
	?>
</div>
