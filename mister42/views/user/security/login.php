<?php

use mister42\models\ActiveForm;
use Da\User\Widget\ConnectWidget;
use yii\bootstrap4\Html;

$this->title = Yii::t('usuario', 'Sign in');
$this->params['breadcrumbs'][] = $this->title;

echo $this->render('@Da/User/resources/views/shared/_alert', ['module' => Yii::$app->getModule('user')]);

echo Html::beginTag('div', ['class' => 'row']);
    echo Html::beginTag('div', ['class' => 'col-md-12 col-lg-6 mx-auto']);
        echo Html::tag('h3', $this->title);

        $form = ActiveForm::begin([
            'id' => $model->formName(),
            'enableAjaxValidation' => true,
            'enableClientValidation' => false,
            'validateOnBlur' => false,
            'validateOnType' => false,
            'validateOnChange' => false,
        ]);
        $tab = 0;

        echo $form->field($model, 'login', [
            'icon' => 'user',
        ])->textInput(['autofocus' => true, 'tabindex' => ++$tab]);

        echo $form->field($model, 'password', [
            'icon' => 'lock',
        ])->passwordInput(['tabindex' => ++$tab])
        ->label(Yii::t('usuario', 'Password') . ($module->allowPasswordRecovery ? ' (' . Html::a(Yii::t('usuario', 'Forgot password?'), ['/user/recovery/request']) . ')' : ''));

        echo $form->field($model, 'rememberMe')->checkBox(['tabindex' => ++$tab]);

        echo Html::submitButton(Yii::t('usuario', 'Sign in'), ['class' => 'btn btn-primary btn-block', 'tabindex' => ++$tab]);

        ActiveForm::end();

        if ($module->enableEmailConfirmation) {
            echo Html::tag('p', Html::a(Yii::t('usuario', 'Didn\'t receive confirmation message?'), ['/user/registration/resend']), ['class' => 'text-center']);
        }

        if ($module->enableRegistration) {
            echo Html::tag('p', Html::a(Yii::t('usuario', 'Don\'t have an account? Sign up!'), ['/user/registration/register']), ['class' => 'text-center']);
        }

        echo ConnectWidget::widget(['baseAuthUrl' => ['/user/security/auth']]);
    echo Html::endTag('div');
echo Html::endTag('div');
