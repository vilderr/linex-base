<?php
/**
 * @var $this        \yii\web\View
 * @var $model       \linex\base\modules\user\models\backend\AuthItem
 * @var $items       array
 * @var $children    array
 */

use yii\helpers\Html;

use kartik\form\ActiveForm;
use kartik\icons\Icon;

use linex\base\BaseModule;
use linex\base\modules\dashboard\widgets\multiselect\MultiSelect;

?>
<?php $form = ActiveForm::begin([
    'id'         => 'edit-rbac-permission-form',
    'type'       => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL],
]); ?>
<div class="panel-body">
    <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'description')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'oldname', ['template' => '{input}'])->input('hidden'); ?>
    <?= $form->field($model, 'type', ['template' => '{input}'])->input('hidden'); ?>
    <?= MultiSelect::widget([
        'items'         => $items,
        'selectedItems' => $children,
        'ajax'          => false,
        'name'          => 'AuthItem[children][]',
        'label'         => $model->getAttributeLabel('children'),
        'defaultLabel'  => BaseModule::t('Choose item'),
    ]); ?>
</div>
<div class="panel-footer">
    <?=
    Html::a(
        Icon::show('arrow-circle-left') . BaseModule::t('Back'),
        Yii::$app->request->get('returnUrl', ['index']),
        ['class' => 'btn btn-danger btn-flat']
    )
    ?>
    <?=
    Html::submitButton(
        Icon::show('save') . BaseModule::t('Save'),
        [
            'class' => 'btn btn-primary btn-flat',
            'name'  => 'action',
            'value' => 'back',
        ]
    )
    ?>
    <?= Html::submitButton(
        Icon::show('save') . BaseModule::t('Apply'),
        [
            'class' => 'btn btn-warning btn-flat',
            'name'  => 'action',
            'value' => 'save',
        ]
    ); ?>
</div>
<?php ActiveForm::end(); ?>
