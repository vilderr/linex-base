<?php

namespace linex\base\widgets\typehead;

use yii\web\AssetBundle;

/**
 * Class Assets
 * @package linex\base\widgets\typehead
 */
class Assets extends AssetBundle
{
    public $sourcePath = '@linex/base/widgets/typehead/assets';

    public $css = [
        'css/typeahead.css',
    ];

    public $js = [
        'js/typeahead.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}