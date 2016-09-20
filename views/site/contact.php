<?php
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\captcha\Captcha;
use yii\web\View;
use yii\widgets\Pjax;

$this->title = 'Contact';
$this->params['breadcrumbs'][] = $this->title;

$rules = $model->rules();
$this->registerJs('$(\'#formContent\').on(\'input keyup\',function(){len=$(this).val().length;char='.$rules['charCount']['max'].'-len;if(len>'.$rules['charCount']['max'].'){$(\'#chars\').text(\'You are \'+Math.abs(char)+\' characters over the limit.\').addClass(\'alert-danger\')}else{$(\'#chars\').text(\'You have \'+char+\' characters left\').removeClass(\'alert-danger\');}}).keyup();', View::POS_READY);
?>
<div class="row">
	<div class="col-md-offset-2 col-md-8">
		<?php echo Html::tag('h1', Html::encode($this->title));

		Pjax::begin(['enablePushState' => false, 'linkSelector' => ':submit']);
			echo Html::tag('p', 'If you have inquiries or other questions, please fill out the following form to contact ' . Yii::$app->name . '. Thank you.');

			$form = ActiveForm::begin(['options' => ['data-pjax' => '']]);

			echo '<div class="row">';
				echo $form->field($model, 'name', [
					'options' => ['class' => 'col-xs-6 form-group'],
					'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('user').'</span>{input}</div>{error}',
				])->textInput(['tabindex' => 1]);

				echo $form->field($model, 'email', [
					'options' => ['class' => 'col-xs-6 form-group'],
					'template' => '{label}<div class="input-group"><span class="input-group-addon"><span class="addon-email"></span></span>{input}</div>{error}',
				])->textInput(['tabindex' => 2]);
			echo '</div>';

			echo $form->field($model, 'title', [
					'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('header').'</span>{input}</div>{error}',
				])->textInput(['tabindex' => 3]);

			echo $form->field($model, 'content',[
				'template' => "{label} <div id=\"chars\" class=\"pull-right\"></div> {input} {hint} {error}",
				'template' => '{label} <div id="chars" class="pull-right"></div><div class="input-group"><span class="input-group-addon">'.Html::icon('comment').'</span>{input}</div> {hint} {error}'
			])->textarea(['id' => 'formContent', 'rows' => 6, 'tabindex' => 4]);

			echo $form->field($model, 'captcha')->widget(Captcha::className(), [
				'captchaAction' => '/tech/captcha',
				'imageOptions' => ['alt' => 'CAPTCHA image', 'class' => 'captcha'],
				'options' => ['class' => 'form-control', 'tabindex' => 5],
				'template' => '<div class="row"><div class="col-xs-4"><div class="input-group"><span class="input-group-addon">'.Html::icon('dashboard').'</span>{input}</div></div> {image}</div>',
			])->hint('Click on the image to retrieve a new verification code.');

			echo '<div class="form-group text-right">';
				echo Html::resetButton('Reset', ['class' => 'btn btn-default', 'tabindex' => 7]) . ' ';
				echo Html::submitButton('Send', ['class' => 'btn btn-primary', 'tabindex' => 6]);
			echo '</div>';

			ActiveForm::end();
		Pjax::end(); ?>
	</div>
</div>
