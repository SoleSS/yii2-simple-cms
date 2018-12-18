<?php

namespace SoleSS\cms;

class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'SoleSS\cms\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (Yii::$app instanceof \yii\console\Application) {
            $this->controllerNamespace = 'SoleSS\cms\commands';
        }

        // custom initialization code goes here
    }
}
