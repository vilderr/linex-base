<?php

namespace linex\base\components;

use yii\rbac\DbManager;

/**
 * Class CachedDbRbacManager
 * @package linex\base\components
 */
class CachedDbRbacManager extends DbManager
{
    /**
     * @var array
     */
    private static $assignmentsByUserId = [];

    /**
     * @inheritdoc
     */
    public function getAssignments($userId)
    {
        if (isset(static::$assignmentsByUserId[$userId]) === false) {
            static::$assignmentsByUserId[$userId] = parent::getAssignments($userId);
        }

        return static::$assignmentsByUserId[$userId];
    }

    public function invalidateCache()
    {
        static::$assignmentsByUserId = [];
        parent::invalidateCache();
    }
}