<?php
use yii\bootstrap\{ActiveForm, Html};
use yii\captcha\Captcha;
use yii\web\View;
use yii\widgets\Pjax;

$this->title = 'Contact';
$this->params['breadcrumbs'][] = $this->title;

$rules = $model->rules();
$this->registerJs(Yii::$app->formatter->jspack('formCharCounter.js', ['%max%' => $rules['charCount']['max']]), View::POS_READY);
$this->registerJs(Yii::$app->formatter->jspack('inputFile.js'), View::POS_READY);
?>
<div class="row">
	<div class="col-md-offset-2 col-md-8"><?php
		echo Html::tag('h1', Html::encode($this->title));

		Pjax::begin(['enablePushState' => false, 'linkSelector' => 'pjaxtrigger']);
			echo Html::tag('p', 'If you have inquiries or other questions, please fill out the following form to contact ' . Yii::$app->name . '. Thank you.');

			$form = ActiveForm::begin(['options' => ['data-pjax' => '']]);

			echo '<div class="row">';
				echo $form->field($model, 'name', [
					'options' => ['class' => 'col-xs-6 form-group'],
					'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('user').'</span>{input}</div>{error}',
				])->textInput(['tabindex' => 1]);

				echo $form->field($model, 'email', [
					'options' => ['class' => 'col-xs-6 form-group'],
					'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('envelope').'</span>{input}</div>{error}',
				])->input('email', ['tabindex' => 2]);
			echo '</div>';

			echo $form->field($model, 'title', [
					'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('header').'</span>{input}</div>{error}',
				])->textInput(['tabindex' => 3]);

			echo $form->field($model, 'content',[
				'template' => '{label} <div id="chars" class="pull-right"></div><div class="input-group"><span class="input-group-addon">'.Html::icon('comment').'</span>{input}</div> {hint} {error}',
			])->textarea(['id' => 'formContent', 'rows' => 6, 'tabindex' => 4]); ?>

			<label class="control-label" for="file">Attachment</label>
			<div class="input-group">
				<?= Html::tag('span', Html::icon('file'), ['class' => 'input-group-addon']) ?>
				<input type="text" id="file" class="form-control" placeholder="No file selected" onclick="$('input[id=sourceFile]').click()" readonly>
				<span class="input-group-btn">
					<button type="button" class="btn btn-primary" onclick="$('input[id=sourceFile]').click()" tabindex="5"><?= Html::icon('folder-open') ?></button>
				</span>
			</div>

			<?php
			echo $form->field($model, 'attachment')
				->fileInput(['class' => 'hidden', 'id' => 'sourceFile'])
				->label(false);

			echo $form->field($model, 'captcha')->widget(Captcha::class, [
				'imageOptions' => ['alt' => 'CAPTCHA image', 'class' => 'captcha'],
				'options' => ['class' => 'form-control', 'tabindex' => 6],
				'template' => '<div class="row"><div class="col-xs-4"><div class="input-group"><span class="input-group-addon">'.Html::icon('dashboard').'</span>{input}</div></div> {image}</div>',
			])->hint('Click on the image to retrieve a new verification code.');

			echo Html::tag('div',
				Html::resetButton('Reset', ['class' => 'btn btn-default', 'tabindex' => 8]) . ' ' .
				Html::submitButton('Send', ['class' => 'btn btn-primary', 'id' => 'pjaxtrigger', 'tabindex' => 7])
			, ['class' => 'form-group text-right']);

			ActiveForm::end();
		Pjax::end();
	?></div>
</div>
