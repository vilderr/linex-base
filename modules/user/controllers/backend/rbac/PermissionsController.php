<?php

namespace linex\base\modules\user\controllers\backend\rbac;

use Yii;
use yii\data\ArrayDataProvider;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

use linex\base\modules\user\components\backend\Controller;
use linex\base\BaseModule;
use linex\base\modules\user\UserModule;
use linex\base\helpers\StringHelper;
use linex\base\modules\user\models\backend\AuthItem;
use yii\rbac\Item;

/**
 * Class PermissionsController
 * @package linex\base\modules\user\controllers\backend\rbac
 */
class PermissionsController extends Controller
{
    /**
     * @return string
     */
    public function actionIndex()
    {
        $rules = Yii::$app->getAuthManager()->getRules();
        $permissions = new ArrayDataProvider(
            [
                'id'         => 'permissions',
                'allModels'  => \Yii::$app->getAuthManager()->getPermissions(),
                'sort'       => [
                    'attributes' => ['name', 'description', 'ruleName', 'createdAt', 'updatedAt'],
                ],
                'pagination' => [
                    'pageSize' => 10,
                ],
            ]
        );

        $this->view->title = UserModule::t('Rbac permissions');
        $this->view->params['breadcrumbs'][] = $this->view->title;

        return $this->render('index', [
            'permissions' => $permissions,
            'isRules'     => !empty($rules),
        ]);
    }

    /**
     * @return string|\yii\web\Response
     */
    public function actionAdd()
    {
        $post = Yii::$app->request->post();
        $model = new AuthItem(['isNewRecord' => true, 'type' => Item::TYPE_PERMISSION]);
        $rules = ArrayHelper::map(Yii::$app->getAuthManager()->getRules(), 'name', 'name');

        if ($model->load($post) && $model->validate()) {
            $item = $model->createItem();

            if ($model->errorMessage !== null) {
                Yii::$app->getSession()->setFlash('error', $model->getErrorMessage());

                return $this->redirect(['edit', 'id' => $item->name]);
            } else {
                Yii::$app->session->setFlash('success', BaseModule::t('Record has been saved'));
                $returnUrl = Yii::$app->request->get('returnUrl', Url::toRoute(['index']));

                switch (Yii::$app->request->post('action', 'save')) {
                    case 'back':
                        return $this->redirect($returnUrl);
                    default:
                        return $this->redirect([
                            'edit',
                            'id'        => $item->name,
                            'returnUrl' => $returnUrl,
                        ]);
                }
            }
        }

        $items = ArrayHelper::map(
            \Yii::$app->getAuthManager()->getPermissions(),
            'name',
            function ($item) {
                return $item->name . (strlen($item->description) > 0 ? ' [' . $item->description . ']' : '');
            }
        );

        $this->view->title = BaseModule::t('Add {name}', ['name' => StringHelper::toLower(UserModule::t('User Permission'))]);
        $this->view->params['breadcrumbs'][] = [
            'label' => UserModule::t('Rbac permissions'),
            'url'   => Url::toRoute(['index']),
        ];
        $this->view->params['breadcrumbs'][] = BaseModule::t('Add');

        return $this->render('add', [
            'model'    => $model,
            'rules'    => $rules,
            'items'    => $items,
            'children' => [],
        ]);
    }

    /**
     * @param $id
     *
     * @return string|\yii\web\Response
     */
    public function actionEdit($id)
    {
        $post = Yii::$app->request->post();
        $rules = ArrayHelper::map(\Yii::$app->getAuthManager()->getRules(), 'name', 'name');
        $model = new AuthItem();

        if ($model->load($post) && $model->validate()) {
            $item = $model->updateItem();

            if ($model->errorMessage !== null) {
                Yii::$app->getSession()->setFlash('error', $model->getErrorMessage());

                return $this->redirect(['edit', 'id' => $item->name]);
            } else {
                Yii::$app->session->setFlash('success', BaseModule::t('Record has been saved'));
                $returnUrl = Yii::$app->request->get('returnUrl', Url::toRoute(['index']));

                switch (Yii::$app->request->post('action', 'save')) {
                    case 'back':
                        return $this->redirect($returnUrl);
                    default:
                        return $this->redirect([
                            'edit',
                            'id'        => $item->name,
                            'returnUrl' => $returnUrl,
                        ]);
                }
            }
        }

        $item = Yii::$app->getAuthManager()->getPermission($id);
        $items = ArrayHelper::map(
            Yii::$app->getAuthManager()->getPermissions(),
            'name',
            function ($item) {
                return $item->name . (strlen($item->description) > 0 ? ' [' . $item->description . ']' : '');
            }
        );

        $children = Yii::$app->getAuthManager()->getChildren($id);
        $selected = [];
        foreach ($children as $child) {
            $selected[] = $child->name;
        }
        $model->name = $item->name;
        $model->oldname = $item->name;
        $model->type = $item->type;
        $model->description = $item->description;
        $model->ruleName = $item->ruleName;

        $this->view->title = BaseModule::t('Edit {name}', ['name' => StringHelper::toLower(UserModule::t('User Permission'))]);
        $this->view->params['breadcrumbs'][] = [
            'label' => UserModule::t('Rbac permissions'),
            'url'   => Url::toRoute(['index']),
        ];
        $this->view->params['breadcrumbs'][] = BaseModule::t('Edit');

        return $this->render(
            'edit',
            [
                'model'    => $model,
                'rules'    => $rules,
                'children' => $selected,
                'items'    => $items,
            ]
        );
    }

    /**
     * Удаление существующей Rbac сущности.
     * В случае успеха редирект на индексную страницу контроллера
     *
     * @param $id
     *
     * @return \yii\web\Response
     */
    public function actionDelete($id)
    {
        if (AuthItem::isProtectedItem($id)) {
            Yii::$app->session->setFlash('error', BaseModule::t('Not allowed!'));
        } else {
            Yii::$app->getAuthManager()->remove(new Item(['name' => $id]));
        }

        return $this->redirect(['index']);
    }
}