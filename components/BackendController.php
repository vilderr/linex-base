<?php

namespace linex\base\components;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * Class BackendController
 * @package linex\base\components
 */
class BackendController extends Controller
{
    public function init()
    {
        Yii::$app->user->loginUrl = '/dashboard/sign/in';
    }

    public function behaviors()
    {
        return [
            'administrate' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['administrate'],
                    ],
                ],
            ],
        ];
    }
}