<?php
use yii\bootstrap\{ActiveForm, Html};

$this->title = Yii::$app->controller->action->id === 'request'
	? Yii::t('usuario', 'Recover your password')
	: Yii::t('usuario', 'Reset your password');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
	<div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3"><?php
		echo Html::tag('h3', Html::encode($this->title));

		$form = ActiveForm::begin([
			'id' => $model->formName(),
			'enableAjaxValidation' => true,
			'enableClientValidation' => false,
		]);

		echo $form->field($model, 'email', [
			'inputTemplate' => '<div class="input-group"><span class="input-group-addon">'.Html::icon('envelope').'</span>{input}</div>',
		])->textInput(['autofocus' => true, 'tabindex' => 1]);

		echo Html::submitButton(Yii::t('usuario', 'Continue'), ['class' => 'btn btn-primary btn-block', 'autofocus' => 2]);

		ActiveForm::end(); ?>
	</div>
</div>
