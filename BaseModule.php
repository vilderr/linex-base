<?php

namespace linex\base;

use Yii;
use yii\base\BootstrapInterface;
use yii\base\Module;

/**
 * Class BaseModule
 * @package linex\base
 */
class BaseModule extends Module implements BootstrapInterface
{
    public $serverName = 'localhost';
    public $serverPort = 80;

    public static function t($message, $category = 'Module', $params = [], $language = null)
    {
        return Yii::t('modules/base/' . $category, $message, $params, $language);
    }

    public function bootstrap($app)
    {
        $app->i18n->translations['modules/base/*'] = [
            'class'            => 'yii\i18n\PhpMessageSource',
            'forceTranslation' => true,
            'basePath'         => '@vendor/linex/base/messages',
            'fileMap'          => [
                'modules/base/Module' => 'base.php',
            ],
        ];
    }
}