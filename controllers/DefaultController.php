<?php

namespace linex\base\controllers;

use yii\web\Controller;

/**
 * Class DefaultController
 * @package linex\base\controllers
 */
class DefaultController extends Controller
{
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }
}