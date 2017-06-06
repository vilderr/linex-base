<?php

namespace linex\base\modules\dashboard\controllers;

use linex\base\helpers\StringHelper;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use yii\helpers\Url;

use linex\base\BaseModule;
use linex\base\modules\dashboard\DashboardModule;
use linex\base\modules\dashboard\models\BackendMenu;
use linex\base\components\BackendController;

/**
 * Class BackendMenuController
 * @package linex\base\modules\dashboard\controllers
 */
class BackendMenuController extends BackendController
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['setting manage'],
                    ],
                ],
            ],
            'verbs'  => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'delete'     => ['post'],
                    'delete-all' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex($parent_id = 0)
    {
        if (($model = BackendMenu::findOne($parent_id)) === null) {
            $model = new BackendMenu();
            $model->id = 0;
            $model->name = BaseModule::t('Parent level');
            $model->loadDefaultValues();
        }

        $dataProvider = $model->getProvider();
        $navChain = BackendMenu::getNavChain($model);

        $this->view->title = DashboardModule::t('Dashboard menu settings');
        $this->view->params['breadcrumbs'][] = [
            'label' => $this->view->title,
            'url'   => empty($navChain) ? null : Url::to(['/dashboard/backend-menu']),
        ];

        foreach ($navChain as $chain) {
            $this->view->params['breadcrumbs'][] = [
                'label' => $chain->name,
                'url'   => ($model->id != $chain->id) ? Url::to(['/dashboard/backend-menu', 'parent_id' => $chain->id]) : null,
            ];
        }

        return $this->render(
            'index',
            [
                'dataProvider' => $dataProvider,
                'model'        => $model,
                'navChain'     => $navChain,
            ]
        );
    }

    /**
     * @param int $parent_id
     *
     * @return string|\yii\web\Response
     * @throws ServerErrorHttpException
     */
    public function actionAdd($parent_id = 0)
    {
        $parent_id = intval($parent_id);
        if ($parent_id > 0) {
            $rootModel = $this->findModel($parent_id);
        }

        $model = new BackendMenu();
        $model->loadDefaultValues();
        $model->parent_id = $parent_id;
        $post = Yii::$app->request->post();

        if ($model->load($post) && $model->validate()) {
            if ($model->save()) {
                $returnUrl = Yii::$app->request->get('returnUrl', [
                    Url::toRoute('index'),
                    'parent_id' => $model->parent_id,
                ]);

                switch (Yii::$app->request->post('action', 'save')) {
                    case 'back':
                        return $this->redirect($returnUrl);
                    default:
                        return $this->redirect(
                            Url::toRoute(
                                [
                                    'edit',
                                    'id'        => $model->id,
                                    'returnUrl' => $returnUrl,
                                ]
                            )
                        );
                }
            } else {
                throw new ServerErrorHttpException;
            }
        }

        $this->view->title = BaseModule::t('Add {name}', ['name' => StringHelper::toLower(DashboardModule::t('Menu Item', [], 'Menu'))]);

        $this->view->params['breadcrumbs'][] = [
            'label' => DashboardModule::t('Dashboard menu settings'),
            'url'   => Url::toRoute('index'),
        ];
        $this->view->params['breadcrumbs'][] = $this->view->title;

        return $this->render('add', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     *
     * @return string|\yii\web\Response
     * @throws ServerErrorHttpException
     */
    public function actionEdit($id)
    {
        $model = $this->findModel($id);
        $post = Yii::$app->request->post();

        if ($model->load($post) && $model->validate()) {
            if ($model->save()) {
                $returnUrl = Yii::$app->request->get('returnUrl', [
                    Url::toRoute([
                        'index',
                        'parent_id' => $model->parent_id,
                    ]),
                ]);

                switch (Yii::$app->request->post('action', 'save')) {
                    case 'back':
                        return $this->redirect($returnUrl);
                    default:
                        return $this->redirect(
                            Url::toRoute(
                                [
                                    'edit',
                                    'id'        => $model->id,
                                    'returnUrl' => $returnUrl,
                                ]
                            )
                        );
                }
            } else {
                throw new ServerErrorHttpException;
            }
        }

        $this->view->title = BaseModule::t('Edit {name}', ['name' => StringHelper::toLower(DashboardModule::t('Menu Item', [], 'Menu'))]);

        $this->view->params['breadcrumbs'][] = [
            'label' => DashboardModule::t('Dashboard menu settings'),
            'url'   => Url::toRoute('index'),
        ];
        $this->view->params['breadcrumbs'][] = BaseModule::t('Edit');

        return $this->render('edit', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     *
     * @return \yii\web\Response
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if ($model->delete()) {
            Yii::$app->session->setFlash('success', DashboardModule::t('Item has been removed', [], 'Menu'));
        }

        return $this->redirect(Url::to(['index', 'parent_id' => $model->parent_id]));
    }

    /**
     * @param $parent_id
     *
     * @return \yii\web\Response
     */
    public function actionDeleteAll($parent_id)
    {
        $items = Yii::$app->request->post('items', []);
        if (!empty($items)) {
            $items = BackendMenu::find()->where(['in', 'id', $items])->all();
            foreach ($items as $item) {
                $item->delete();
            }
        }

        return $this->redirect(['index', 'parent_id' => $parent_id]);
    }

    /**
     * @param $id
     *
     * @return BackendMenu
     * @throws NotFoundHttpException
     */
    private function findModel($id)
    {
        if (($model = BackendMenu::findById($id)) === null) {
            throw new NotFoundHttpException;
        }

        return $model;
    }
}