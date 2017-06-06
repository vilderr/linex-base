<?php

namespace linex\base\modules\user\controllers\backend;

use linex\base\BaseModule;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\rbac\Item;
use linex\base\modules\user\components\backend\Controller;
use linex\base\modules\user\UserModule;
use linex\base\modules\user\models\User;
use linex\base\modules\user\models\backend\Search;

/**
 * Class DefaultController
 * @package linex\base\modules\user\controllers\backend
 */
class DefaultController extends Controller
{
    /**
     * @return string
     */
    public function actionIndex()
    {
        //echo '<pre>'; print_r($this->behaviors());echo'</pre>';
        $searchModel = new Search();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $this->view->title = UserModule::t('Users list');
        $this->view->params['breadcrumbs'][] = $this->view->title;

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return string|\yii\web\Response
     */
    public function actionAdd()
    {
        $model = new User(['scenario' => 'adminSignup']);
        $model->generateAuthKey();
        $assignments = Yii::$app->authManager->getAssignments(null);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->save();

            $postAssignments = Yii::$app->request->post('AuthAssignment', []);
            $errors = [];

            foreach ($assignments as $assignment) {
                $key = array_search($assignment->roleName, $postAssignments);
                if ($key === false) {
                    Yii::$app->authManager->revoke(new Item(['name' => $assignment->roleName]), $model->id);
                } else {
                    unset($postAssignments[$key]);
                }
            }
            foreach ($postAssignments as $assignment) {
                try {
                    Yii::$app->authManager->assign(new Item(['name' => $assignment]), $model->id);
                } catch (\Exception $e) {
                    $errors[] = 'Cannot assign "' . $assignment . '" to user';
                }
            }
            if (count($errors) > 0) {
                Yii::$app->getSession()->setFlash('error', implode('<br />', $errors));
            }
            Yii::$app->session->setFlash('success', UserModule::t('User has been saved'));

            $returnUrl = Yii::$app->request->get('returnUrl', ['index']);
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
        }

        $this->view->title = UserModule::t('Add User');
        $this->view->params['breadcrumbs'][] = [
            'label' => UserModule::t('Users list'),
            'url'   => Url::toRoute('index'),
        ];
        $this->view->params['breadcrumbs'][] = BaseModule::t('Add');

        return $this->render('add', [
            'model'       => $model,
            'assignments' => ArrayHelper::map($assignments, 'roleName', 'roleName'),
        ]);
    }

    /**
     * @param $id
     *
     * @return string|\yii\web\Response
     */
    public function actionEdit($id)
    {
        $model = $this->findModel($id);
        $model->scenario = 'admin';
        $assignments = Yii::$app->authManager->getAssignments($id);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->save();

            $postAssignments = Yii::$app->request->post('AuthAssignment', []);
            $errors = [];

            foreach ($assignments as $assignment) {
                $key = array_search($assignment->roleName, $postAssignments);
                if ($key === false) {
                    Yii::$app->authManager->revoke(new Item(['name' => $assignment->roleName]), $model->id);
                } else {
                    unset($postAssignments[$key]);
                }
            }
            foreach ($postAssignments as $assignment) {
                try {
                    Yii::$app->authManager->assign(new Item(['name' => $assignment]), $model->id);
                } catch (\Exception $e) {
                    $errors[] = 'Cannot assign "' . $assignment . '" to user';
                }
            }
            if (count($errors) > 0) {
                Yii::$app->getSession()->setFlash('error', implode('<br />', $errors));
            }

            Yii::$app->session->setFlash('success', UserModule::t('User has been saved'));

            $returnUrl = Yii::$app->request->get('returnUrl', ['index']);

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
        }

        $this->view->title = UserModule::t('Edit User{username}', ['username' => '']);
        $this->view->params['breadcrumbs'][] = [
            'label' => UserModule::t('Users list'),
            'url'   => Url::toRoute('index'),
        ];

        $this->view->params['breadcrumbs'][] = UserModule::t('Edit User{username}', ['username' => ' - "' . $model->username . '"']);

        return $this->render('edit', [
            'model'       => $model,
            'assignments' => ArrayHelper::map($assignments, 'roleName', 'roleName'),
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
        $model->delete();

        return $this->redirect(['index']);
    }

    public function actionDeleteAll()
    {
        $items = Yii::$app->request->post('items', []);
        if (!empty($items)) {
            $items = User::find()->where(['in', 'id', $items])->all();
            foreach ($items as $item) {
                $item->delete();
            }
        }
    }

    /**
     * @param $id
     *
     * @return User
     * @throws NotFoundHttpException
     */
    private function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException;
    }
}