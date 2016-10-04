<?php
namespace app\models\lyrics;
use Yii;

class Lyrics1Artists extends \yii\db\ActiveRecord
{
	const STATUS_INACTIVE = '0';
	const STATUS_ACTIVE = '1';

	public $artistName;
	public $artistUrl;

	public static function tableName()
	{
		 return '{{%lyrics_1_artists}}';
	}

	public static function lastUpdate() {
		$lastUpdate = self::find()
			->select(['updated' => 'max(UNIX_TIMESTAMP(updated))'])
			->asArray()
			->one();

		return $lastUpdate['updated'];
	}

	public static function artistsList() {
		return self::find()
			->select(['artistName' => 'name', 'artistUrl' => 'COALESCE(`url`, `name`)', 'updated' => 'UNIX_TIMESTAMP(updated)', 'active'])
			->orderBy('name')
			->all();
	}

	public static function getAlbums()
	{
		return parent::hasMany(Lyrics2Albums::className(), ['parent' => 'id'])
			->onCondition(
				!Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin
					? ['or', [Lyrics2Albums::tableName().'.`active`' => [self::STATUS_INACTIVE, self::STATUS_ACTIVE]]]
					: [Lyrics2Albums::tableName().'.`active`' => self::STATUS_ACTIVE]
			)
		;
	}

	public static function find()
	{
		return parent::find()
			->onCondition(
				php_sapi_name() !== 'cli' && !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin
					? ['or', [self::tableName().'.`active`' => [Self::STATUS_INACTIVE, Self::STATUS_ACTIVE]]]
					: [self::tableName().'.`active`' => Self::STATUS_ACTIVE]
				)
		;
	}
}
