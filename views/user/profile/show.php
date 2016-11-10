<?php
use app\models\user\Profile;
use yii\bootstrap\Html;

$title = empty($profile->name) ? Html::encode($profile->user->username) : Html::encode($profile->name);
$this->title = $title;
$this->params['breadcrumbs'][] = 'Profile';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
	<div class="col-sm-12">
		<?= Html::tag('h2', $title) ?>
		<div class="row">
			<div class="col-md-6"><?php
				if (!empty($profile->location))
					echo Html::icon('map-marker', ['class' => 'text-muted']) . Html::encode($profile->location) . '<br>';
				if (!empty($profile->website))
					echo Html::icon('globe', ['class' => 'text-muted']) . Html::a(Html::encode($profile->website), Html::encode($profile->website));
			?></div>
			<div class="col-md-6 text-right">
				<?= '<time datetime="'.date(DATE_W3C, $profile->user->created_at).'">' . Yii::t('user', 'Joined on {0, date}', $profile->user->created_at) . '</time>' ?> <?= Html::icon('time', ['class' => 'text-muted']) ?><br>
				<?php if ($profile->user->created_at != $profile->user->updated_at): ?>
					<?= '<time datetime="'.date(DATE_W3C, $profile->user->updated_at).'">' . Yii::t('user', 'Updated on {0, date}', $profile->user->updated_at) . '</time>' ?> <?= Html::icon('refresh', ['class' => 'text-muted']) ?>
				<?php endif; ?>
			</div>
		</div>
		<hr><?php
		if (!empty($profile->bio))
			echo Profile::show($profile);
	?></div>
</div>
