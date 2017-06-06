<?php

namespace linex\base\modules\pages;

use Yii;
use yii\base\BootstrapInterface;

/**
 * Class Module
 * @package linex\base\modules\pages
 */
class PagesModule extends \yii\base\Module implements BootstrapInterface
{
    public function bootstrap($app)
    {
        $app->i18n->translations['modules/pages/*'] = [
            'class'            => 'yii\i18n\PhpMessageSource',
            'forceTranslation' => true,
            'basePath'         => '@vendor/linex/base/modules/pages/messages',
            'fileMap'          => [
                'modules/pages/Module' => 'module.php',
            ],
        ];

        $app->getUrlManager()->addRules([
            [
                'class'       => 'yii\web\GroupUrlRule',
                'prefix'      => 'dashboard',
                'routePrefix' => 'dashboard',
                'rules'       => [
                    '<_m:(pages)>'                                   => '<_m>/default/index',
                    '<_m:(pages)>/<_a:(add|edit|delete|delete-all)>' => '<_m>/default/<_a>',
                ],
            ],
        ]);
    }

    /**
     * @param string $message
     * @param string $category
     * @param array  $params
     * @param null   $language
     *
     * @return string
     */
    public static function t($message, $params = [], $category = 'Module', $language = null)
    {
        return Yii::t('modules/pages/' . $category, $message, $params, $language);
    }
}
