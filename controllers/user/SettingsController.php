<?php
namespace app\controllers\user;
use dektrium\user\controllers\SettingsController as BaseSettingsController;
use Yii;
use app\models\user\Profile;

class SettingsController extends BaseSettingsController
{
	public function actionProfile()
	{
		$model = $this->finder->findProfileById(Yii::$app->user->identity->getId());
		if ($model === null) {
			$model = Yii::createObject(Profile::className());
			$model->link('user', Yii::$app->user->identity);
		}
		$event = $this->getProfileEvent($model);
		$this->performAjaxValidation($model);
		$this->trigger(self::EVENT_BEFORE_PROFILE_UPDATE, $event);
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			if (empty($model->bio)) {
				Yii::$app->getSession()->setFlash('success', 'Your profile has been updated');
			} else
				Yii::$app->getSession()->setFlash('success', '<p><strong>Your profile has been updated</strong></p>'.Profile::show($model));
			$this->trigger(self::EVENT_AFTER_PROFILE_UPDATE, $event);
			return $this->refresh();
		}

		return $this->render('profile', [
			'model' => $model,
		]);
	}
}
