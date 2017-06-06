<?php

namespace linex\base\modules\dashboard\widgets\flushcache;

use yii\web\AssetBundle;

/**
 * Class Asset
 * @package linex\base\modules\dashboard\widgets\flushcache
 */
class Asset extends AssetBundle
{
    public $sourcePath = '@linex/base/modules/dashboard/widgets/flushcache/assets';
    public $js = [
        'js/flushcache.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}