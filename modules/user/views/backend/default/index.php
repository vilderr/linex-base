<?php
/**
 * @var $this         \yii\web\View
 * @var $searchModel  \linex\base\modules\user\models\backend\Search
 * @var $dataProvider \yii\data\ActiveDataProvider
 */

use yii\helpers\Html;
use kartik\icons\Icon;
use kartik\dynagrid\DynaGrid;
use kartik\date\DatePicker;
use yii\grid\CheckboxColumn;
use linex\base\BaseModule;
use linex\base\modules\dashboard\widgets\removeallbutton\RemoveAllButton;
use linex\base\modules\user\models\User;

?>
<div class="users-list">
    <?=
    DynaGrid::widget([
        'options'            => [
            'id' => 'users-grid',
        ],
        'columns'            => [
            [
                'class'   => CheckboxColumn::className(),
                'options' => [
                    'width' => '10px',
                ],
            ],
            'id',
            [
                'attribute' => 'username',
                'value'     => function ($model, $key, $index, $widget) {
                    return Html::a($model->username, ['edit', 'id' => $model->id]);
                },
                'format'    => 'raw',
            ],
            'email:email',
            [
                'attribute' => 'status',
                'filter'    => User::getStatusList(),
                'value'     => function ($data) {
                    return isset(User::getStatusList()[$data->status])
                        ? User::getStatusList()[$data->status]
                        : $data->status;
                },
            ],
            [
                'filter'        => DatePicker::widget([
                    'model'         => $searchModel,
                    'attribute'     => 'date_from',
                    'attribute2'    => 'date_to',
                    'type'          => DatePicker::TYPE_RANGE,
                    'separator'     => '-',
                    'pluginOptions' => ['format' => 'yyyy-mm-dd'],
                ]),
                'attribute'     => 'created_at',
                'format'        => 'datetime',
                'filterOptions' => [
                    'style' => 'max-width: 180px',
                ],
            ],
            [
                'class'    => 'kartik\grid\ActionColumn',
                'template' => '<div class="btn-group">{update}{delete}</div>',
                'buttons'  => [
                    'update' => function ($url, $model) {
                        return Html::a(Icon::show('pencil'), ['edit', 'id' => $model->id], ['class' => 'btn btn-primary btn-sm', 'title' => BaseModule::t('Edit')]);
                    },
                    'delete' => function ($url, $model) {
                        return Html::a(Icon::show('trash'), ['delete', 'id' => $model->id], ['class' => 'btn btn-danger btn-sm', 'title' => BaseModule::t('Delete'), 'data-method' => 'post']);
                    },
                ],
            ],
        ],
        'theme'              => 'panel-default',
        'gridOptions'        => [
            'dataProvider' => $dataProvider,
            'filterModel'  => $searchModel,
            'hover'        => true,
            'panel'        => [
                'before' => Html::a(Icon::show('plus') . BaseModule::t('Add'), ['add'], ['class' => 'btn btn-primary btn-flat']),
                'after'  => RemoveAllButton::widget([
                    'url'          => '/dashboard/users/delete-all',
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
