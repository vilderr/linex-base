<?php

namespace linex\base\modules\dashboard;

use Yii;
use yii\base\BootstrapInterface;

/**
 * dashboard module definition class
 */
class DashboardModule extends \yii\base\Module implements BootstrapInterface
{
    public static $administratePermission = 'administrate';

    public function bootstrap($app)
    {
        $app->i18n->translations['modules/dashboard/*'] = [
            'class'            => 'yii\i18n\PhpMessageSource',
            'forceTranslation' => true,
            'basePath'         => '@vendor/linex/base/modules/dashboard/messages',
            'fileMap'          => [
                'modules/dashboard/Module' => 'module.php',
                'modules/dashboard/Menu'   => 'menu.php',
            ],
        ];

        $app->getUrlManager()->addRules([
            [
                'class'       => 'yii\web\GroupUrlRule',
                'prefix'      => 'dashboard',
                'routePrefix' => 'dashboard',
                'rules'       => [
                    ''                          => 'default/index',
                    '<_a:(flush-cache|make-slug)>'        => 'default/<_a>',
                    '<_c:(backend-menu)>'       => '<_c>/index',
                    '<_c:[\w\-]+>/<_a:[\w\-]+>' => '<_c>/<_a>',
                ],
            ],
        ]);
    }

    public function init()
    {
        parent::init();
    }

    public static function t($message, $params = [], $category = 'Module', $language = null)
    {
        return Yii::t('modules/dashboard/' . $category, $message, $params, $language);
    }
}
