<?php

namespace linex\base\modules\user\controllers\backend;

use yii\data\ArrayDataProvider;
use linex\base\components\BackendController;
use linex\base\modules\user\UserModule;

/**
 * Class RbacController
 * @package linex\base\modules\user\controllers\backend
 */
class RbacController extends BackendController
{
    public function actionIndex()
    {
        $rules = \Yii::$app->getAuthManager()->getRules();
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

        return $this->render('index', [
            'permissions' => $permissions,
            'roles'       => $roles,
            'isRules'     => !empty($rules),
        ]);
    }
}