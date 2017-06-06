<?php

namespace linex\base\helpers;

use Yii;

/**
 * Class Utils
 * @package linex\base\helpers
 */
final class Utils
{
    public static function getLocale()
    {
        return strtolower(substr(Yii::$app->language, 0, 2));
    }
}