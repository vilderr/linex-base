<?php

namespace linex\base\modules\dashboard\widgets\removeallbutton;

use yii\web\AssetBundle;

/**
 * Class Asset
 * @package linex\base\modules\dashboard\widgets\removeallbutton
 */
class Asset extends AssetBundle
{
    public $sourcePath = '@linex/base/modules/dashboard/widgets/removeallbutton/assets';

    public $js = [
        'js/script.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}