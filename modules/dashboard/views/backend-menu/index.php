<?php
/**
 * @var $this         \yii\web\View
 * @var $dataProvider \yii\data\ActiveDataProvider
 * @var $model        \linex\base\modules\dashboard\models\BackendMenu
 * @var $navChain     \linex\base\modules\dashboard\models\BackendMenu[]
 */

use yii\helpers\Html;

use kartik\icons\Icon;
use kartik\dynagrid\DynaGrid;
use kartik\grid\CheckboxColumn;

use linex\base\BaseModule;
use linex\base\modules\dashboard\DashboardModule;
use linex\base\modules\dashboard\widgets\removeallbutton\RemoveAllButton;

?>
<?=
DynaGrid::widget([
    'options'           => [
        'id' => 'backend-menu-grid',
    ],
    'columns'           => [
        [
            'class'   => CheckboxColumn::className(),
            'options' => [
                'width' => '10px',
            ],
        ],
        [
            'class'     => 'yii\grid\DataColumn',
            'attribute' => 'id',
        ],
        [
            'attribute' => 'name',
            'value'     => function ($model) {
                return Html::a($model->name, \yii\helpers\Url::toRoute([
                    'index',
                    'parent_id' => $model->id,
                ]));
            },
            'format'    => 'raw',
        ],
        'sort',
        'route',
        'icon',
        'rbac_check',
        [
            'class'          => 'yii\grid\ActionColumn',
            'contentOptions' => [
                'class' => 'text-center',
            ],
            'template'       => '<div class="btn-group">{update}{delete}</div>',
            'buttons'        => [
                'update' => function ($url, $model) {
                    return Html::a('<i class="fa fa-pencil"></i>', ['edit', 'id' => $model->id, 'returnUrl' => \yii\helpers\Url::to(['index', 'parent_id' => $model->parent_id])], ['class' => 'btn btn-primary btn-sm', 'title' => BaseModule::t('Edit')]);
                },
                'delete' => function ($url, $model) {
                    return Html::a('<i class="fa fa-trash"></i>', ['delete', 'id' => $model->id], ['class' => 'btn btn-danger btn-sm', 'data-method' => 'post', 'title' => BaseModule::t('Delete')]);
                },
            ],
            'options'        => [
                'width' => '90px',
            ],
        ],
    ],
    'theme'             => 'panel-default',
    'gridOptions'       => [
        'dataProvider' => $dataProvider,
        'hover'        => true,
        'panel'        => [
            'heading' => DashboardModule::t('{item-name}: items', ['item-name' => DashboardModule::t($model->name, [],'Menu')]),
            'before'  => Html::a(Icon::show('plus') . BaseModule::t('Add'), ['add', 'parent_id' => $model->id], ['class' => 'btn btn-primary btn-flat']) . ' ' . (!$model->isNewRecord ? Html::a(Icon::show('reply') . BaseModule::t('Up to parent'), ['index', 'parent_id' => $model->parent_id], ['class' => 'btn btn-default btn-flat']) : ''),
            'after'   => RemoveAllButton::widget([
                'url'          => '/dashboard/backend-menu/delete-all?parent_id=' . $model->id,
                'gridSelector' => '.grid-view',
                'htmlOptions'  => [
                    'class' => 'btn btn-danger btn-flat',
                ],
            ]),
        ],

    ],
    'allowSortSetting'  => false,
    'allowThemeSetting' => false,
]);
?>
