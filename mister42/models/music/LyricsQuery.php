<?php

namespace mister42\models\music;

use Yii;
use yii\web\Request;

class LyricsQuery extends \yii\db\ActiveQuery
{
    public function init(): self
    {
        parent::init();
        $alias = ($this->modelClass === Lyrics1Artists::class) ? 'artist' : 'album';
        $this->alias($alias);
        $request = new Request();
        return (php_sapi_name() === 'cli' || $request->getHostName() === 'mr42.me' || (!Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin))
            ? $this
            : $this->onCondition(["{$alias}.active" => true]);
    }
}
