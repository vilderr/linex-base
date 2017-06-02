<?php

namespace linex\base\modules\user;

use yii\base\BootstrapInterface;

/**
 * user module definition class
 *
 * Class Module
 * @package linex\base\modules\user
 */
class Module extends \yii\base\Module implements BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'linex\base\modules\user\controllers';


    public function bootstrap($app)
    {

    }
}
