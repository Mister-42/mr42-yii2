<?php
use yii\bootstrap\{ActiveForm, Html};

$this->title = Yii::t('usuario', 'Account settings');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="clearfix"></div>

<?= $this->render('@Da/User/resources/views/shared/_alert', ['module' => Yii::$app->getModule('user')]) ?>

<div class="row">
	<div class="col-md-3">
		<?= $this->render('@Da/User/resources/views/settings/_menu') ?>
	</div>
	<div class="col-md-9"><?php
		echo Html::tag('h3', Html::encode($this->title));

		$form = ActiveForm::begin([
			'enableAjaxValidation' => true,
			'enableClientValidation' => false,
			'fieldConfig' => [
				'template' => '{label}{beginWrapper}{input}{hint}{error}{endWrapper}',
				'horizontalCssClasses' => [
					'error' => '',
					'hint' => '',
					'label' => 'col-lg-3',
					'wrapper' => 'col-lg-9',
				],
			],
			'layout' => 'horizontal',
		]);

		echo $form->field($model, 'email', [
			'inputTemplate' => '<div class="input-group"><span class="input-group-addon">'.Html::icon('envelope').'</span>{input}</div>',
		])->textInput(['tabindex' => 1]);

		echo $form->field($model, 'username', [
			'inputTemplate' => '<div class="input-group"><span class="input-group-addon">'.Html::icon('user').'</span>{input}</div>',
		])->textInput(['tabindex' => 2]);

		echo $form->field($model, 'new_password', [
			'inputTemplate' => '<div class="input-group"><span class="input-group-addon">'.Html::icon('lock').'</span>{input}</div>',
		])->passwordInput(['tabindex' => 3]);

		echo '<hr>';

		echo $form->field($model, 'current_password', [
			'inputTemplate' => '<div class="input-group"><span class="input-group-addon">'.Html::icon('lock').'</span>{input}</div>',
		])->passwordInput(['tabindex' => 4]); ?>

		<div class="form-group">
			<div class="col-lg-offset-3 col-lg-9">
				<?= Html::submitButton(Yii::t('usuario', 'Save'), ['class' => 'btn btn-block btn-success', 'tabindex' => 5]) ?>
				<br>
			</div>
		</div>

		<?php ActiveForm::end(); ?>
	</div>
</div>
