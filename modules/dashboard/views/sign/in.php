<?php
/**
 * @var $this  \yii\web\View
 * @var $model \linex\base\modules\user\models\forms\LoginForm
 */

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use linex\base\modules\user\UserModule;
?>
<div class="dashboard-login">
    <?
    $form = ActiveForm::begin([
        'id'               => 'dashboard-login',
        'errorCssClass'    => 'error',
        'successCssClass'  => 'sucess',
        'fieldConfig'      => [
            'template' => "{label}{input}",
        ],
    ]);
    ?>
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <?= $form->field($model, 'username'); ?>
            <?= $form->field($model, 'password')->passwordInput(); ?>
            <?= $form->field($model, 'rememberMe', ['template' => '{input}{label}', 'options' => ['class' => 'form-group one-checkbox']])->checkbox(['class' => 'checkbox'], false); ?>
        </div>
    </div>
    <div class="controls text-center">
        <?= Html::submitButton(UserModule::t('Sign In'), ['class' => 'btn btn-default btn-lg btn-flat', 'name' => 'next']); ?>
    </div>
    <? ActiveForm::end(); ?>
</div>
