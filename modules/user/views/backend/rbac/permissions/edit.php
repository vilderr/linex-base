<?php
/**
 * @var $this     \yii\web\View
 * @var $model    \linex\base\modules\user\models\backend\AuthItem
 * @var $rules    array
 * @var $items    array
 * @var $children array
 */

?>
<div class="row rbac-permission-edit">
    <div class="col-md-7">
        <div class="panel panel-default flat widget">
            <?= $this->render('_form', [
                'model'    => $model,
                'items'    => $items,
                'children' => $children,
            ]) ?>
        </div>
    </div>
</div>
