<?php
use yii\bootstrap\{ActiveForm, Html};
use yii\captcha\Captcha;
use yii\web\View;
use yii\widgets\Pjax;

$rules = $model->rules();
$this->registerJs('$(\'#formContent\').on(\'input keyup\',function(){len=$(this).val().length;char='.$rules['charCount']['max'].'-len;if(len>'.$rules['charCount']['max'].'){$(\'#chars\').text(\'You are \'+Math.abs(char)+\' characters over the limit.\').addClass(\'alert-danger\')}else{$(\'#chars\').text(\'You have \'+char+\' characters left\').removeClass(\'alert-danger\');}}).keyup();', View::POS_READY);

Pjax::begin(['enablePushState' => false, 'linkSelector' => 'pjaxtrigger', 'options' => ['class' => 'comment-form']]);
	echo Html::tag('h2', 'Leave a Comment');

	$form = ActiveForm::begin(['id' => 'comment-form', 'options' => ['data-pjax' => '']]);

		if (Yii::$app->user->isGuest) {
			echo '<div class="row">';
				echo $form->field($model, 'name', [
					'options' => ['class' => 'col-xs-6'],
					'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('user').'</span>{input}</div>{error}',
				])->textInput(['tabindex' => 1]);

				echo $form->field($model, 'email', [
					'options' => ['class' => 'col-xs-6'],
					'template' => '{label}<div class="input-group"><span class="input-group-addon"><span class="addon-email"></span></span>{input}</div>{hint}{error}',
				])->input('email', ['tabindex' => 2])
				->hint('This will not be published.');
			echo '</div>';

			echo $form->field($model, 'website', [
				'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('globe').'</span>{input}</div>{error}',
			])->input('url', ['tabindex' => 3]);
		}

		echo $form->field($model, 'title', [
				'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('header').'</span>{input}</div>{error}',
			])->textInput(['tabindex' => 4]);

		echo $form->field($model, 'content', [
				'template' => '{label} <div id="chars" class="pull-right"></div><div class="input-group"><span class="input-group-addon">'.Html::icon('comment').'</span>{input}</div> {hint} {error}'
			])
			->textarea(['id' => 'formContent', 'rows' => 6, 'tabindex' => 5])
			->hint('You may use ' . Html::a('Markdown Syntax', ['/articles/index', 'id' => 4], ['target' => '_blank']) . '. HTML is not allowed.');

		if (Yii::$app->user->isGuest) {
			echo $form->field($model, 'captcha')->widget(Captcha::className(), [
				'imageOptions' => ['alt' => 'CAPTCHA image', 'class' => 'captcha'],
				'options' => ['class' => 'form-control', 'tabindex' => 6],
				'template' => '<div class="row"><div class="col-xs-3"><div class="input-group"><span class="input-group-addon">'.Html::icon('dashboard').'</span>{input}</div></div> {image}</div>',
			])->hint('Click on the image to retrieve a new verification code.');
		} ?>

		<div class="form-group text-right">
			<?= Html::resetButton('Reset', ['class' => 'btn btn-default', 'tabindex' => 8]) ?>
			<?= Html::submitButton('Submit', ['class' => 'btn btn-primary', 'id' => 'pjaxtrigger', 'tabindex' => 7]) ?>
		</div>
	<?php ActiveForm::end(); ?>
<?php Pjax::end(); ?>
