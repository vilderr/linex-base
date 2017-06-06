<?php

namespace linex\base\modules\dashboard\controllers;

use linex\base\components\BackendController;
use linex\base\modules\dashboard\widgets\redactor\actions\GetAction;

/**
 * Class RedactorController
 * @package linex\base\modules\dashboard\controllers
 */
class RedactorController extends BackendController
{
    /**
     * @return array
     */
    public function actions()
    {
        return [
            'image-upload' => [
                'class' => 'linex\base\modules\dashboard\widgets\redactor\actions\UploadAction',
                'url'   => '/upload/redactor',
                'path'  => '@uploads/redactor' // Or absolute path to directory where files are stored.
            ],
            'images-get'   => [
                'class' => 'linex\base\modules\dashboard\widgets\redactor\actions\GetAction',
                'url'   => '/upload/redactor',
                'path'  => '@uploads/redactor', // Or absolute path to directory where files are stored.
                'type'  => GetAction::TYPE_IMAGES,
            ],
        ];
    }
}