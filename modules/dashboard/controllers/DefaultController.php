<?php

namespace linex\base\modules\dashboard\controllers;

use linex\base\helpers\StringHelper;
use Yii;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use yii\web\Response;

use linex\base\components\BackendController;
use linex\base\modules\dashboard\actions\FlushCache;

/**
 * Default controller for the `dashboard` module
 */
class DefaultController extends BackendController
{
    /**
     * @return array
     */
    public function behaviors()
    {
        $behaviors = [
            'access' => [
                'class' => AccessControl::className(),
                'only'  => ['flush-cache'],
                'rules' => [
                    [
                        'allow'   => true,
                        'actions' => ['flush-cache'],
                        'roles'   => ['cache manage'],
                    ],
                ],
            ],
        ];

        return ArrayHelper::merge(
            parent::behaviors(),
            $behaviors
        );
    }

    /**
     * @return array
     */
    public function actions()
    {
        return [
            'flush-cache' => [
                'class' => FlushCache::className(),
            ],
            'error'       => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionMakeSlug($str)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        return StringHelper::translit($str);
    }
}
