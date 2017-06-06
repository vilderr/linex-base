<?php
/**
 * @var $this        \yii\web\View
 * @var $model       \linex\base\modules\user\models\User
 * @var $assignments \yii\rbac\Assignment[]
 */

use yii\helpers\Html;
use kartik\form\ActiveForm;
use kartik\icons\Icon;
use linex\base\BaseModule;
use linex\base\modules\user\UserModule;
use linex\base\modules\dashboard\widgets\multiselect\MultiSelect;

?>
<?php $form = ActiveForm::begin([
    'id'         => 'edit-user-form',
    'type'       => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL],
]); ?>
<div class="panel-body">
    <?= $form->field($model, 'username')->textInput(['maxlength' => 255, 'autocomplete' => 'off']) ?>
    <?= $form->field($model, 'first_name')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'last_name')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'password')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'status')->dropDownList($model->getStatusList()) ?>
    <?= $form->field($model, 'email')->textInput(['maxlength' => 255]) ?>
    <?= MultiSelect::widget([
        'items'         => \yii\helpers\ArrayHelper::map(
            \Yii::$app->getAuthManager()->getRoles(),
            'name',
            function ($item) {
                return $item->name . (strlen($item->description) > 0
                        ? ' [' . $item->description . ']'
                        : '');
            }
        ),
        'selectedItems' => $model->isNewRecord ? [] : $assignments,
        'ajax'          => false,
        'name'          => 'AuthAssignment[]',
        'label'         => UserModule::t('Roles'),
        'defaultLabel'  => BaseModule::t('Choose item'),
    ]);
    ?>
</div>
<div class="panel-footer">
    <?=
    Html::a(
        Icon::show('arrow-circle-left') . BaseModule::t('Back'),
        Yii::$app->request->get('returnUrl', ['index']),
        ['class' => 'btn btn-danger btn-flat']
    )
    ?>
    <?= Html::submitButton(
        Icon::show('save') . BaseModule::t('Save'),
        [
            'class' => 'btn btn-primary btn-flat',
            'name'  => 'action',
            'value' => 'back',
        ]
    ); ?>
    <?=
    Html::submitButton(
        Icon::show('save') . BaseModule::t('Apply'),
        [
            'class' => 'btn btn-warning btn-flat',
            'name'  => 'action',
            'value' => 'save',
        ]
    )
    ?>
</div>
<?php ActiveForm::end(); ?>

