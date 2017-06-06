<?php
/**
 * @var $this  \yii\web\View
 * @var $model \linex\base\modules\dashboard\models\BackendMenu
 */

use yii\helpers\Html;
use kartik\form\ActiveForm;
use kartik\icons\Icon;
use linex\base\BaseModule;

?>
<?php $form = ActiveForm::begin([
    'id'         => 'backend-menu-form',
    'type'       => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL],
]); ?>

<div class="panel-body">
    <?= $form->field($model, 'name'); ?>
    <?= $form->field($model, 'route'); ?>
    <?= $form->field($model, 'icon'); ?>
    <?= $form->field($model, 'sort'); ?>
    <?= $form->field($model, 'rbac_check'); ?>
</div>
<div class="panel-footer">
    <?=
    Html::a(
        Icon::show('arrow-circle-left') . BaseModule::t('Back'),
        Yii::$app->request->get('returnUrl', ['/dashboard/backend-menu']),
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
