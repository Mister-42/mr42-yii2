<?php

namespace app\models;

use Yii;

class Icon extends \thoulah\fontawesome\IconComponent
{
    public function instrumental(): self
    {
        return $this->name('images/instrumental', '')
            ->fontAwesomeFolder('@assetsroot')
            ->title(Yii::t('mr42', 'Instrumental'));
    }
}
