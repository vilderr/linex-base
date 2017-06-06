<?php
/**
 * @var $this   \yii\web\View
 * @var $params array
 */
?>
<div class="multi-select form-group" id="<?= $id; ?>">
    <label class="control-label col-sm-3" for=""><?= $label; ?></label>
    <div class="col-sm-9">
        <?= \yii\helpers\Html::dropDownList('', null, $list, ['class' => 'form-control list', 'id' => 'rules']); ?>
        <br>
        <table class="table table-striped table-bordered table-condensed">
            <tbody>
            <tr class="hidden">
                <td></td>
                <td style="width: 30px;" class="text-center"><a href="#"
                                                                class="remove"><?= \kartik\icons\Icon::show('trash-o', ['class' => 'text-danger']) ?></a>
                </td>
            </tr>
            <?php $isFirst = true; ?>
            <?php foreach ($table as $dataId => $dataName): ?>
                <?php
                $class = '';
                if ($isFirst && $sortable) {
                    $class = 'success';
                    $isFirst = false;
                }
                ?>
                <tr data-id="<?= $dataId; ?>" class="<?= $class ?>">
                    <td><?= $dataName; ?></td>
                    <td style="width: 30px;" class="text-center"><a href="#"
                                                                    class="remove"><?= \kartik\icons\Icon::show('trash-o', ['class' => 'text-danger']) ?></a>
                    </td>
                    <?= \yii\helpers\Html::input('hidden', $name, $dataId) ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
