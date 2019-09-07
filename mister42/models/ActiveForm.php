<?php

namespace app\models;

use Yii;
use yii\bootstrap4\ButtonGroup;
use yii\bootstrap4\Html;
use yii\web\View;

class ActiveForm extends \thoulah\fontawesome\bootstrap4\ActiveForm
{
    public function submitToolbar(string $text, int $tab): string
    {
        return ButtonGroup::widget([
            'buttons' => [
                ['label' => Yii::t('mr42', 'Reset'), 'options' => ['class' => 'btn-light ml-1', 'tabindex' => $tab + 2, 'type' => 'reset']],
                ['label' => $text, 'options' => ['class' => 'btn-primary ml-1', 'tabindex' => ++$tab, 'type' => 'submit']]
            ],
            'options' => ['class' => 'form-group float-right'],
        ]);
    }
    public function togglePassword($model, int $tab, array $options = []): string
    {
        $this->getView()->registerJs("var togglePassword = {lang:{hide:'" . Yii::t('mr42', 'Hide Password') . "', show:'" . Yii::t('mr42', 'Show Password') . "'}};" . Yii::$app->formatter->jspack('togglePassword.js'), View::POS_READY);

        Html::addCssClass($options, 'form-group');
        $eyeIcons = (string) Yii::$app->icon->name('eye')->class('append');
        $eyeIcons .= (string) Yii::$app->icon->name('eye-slash')->class('append d-none');
        return $this->field($model, 'password', [
            'inputTemplate' => '<div class="input-group" id="pwdToggle">' . Yii::$app->icon->activeFieldIcon('lock') . '{input}<span class="input-group-append">' . Html::button($eyeIcons, ['class' => 'btn btn-primary', 'title' => Yii::t('mr42', 'Show Password')]) . '</span></div>',
            'options' => $options,
        ])->passwordInput(['tabindex' => $tab]);
    }
}
