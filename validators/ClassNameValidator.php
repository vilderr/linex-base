<?php
/**
 * Created by PhpStorm.
 * User: vilderr
 * Date: 02.06.17
 * Time: 16:41
 */

namespace linex\base\validators;

use Yii;
use yii\validators\Validator;

/**
 * Class ClassNameValidator
 * @package linex\base\validators
 */
class ClassNameValidator extends Validator
{
    /**
     * @inheritdoc
     * @return null|array
     */
    public function validateValue($value)
    {
        if (class_exists($value) === false) {
            return [
                Yii::t('app', 'Unable to find specified class.'),
                [],
            ];
        } else {
            return null;
        }
    }
}