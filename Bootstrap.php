<?php

namespace linex\base;

use yii\base\BootstrapInterface;

class Bootstrap implements BootstrapInterface
{
    public function bootstrap($app)
    {
        $app->i18n->translations['modules/base/*'] = [
            'class'            => 'yii\i18n\PhpMessageSource',
            'forceTranslation' => true,
            'basePath'         => '@vendor/linex/base/messages',
            'fileMap'          => [
                'modules/base/Base' => 'base.php',
            ],
        ];
    }
}