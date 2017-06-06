<?php

namespace vendor\linex\base\modules\user\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use linex\base\components\BackendController;
use linex\base\modules\user\models\backend\Search;
use linex\base\behaviors\BanDelete;
use yii\web\Controller;

/**
 * Class DefaultController
 * @package vendor\linex\base\modules\user\controllers
 */
class DefaultController extends Controller
{

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new Search();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index');
    }
}
