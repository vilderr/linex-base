<?php

namespace linex\base\modules\user\controllers\backend\rbac;

use Yii;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\data\ArrayDataProvider;
use yii\rbac\Item;

use linex\base\BaseModule;
use linex\base\modules\user\UserModule;
use linex\base\modules\user\components\backend\Controller;
use linex\base\modules\user\models\backend\AuthItem;
use linex\base\helpers\StringHelper;

/**
 * Class RolesController
 * @package linex\base\modules\user\controllers\backend\rbac
 */
class RolesController extends Controller
{
    /**
     * @return string
     */
    public function actionIndex()
    {
        $rules = \Yii::$app->getAuthManager()->getRules();
        $roles = new ArrayDataProvider(
            [
                'id'         => 'roles',
                'allModels'  => \Yii::$app->getAuthManager()->getRoles(),
                'sort'       => [
                    'attributes' => ['name', 'description', 'ruleName', 'createdAt', 'updatedAt'],
                ],
                'pagination' => [
                    'pageSize' => 10,
                ],
            ]
        );

        $this->view->title = UserModule::t('Rbac roles');
        $this->view->params['breadcrumbs'][] = $this->view->title;

        return $this->render('index', [
            'roles'   => $roles,
            'isRules' => !empty($rules),
        ]);
    }

    /**
     * Добавление новой роли пользователя
     *
     * @return string|\yii\web\Response
     */
    public function actionAdd()
    {
        $post = Yii::$app->request->post();
        $model = new AuthItem(['isNewRecord' => true, 'type' => Item::TYPE_ROLE]);
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
            ArrayHelper::merge(
                Yii::$app->getAuthManager()->getRoles(),
                Yii::$app->getAuthManager()->getPermissions()
            ),
            'name',
            function ($item) {
                return $item->name . (strlen($item->description) > 0 ? ' [' . $item->description . ']' : '');
            },
            function ($item) {
                return AuthItem::getRbacType($item->type);
            }
        );


        $this->view->title = BaseModule::t('Add {name}', ['name' => StringHelper::toLower(UserModule::t('User Role'))]);
        $this->view->params['breadcrumbs'][] = [
            'label' => UserModule::t('Rbac roles'),
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
     * Редактирование роли пользователя
     *
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

        $item = Yii::$app->getAuthManager()->getRole($id);
        $items = ArrayHelper::map(
            ArrayHelper::merge(
                Yii::$app->getAuthManager()->getRoles(),
                Yii::$app->getAuthManager()->getPermissions()
            ),
            'name',
            function ($item) {
                return $item->name . (strlen($item->description) > 0 ? ' [' . $item->description . ']' : '');
            },
            function ($item) {
                return AuthItem::getRbacType($item->type);
            }
        );

        $children = \Yii::$app->getAuthManager()->getChildren($id);
        $selected = [];
        foreach ($children as $child) {
            $selected[] = $child->name;
        }

        $model->name = $item->name;
        $model->oldname = $item->name;
        $model->type = $item->type;
        $model->description = $item->description;
        $model->ruleName = $item->ruleName;

        $this->view->title = BaseModule::t('Edit {name}', ['name' => StringHelper::toLower(UserModule::t('User Role'))]);
        $this->view->params['breadcrumbs'][] = [
            'label' => UserModule::t('Rbac roles'),
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