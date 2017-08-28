<?php
use app\models\tools\Qr;
use yii\bootstrap\{ActiveForm, Alert, Html};
use yii\helpers\Url;
use yii\web\View;

$this->title = 'QR Code Generator';
$this->params['breadcrumbs'][] = 'Tools';
$this->params['breadcrumbs'][] = $this->title;

if ($model->load(Yii::$app->request->post())) {
	$post = Yii::$app->request->post('Qr');
	$this->registerJs('$("#qr-dtstart").val("' . $post['dtStart'] . '")', View::POS_READY);
	$this->registerJs('$("#qr-dtend").val("' . $post['dtEnd'] . '")', View::POS_READY);
}
$this->registerJs(Yii::$app->formatter->jspack('tools/formQr.js'), View::POS_READY);
?>
<div class="row">
	<div class="col-md-offset-2 col-md-8"><?php
		echo Html::tag('h1', Html::encode($this->title));

		if ($icon = Yii::$app->session->getFlash('qr-success')) {
			$imgSize = min(250, $model->size + ($model->margin * 2));
			Alert::begin(['options' => ['class' => 'alert-success', 'style' => ['min-height' => $imgSize + 30 . 'px']]]);
			echo Html::img(Url::to('@web/assets/temp/qr/'.$icon), ['alt' => $model->type . ' QR Code', 'class' => 'inline-left pull-left', 'height' => $imgSize, 'width' => $imgSize]);
			echo Html::tag('p', 'Your QR Code has been generated successfully.');
			echo Html::tag('p', 'Do not link to the image on this website directly. Your image will be deleted shortly.');
			Alert::end();
		}

		$form = ActiveForm::begin();
		echo Qr::printFormField($form, $model, 'dropDownList', 'type', 'th-list', $tab++, null, Qr::getTypes());
		echo Qr::printFormField($form, $model, 'textInput', 'address', 'home', $tab++);
		echo Qr::printFormField($form, $model, 'otherInput', 'amount', 'bitcoin', $tab++, 'number');
		echo Qr::printFormField($form, $model, 'textInput', 'name', 'user', $tab++);
		echo Qr::printFormField($form, $model, 'textInput', 'title', 'header', $tab++);
		echo Qr::printFormField($form, $model, 'otherInput', 'url', 'globe', $tab++, 'url');
		echo '<div class="row">';
			echo Qr::printFormField($form, $model, 'textInput', 'lat', 'globe', $tab++, 'col-sm-4');
			echo Qr::printFormField($form, $model, 'textInput', 'lng', 'globe', $tab++, 'col-sm-4');
			echo Qr::printFormField($form, $model, 'textInput', 'altitude', 'globe', $tab++, 'col-sm-4');
		echo '</div>';
		echo Qr::printFormField($form, $model, 'textInput', 'summary', 'comment', $tab++);
		echo '<div class="row">';
			echo Qr::printFormField($form, $model, 'DateTimePicker', 'dtStart', 'time', $tab++, 'col-sm-6');
			echo Qr::printFormField($form, $model, 'DateTimePicker', 'dtEnd', 'time', $tab++, 'col-sm-6');
		echo '</div>';
		echo Qr::printFormField($form, $model, 'otherInput', 'email', 'envelope', $tab++, 'email');
		echo Qr::printFormField($form, $model, 'textInput', 'subject', 'header', $tab++);
		echo '<div class="row">';
			echo Qr::printFormField($form, $model, 'textInput', 'firstName', 'user', $tab++, 'col-sm-6');
			echo Qr::printFormField($form, $model, 'textInput', 'lastName', 'user', $tab++, 'col-sm-6');
		echo '</div>';
		echo Qr::printFormField($form, $model, 'textInput', 'sound', 'music', $tab++);
		echo Qr::printFormField($form, $model, 'otherInput', 'phone', 'phone-alt', $tab++, 'tel');
		echo Qr::printFormField($form, $model, 'otherInput', 'videoPhone', 'phone-alt', $tab++, 'tel');
		echo Qr::printFormField($form, $model, 'textInput', 'note', 'tag', $tab++);
		echo Qr::printFormField($form, $model, 'DatePicker', 'birthday', 'calendar', $tab++);
		echo Qr::printFormField($form, $model, 'textInput', 'nickName', 'user', $tab++);
		echo Qr::printFormField($form, $model, 'textArea', 'msg', 'comment', $tab++);
		echo Qr::printFormField($form, $model, 'textInput', 'fullName', 'user', $tab++);
		echo Qr::printFormField($form, $model, 'dropDownList', 'authentication', 'cog', $tab++, null, Qr::getAuthentication());
		echo Qr::printFormField($form, $model, 'textInput', 'ssid', 'signal', $tab++);
		echo Qr::printFormField($form, $model, 'textInput', 'password', 'lock', $tab++);
		echo Qr::printFormField($form, $model, 'checkBox', 'hidden', null, $tab++);
		echo Qr::printFormField($form, $model, 'textInput', 'videoId', 'header', $tab++);
		echo '<div class="row">';
			echo Qr::printFormField($form, $model, 'otherInput', 'size', 'move', 96, 'col-sm-6', 'number');
			echo Qr::printFormField($form, $model, 'otherInput', 'margin', 'fullscreen', 97, 'col-sm-6', 'number');
		echo '</div>';

		echo Html::tag('div',
			Html::resetButton('Reset', ['class' => 'btn btn-default', 'tabindex' => 99]) . ' ' .
			Html::submitButton('Generate QR Code', ['class' => 'btn btn-primary', 'tabindex' => 98])
		, ['class' => 'form-group field-qr-buttons text-right']);

		ActiveForm::end();
	?></div>
</div>
