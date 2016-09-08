<?php
use dektrium\user\widgets\Connect;
use yii\helpers\Html;

$this->title = 'Social Networks';
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('/_alert', ['module' => Yii::$app->getModule('user')]) ?>

<div class="row">
	<div class="col-md-offset-2 col-md-8">
		<?= Html::tag('h1', Html::encode($this->title)) ?>

		<?= Html::tag('div', 'You can connect multiple accounts to be able to log in using them.', ['class' => 'alert alert-info'])?>

		<?php $auth = Connect::begin([
			'baseAuthUrl' => ['/user/security/auth'],
			'accounts'    => $user->accounts,
			'autoRender'  => false,
			'popupMode'   => false,
		]) ?>

		<?php foreach ($auth->getClients() as $client): ?>
			<div class="row">
				<div class="col-md-offset-2 col-md-1">
					<?= Html::tag('span', '', ['aria-hidden' => 'true', 'class' => 'auth-icon ' . $client->getName()]) ?>
				</div>
				<?= Html::tag('div', $client->getTitle(), ['class' => 'col-md-2']) ?>
				<div class="col-md-5">
					<?= $auth->isConnected($client) ?
						Html::a('Disconnect', $auth->createClientUrl($client), [
							'class' => 'btn btn-danger btn-block',
							'data-method' => 'post',
						]) :
						Html::a('Connect', $auth->createClientUrl($client), [
							'class' => 'btn btn-success btn-block',
						])
					?>
				</div>
			</div>
		<?php endforeach; ?>

		<?php Connect::end() ?>
    </div>
</div>
