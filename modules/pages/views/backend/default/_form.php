<?php
/**
 * @var $this \yii\web\View
 * @var $model \linex\base\modules\pages\models\Page
 */

use yii\helpers\Url;
use yii\helpers\Html;

use kartik\icons\Icon;

use linex\base\BaseModule;
use linex\base\modules\dashboard\components\ActiveForm;
use linex\base\modules\dashboard\widgets\redactor\Widget;
?>
<div class="panel panel-default flat">
    <?php $form = ActiveForm::begin([
        'id'   => 'page-form',
        'type' => ActiveForm::TYPE_HORIZONTAL,
    ]); ?>
    <div class="panel-body">
        <?= $form->field($model, 'name')->textInput(['id' => 'page-title']); ?>
        <?= $form->field($model, 'slug', [
            'makeSlug' => "#page-title",
        ]); ?>
        <?= $form->field($model, 'content')->widget(Widget::className(), [
            'settings' => [
                'minHeight'        => 300,
                'maxHeight'        => 500,
                'imageManagerJson' => Url::to(['/dashboard/redactor/images-get']),
                'imageUpload'      => Url::to(['/dashboard/redactor/image-upload']),
                'plugins'          => [
                    'imagemanager',
                    'fullscreen',
                ],
            ],
        ]); ?>
        <?=$form->field($model, 'published')->checkbox()?>
        <?= $form->field($model, 'sort'); ?>
    </div>
    <div class="panel-footer">
        <?=
        Html::a(
            Icon::show('arrow-circle-left') . BaseModule::t('Back'),
            Yii::$app->request->get('returnUrl', ['/dashboard/pages']),
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
    <? ActiveForm::end(); ?>
</div>
