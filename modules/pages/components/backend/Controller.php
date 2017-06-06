<?php

namespace linex\base\modules\pages\components\backend;

use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

use linex\base\components\BackendController;

/**
 * Class Controller
 * @package linex\base\modules\pages\components\backend
 */
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
                        'roles' => ['content manage'],
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