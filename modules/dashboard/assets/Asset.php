<?php

namespace linex\base\modules\dashboard\assets;

use yii\web\AssetBundle;

/**
 * Class Asset
 * @package linex\base\modules\dashboard\assets
 */
class Asset extends AssetBundle
{
    public $sourcePath = '@linex/base/modules/dashboard/assets/dist';

    public function init()
    {
        parent::init();

        $this->css = [
            YII_DEBUG ? 'css/admin.css?' . time() : 'css/admin.min.css',
            YII_DEBUG ? 'css/skins/default.css?' . time() : 'css/skins/default.min.css',
        ];

        $this->js = [
            YII_DEBUG ? 'js/admin.js?' . time() : 'js/admin.js',
            'js/plugins/slimscroll/jquery.slimscroll.min.js',
            'js/plugins/amaran/jquery.amaran.min.js',
        ];
    }

    public $depends = [
        'yii\web\YiiAsset',
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        '\kartik\icons\FontAwesomeAsset',
    ];
}