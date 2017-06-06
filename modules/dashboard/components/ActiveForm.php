<?php

namespace linex\base\modules\dashboard\components;

/**
 * Class ActiveForm
 * @package linex\base\modules\dashboard\components
 */
class ActiveForm extends \kartik\form\ActiveForm
{
    public function initForm()
    {
        if (!isset($this->fieldConfig['class'])) {
            $this->fieldConfig['class'] = ActiveField::className();
        }
        parent::initForm();
    }
}