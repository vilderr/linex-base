<?php
namespace linex\base\modules\reference;

use yii\base\BootstrapInterface;

class Bootstrap implements BootstrapInterface
{
    public function bootstrap($app)
    {
        $app->i18n->translations['modules/reference/*'] = [
            'class'            => 'yii\i18n\PhpMessageSource',
            'forceTranslation' => true,
            'basePath'         => '@vendor/linex/base/modules/reference/messages',
            'fileMap'          => [
                'modules/reference/Module' => 'module.php',
            ],
        ];

        $app->getUrlManager()->addRules([
            [
                'class'       => 'yii\web\GroupUrlRule',
                'prefix'      => 'dashboard',
                'routePrefix' => 'dashboard',
                'rules'       => [
                    '<_m:(reference)>'                                   => '<_m>/default/index',
                    '<_m:(reference)>/<_a:(add|edit|delete|delete-all)>' => '<_m>/default/<_a>',
                ],
            ],
        ]);
    }
}