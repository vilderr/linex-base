<?php

namespace linex\base\behaviors;

use linex\base\BaseModule;
use yii\base\Behavior;
use yii\db\ActiveRecord;

/**
 * Запрет на удаление системных записей
 *
 * Class BanDelete
 * @package linex\base\behaviors
 */
class BanDelete extends Behavior
{
    public $message = 'You can not remove system line';

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_DELETE => 'beforeDelete',
        ];
    }

    public function beforeDelete($event)
    {
        if ($this->owner->added_by === 'core') {
            \Yii::$app->session->setFlash('error', $this->message);
            $event->isValid = false;
        } else {
            $event->isValid = true;
        }
    }
}