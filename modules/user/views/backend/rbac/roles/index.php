<?php
/**
 * @var $this    \yii\web\View
 * @var $roles   \yii\data\ArrayDataProvider
 * @var $isRules bool
 */

use yii\helpers\Html;
use kartik\dynagrid\DynaGrid;
use kartik\grid\CheckboxColumn;
use kartik\icons\Icon;

use linex\base\BaseModule;
use linex\base\modules\user\UserModule;
use linex\base\modules\dashboard\widgets\removeallbutton\RemoveAllButton;

?>
<div class="rbac-roles">
    <?=
    DynaGrid::widget([
        'options'            => [
            'id' => 'rbac-roles-grid',
        ],
        'columns'            => [
            [
                'class'   => CheckboxColumn::className(),
                'options' => [
                    'width' => '10px',
                ],
            ],
            [
                'attribute' => 'name',
                'label'     => UserModule::t('Name'),
                'value'     => function ($model) {
                    return Html::a($model->name, ['edit', 'id' => $model->name]);
                },
                'format'    => 'raw',
                'options'   => [
                    'width' => '30%',
                ],
            ],
            [
                'attribute' => 'description',
                'label'     => UserModule::t('Description'),
            ],
            [
                'attribute' => 'ruleName',
                'visible'   => $isRules,
            ],
            [
                'attribute' => 'createdAt',
                'label'     => UserModule::t('Created At'),
                'value'     => function ($data) {
                    return date("Y-m-d H:i:s", $data->createdAt);
                },
                'options'   => [
                    'width' => '200px',
                ],
            ],
            [
                'attribute' => 'updatedAt',
                'label'     => UserModule::t('Updated At'),
                'value'     => function ($data) {
                    return date("Y-m-d H:i:s", $data->updatedAt);
                },
                'options'   => [
                    'width' => '200px',
                ],
            ],
            [
                'class'    => 'kartik\grid\ActionColumn',
                'template' => '<div class="btn-group">{update}{delete}</div>',
                'buttons'  => [
                    'update' => function ($url, $model) {
                        return Html::a(Icon::show('pencil'), ['edit', 'id' => $model->name], ['class' => 'btn btn-primary btn-sm', 'title' => BaseModule::t('Edit')]);
                    },
                    'delete' => function ($url, $model) {
                        return Html::a(Icon::show('trash'), ['delete', 'id' => $model->name], ['class' => 'btn btn-danger btn-sm', 'title' => BaseModule::t('Delete'), 'data-method' => 'post']);
                    },
                ],
            ],
        ],
        'theme'              => 'panel-default',
        'gridOptions'        => [
            'dataProvider' => $roles,
            'hover'        => true,
            'panel'        => [
                'before' => Html::a(Icon::show('plus') . BaseModule::t('Add'), ['add'], ['class' => 'btn btn-primary btn-flat']),
                'after'  => RemoveAllButton::widget([
                    'url'          => '/dashboard/users/rbac/roles/delete-all',
                    'gridSelector' => '.grid-view',
                    'htmlOptions'  => [
                        'class' => 'btn btn-danger btn-flat',
                    ],
                ]),
            ],
        ],
        'allowSortSetting'   => false,
        'allowThemeSetting'  => false,
        'allowFilterSetting' => false,
    ]);
    ?>
</div>
