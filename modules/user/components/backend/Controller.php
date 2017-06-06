<?php

namespace linex\base\modules\user\components\backend;

use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use linex\base\components\BackendController;

class Controller extends BackendController
{
    public function behaviors()
    {
        $behaviors = [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['user manage'],
                    ],
                ],
            ],
            'verbs'  => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];

        return ArrayHelper::merge(
            parent::behaviors(),
            $behaviors
        );
    }
}