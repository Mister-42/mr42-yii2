<?php

use Da\User\Widget\ReCaptchaWidget;
use mister42\models\ActiveForm;
use yii\bootstrap4\Html;

$this->title = Yii::t('usuario', 'Sign up');
$this->params['breadcrumbs'][] = $this->title;

echo Html::beginTag('div', ['class' => 'row']);
    echo Html::beginTag('div', ['class' => 'col-sm-12 col-md-6 mx-auto']);
        echo Html::tag('h3', $this->title);

        $form = ActiveForm::begin([
            'id' => $model->formName(),
            'enableAjaxValidation' => false,
            'enableClientValidation' => true,
        ]);
        $tab = 0;

        echo $form->field($model, 'email', [
            'icon' => 'at',
        ])->input('email', ['tabindex' => ++$tab]);

        echo Html::beginTag('div', ['class' => 'row']);
            echo $form->field($model, 'username', [
                'icon' => 'user',
                'options' => ['class' => 'col-6 form-group'],
            ])->textInput(['tabindex' => ++$tab]);

            if ($module->generatePasswords === false) {
                echo $form->field($model, 'password', [
                    'icon' => 'lock',
                    'options' => ['class' => 'col-6 form-group'],
                ])->passwordInput(['tabindex' => ++$tab]);
            }
        echo Html::endTag('div');

        echo $form->field($model, 'captcha')->widget(ReCaptchaWidget::class)->label(false);

        echo Html::submitButton(Yii::t('usuario', 'Sign up'), ['class' => 'btn btn-success btn-block', 'tabindex' => ++$tab]);

        ActiveForm::end();

        echo Html::tag('p', Html::a(Yii::t('usuario', 'Already registered? Sign in!'), ['/user/security/login']), ['class' => 'text-center']);
    echo Html::endTag('div');
echo Html::endTag('div');
