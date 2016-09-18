<?php
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

$this->title = 'Account Settings';
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('/_alert', ['module' => Yii::$app->getModule('user')]) ?>

<div class="row">
	<div class="col-md-offset-2 col-md-8">
		<?= Html::tag('h1', Html::encode($this->title)) ?>

		<?php $form = ActiveForm::begin([
			'id'          => 'account-form',
			'options'     => ['class' => 'form-horizontal'],
			'fieldConfig' => [
				'template'     => "{label}\n<div class=\"col-lg-9\">{input}</div>\n<div class=\"col-sm-offset-3 col-lg-9\">{error}\n{hint}</div>",
				'labelOptions' => ['class' => 'col-lg-3 control-label'],
			],
			'enableAjaxValidation'   => true,
			'enableClientValidation' => false,
		]); ?>

		<?= $form->field($model, 'email', [
			'template' => '{label}<div class="input-group"><span class="input-group-addon"><span class="addon-email"></span></span>{input}</div>{error}',
		]) ?>

		<?= $form->field($model, 'username', [
			'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('user').'</span>{input}</div>{error}',
		]) ?>

		<?= $form->field($model, 'new_password', [
			'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('lock').'</span>{input}</div>{error}',
		])->passwordInput() ?>

		<hr />

		<?= $form->field($model, 'current_password', [
			'template' => '{label}<div class="input-group"><span class="input-group-addon">'.Html::icon('lock').'</span>{input}</div>{error}',
		])->passwordInput() ?>

		<div class="form-group">
			<div class="text-right">
				<?= Html::resetButton('Reset', ['class' => 'btn btn-default', 'tabindex' => 6]) ?>
				<?= Html::submitButton('Save', ['class' => 'btn btn-primary', 'tabindex' => 5]) ?><br>
			</div>
		</div>

		<?php ActiveForm::end(); ?>

    </div>
</div>
