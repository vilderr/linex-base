<?php
/**
 * @var $this        \yii\web\View
 * @var $model       \linex\base\modules\user\models\User
 * @var $assignments \yii\rbac\Assignment[]
 */
?>
<div class="row edit-user">
    <div class="col-md-6">
        <div class="panel panel-default flat">
            <?= $this->render('_form', [
                'model'       => $model,
                'assignments' => $assignments,
            ]); ?>
        </div>
    </div>
</div>