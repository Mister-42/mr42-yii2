<?php
use app\models\Icon;
use yii\bootstrap4\{ActiveForm, Html};
use yii\captcha\Captcha;
use yii\web\View;
use yii\widgets\Pjax;

$this->title = 'Contact';
$this->params['breadcrumbs'][] = $this->title;

$rules = $model->rules();
$this->registerJs(Yii::$app->formatter->jspack('formCharCounter.js', ['%max%' => $rules['charCount']['max']]), View::POS_READY);
$this->registerJs(Yii::$app->formatter->jspack('inputFile.js'), View::POS_READY);

echo Html::beginTag('div', ['class' => 'row']);
	echo Html::beginTag('div', ['class' => 'col-md-12 col-lg-8 mx-auto']);
		echo Html::tag('h1', $this->title);

		Pjax::begin(['enablePushState' => false, 'linkSelector' => 'pjaxtrigger']);
			echo Html::tag('div', 'If you have inquiries or other questions, please fill out the following form to contact ' . Yii::$app->name . '. Thank you.', ['class' => 'alert alert-info']);

			$form = ActiveForm::begin(['options' => ['data-pjax' => '']]);

			echo '<div class="row">';
				echo $form->field($model, 'name', [
					'options' => ['class' => 'col-md-6 form-group'],
					'template' => '{label}<div class="input-group">'.Icon::fieldAddon('user').'{input}</div>{error}',
				])->textInput(['tabindex' => ++$tab]);

				echo $form->field($model, 'email', [
					'options' => ['class' => 'col-md-6 form-group'],
					'template' => '{label}<div class="input-group">'.Icon::fieldAddon('at').'{input}</div>{error}',
				])->input('email', ['tabindex' => ++$tab]);
			echo '</div>';

			echo $form->field($model, 'title', [
					'template' => '{label}<div class="input-group">'.Icon::fieldAddon('heading').'{input}</div>{error}',
				])->textInput(['tabindex' => ++$tab]);

			echo $form->field($model, 'content',[
				'template' => '{label} <div id="chars" class="float-right"></div><div class="input-group">'.Icon::fieldAddon('comment').'{input}</div> {hint} {error}',
			])->textarea(['id' => 'formContent', 'rows' => 6, 'tabindex' => ++$tab]); ?>

			<label class="control-label" for="file">Attachment</label>
			<div class="input-group">
				<?= Icon::fieldAddon('paperclip') ?>
				<input type="text" id="file" class="form-control" placeholder="No file selected" onclick="$('input[id=sourceFile]').click()" readonly>
				<span class="input-group-append">
					<button type="button" class="btn btn-primary" onclick="$('input[id=sourceFile]').click()" tabindex="<?= ++$tab ?>"><?= Icon::show('folder-open') ?></button>
				</span>
			</div>

			<?php
			echo $form->field($model, 'attachment')
				->fileInput(['class' => 'd-none', 'id' => 'sourceFile'])
				->label(false);

			echo $form->field($model, 'captcha')->widget(Captcha::class, [
				'imageOptions' => ['alt' => 'CAPTCHA image', 'class' => 'captcha'],
				'options' => ['class' => 'form-control', 'tabindex' => ++$tab],
				'template' => '<div class="row"><div class="col-6 col-md-4"><div class="input-group">'.Icon::fieldAddon('question').'{input}</div></div> {image}</div>',
			])->hint('Click on the image to retrieve a new verification code.');

			echo Html::tag('div',
				Html::resetButton('Reset', ['class' => 'btn btn-default ml-1', 'tabindex' => $tab+2]) . ' ' .
				Html::submitButton('Send', ['class' => 'btn btn-primary ml-1', 'id' => 'pjaxtrigger', 'tabindex' => ++$tab])
			, ['class' => 'btn-toolbar float-right form-group']);

			ActiveForm::end();
		Pjax::end();
	echo Html::endTag('div');
echo Html::endTag('div');
