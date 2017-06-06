<?php

namespace linex\base\modules\pages\controllers\backend;

use linex\base\helpers\StringHelper;
use Yii;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

use linex\base\BaseModule;
use linex\base\modules\pages\components\backend\Controller;
use linex\base\modules\pages\models\Page;
use linex\base\modules\pages\PagesModule;

/**
 * Class DefaultController
 * @package vendor\linex\base\modules\pages\controllers\backend
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex($parent_id = 0)
    {
        if (($model = Page::findOne($parent_id)) === null) {
            $model = new Page(['id' => 0]);
            $model->loadDefaultValues();
        }

        $dataProvider = $model->getProvider();

        $this->view->title = PagesModule::t('Module name');
        $this->view->params['breadcrumbs'][] = $this->view->title;

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'model'        => $model,
        ]);
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

        $model = new Page();
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

        // META TAGS
        $this->view->title = 'Добавить страницу';
        $this->view->params['breadcrumbs'][] = [
            'label' => PagesModule::t('Module name'),
            'url'   => Url::toRoute('index'),
        ];
        $this->view->params['breadcrumbs'][] = BaseModule::t('Add');

        return $this->render('add', [
            'model' => $model,
        ]);
    }

    public function actionEdit($id)
    {
        $post = Yii::$app->request->post();
        $model = $this->findModel($id);

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

        $this->view->title = PagesModule::t('Module name') . ': ' . StringHelper::toLower(BaseModule::t('Editing'));
        $this->view->params['breadcrumbs'][] = [
            'label' => PagesModule::t('Module name'),
            'url'   => Url::toRoute('index'),
        ];
        $this->view->params['breadcrumbs'][] = BaseModule::t('Editing');

        return $this->render('edit', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {

    }

    /**
     * @param $id
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    private function findModel($id)
    {
        if (($model = Page::findById($id)) === null) {
            throw new NotFoundHttpException;
        }

        return $model;
    }
}
