<?php

namespace linex\base\modules\dashboard\assets;

use yii\web\AssetBundle;

class EmptyAsset extends AssetBundle
{
    public $sourcePath = '@linex/base/modules/dashboard/assets/dist';

    public function init()
    {
        parent::init();

        $this->css = [
            YII_DEBUG ? 'css/empty.css?' . time() : 'css/empty.min.css',
        ];
    }

    public $depends = [
        'yii\web\YiiAsset',
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapAsset',
        '\kartik\icons\FontAwesomeAsset',
    ];
}