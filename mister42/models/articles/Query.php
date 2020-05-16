<?php

namespace mister42\models\articles;

use Yii;
use yii\web\Request;

class Query extends \yii\db\ActiveQuery
{
    public function init(): self
    {
        parent::init();
        $alias = ($this->modelClass === Articles::class) ? 'article' : 'comment';
        $this->alias($alias);
        $request = new Request();
        return (php_sapi_name() === 'cli' || $request->getHostName() === 'mr42.me' || (!Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin))
            ? $this
            : $this->onCondition(["{$alias}.active" => true]);
    }
}
