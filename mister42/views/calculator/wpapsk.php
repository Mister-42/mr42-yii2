<?php

use mister42\assets\ClipboardJsAsset;
use mister42\models\ActiveForm;
use mister42\models\calculator\Wpapsk;
use yii\bootstrap4\Html;
use yii\bootstrap4\Progress;
use yii\web\View;

$this->title = Yii::t('mr42', 'Wifi Protected Access Pre-Shared Key (WPA-PSK) Calculator');
$this->params['breadcrumbs'] = [Yii::t('mr42', 'Calculator')];
$this->params['breadcrumbs'][] = Yii::t('mr42', 'Wifi Protected Access Pre-Shared Key');

$model = new Wpapsk();
ClipboardJsAsset::register($this);
$this->registerJs(Yii::$app->formatter->jspack('calculator/wpapsk.js'), View::POS_HEAD);
$this->registerJs('resetPsk();', View::POS_READY);
$this->registerJs('$("input").keypress(function(e){if(e.which==13){calcPsk();return false}});', View::POS_READY);
$this->registerJs(Yii::$app->formatter->jspack('calculator/pbkdf2.js'), View::POS_END);
$this->registerJs(Yii::$app->formatter->jspack('calculator/sha1.js'), View::POS_END);

echo Html::beginTag('div', ['class' => 'row']);
    echo Html::beginTag('div', ['class' => 'col-md-12 col-lg-8 mx-auto']);
        echo Html::tag('h1', Html::tag('abbr', 'WPA', ['title' => 'Wifi Protected Access']) . '-' . Html::tag('abbr', 'PSK', ['title' => 'Pre-Shared Key']) . ' ' . Yii::t('mr42', 'Calculator'));
        echo Html::beginTag('div', ['class' => 'alert alert-info shadow']);
            echo Html::tag('div', Yii::t('mr42', 'This WPA-PSK calculator provides an easy way to convert a {ssid} and WPA Passphrase to the 256-bit pre-shared ("raw") key used for key derivation.', ['ssid' => Html::tag('abbr', 'SSID', ['title' => 'Service Set Identifier'])]));
            echo Html::tag('div', Yii::t('mr42', 'Type or paste in your SSID and WPA Passphrase below. Click {calcbutton} and wait a while as JavaScript isn\'t known for its blistering cryptographic speed. The Pre-Shared Key will be calculated by your browser. <b>None</b> of this information will be sent over the network.', ['calcbutton' => Html::tag('span', Yii::t('mr42', 'Calculate'), ['class' => 'font-italic'])]));
        echo Html::endTag('div');

        $form = ActiveForm::begin(['action' => false, 'id' => 'wpapsk']);
        $tab = 0;

        echo Html::beginTag('div', ['class' => 'row']);
            echo $form->field($model, 'ssid', [
                'icon' => 'wifi',
                'options' => ['class' => 'form-group col-md-6'],
            ])->textInput(['tabindex' => ++$tab]);

            echo $form->togglePassword($model, ++$tab, ['class' => 'col-md-6']);
        echo Html::endTag('div');

        echo $form->field($model, 'psk', [
            'inputTemplate' => '<div class="input-group">' . Yii::$app->icon->activeFieldIcon('key') . '{input}<span class="input-group-append">' . Html::button(Yii::$app->icon->name('copy'), ['class' => 'btn btn-primary clipboard-js-init', 'data-clipboard-target' => '#wpapsk-psk', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => Yii::t('mr42', 'Copy to Clipboard')]) . '</span></div>',
            'options' => ['class' => 'form-group has-success'],
        ])->textInput(['placeholder' => Yii::t('mr42', 'JavaScript is disabled in your web browser. This tool does not work without JavaScript.'), 'readonly' => true]);

        echo Html::tag(
            'div',
            Html::tag('label', Yii::t('mr42', 'Progress')) .
            Progress::widget([
                'options' => ['class' => 'progress-bar progress-bar-striped progress-bar-animated'],
            ]),
            ['class' => 'd-none form-group current-progress']
        );

        echo Html::tag(
            'div',
            Html::resetButton(Yii::t('mr42', 'Reset'), ['class' => 'btn btn-light shadow ml-1 suppress', 'tabindex' => $tab + 2, 'onclick' => 'resetPsk()']) .
            Html::button(Yii::t('mr42', 'Calculate'), ['class' => 'btn btn-primary shadow ml-1 suppress', 'tabindex' => ++$tab, 'onclick' => 'calcPsk()']),
            ['class' => 'btn-toolbar float-right form-group']
        );

        ActiveForm::end();
    echo Html::endTag('div');
echo Html::endTag('div');
