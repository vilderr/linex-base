<?php

namespace linex\base\modules\dashboard\controllers;

use linex\base\modules\dashboard\DashboardModule;
use Yii;
use linex\base\modules\user\models\forms\LoginForm;
use yii\filters\VerbFilter;
use yii\web\Controller;

/**
 * Class SignController
 * @package linex\base\modules\dashboard\controllers
 */
class SignController extends Controller
{
    public $layout = 'empty';

    public function behaviors()
    {
        return [
            'verbs' => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'out' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @return string|\yii\web\Response
     */
    public function actionIn()
    {
        if (Yii::$app->user->can(DashboardModule::$administratePermission)) {
            Yii::$app->getResponse()->redirect(Yii::$app->user->getReturnUrl('/dashboard'));
        }

        $model = new LoginForm();
        $model->setScenario(LoginForm::SCENARIO_DASHBOARD);
        $post = Yii::$app->request->post();

        if ($model->load($post) && $model->login()) {
            return $this->goBack('/dashboard');
        }

        $this->view->title = 'Авторизация';

        return $this->render('in', [
            'model' => $model,
        ]);
    }

    public function actionOut()
    {
        Yii::$app->user->logout();

        return $this->redirect('/dashboard/sign/in');
    }
}