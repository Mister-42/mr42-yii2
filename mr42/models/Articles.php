<?php
namespace app\models;
use Yii;
use app\models\Pdf;
use Da\User\Model\{Profile, User};
use yii\behaviors\TimestampBehavior;
use yii\bootstrap\Html;
use yii\helpers\{StringHelper, Url};
use yii\web\AccessDeniedHttpException;

class Articles extends \yii\db\ActiveRecord {
	const STATUS_INACTIVE = 0;
	const STATUS_ACTIVE = 1;

	public static function tableName() {
		return '{{%articles}}';
	}

	public function afterFind() {
		parent::afterFind();
		$this->url = $this->url ?? $this->title;
	}

	public static function find() {
		return parent::find()
			->onCondition(['active' => Self::STATUS_ACTIVE]);
	}
}
