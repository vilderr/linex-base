<?php
/**
 * @var $this  \yii\web\View
 * @var $model \linex\base\modules\dashboard\models\BackendMenu
 */
?>
<div class="row">
    <div class="col-md-6">
        <div class="panel panel-default flat">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
</div>
