<?php

namespace mister42\models\music;

use Yii;

class LyricsQuery extends \yii\db\ActiveQuery
{
    public function init(): self
    {
        parent::init();
        $alias = ($this->modelClass === Lyrics1Artists::class) ? 'artist' : 'album';
        $this->alias($alias);

        $con1 = Yii::$app->id === 'mr42' && Yii::$app->controller->id !== 'feed';
        $con2 = Yii::$app->id === 'mister42' && !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin;
        $con3 = Yii::$app->id === 'mister42-console';
        return ($con1 || $con2 || $con3) ? $this : $this->onCondition(["{$alias}.active" => true]);
    }
}
