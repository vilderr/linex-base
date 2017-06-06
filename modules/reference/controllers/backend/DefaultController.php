<?php

namespace linex\base\modules\reference\controllers\backend;

use linex\base\modules\reference\components\backend\Controller;

/**
 * Class DefaultController
 * @package linex\base\modules\reference\controllers\backend
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $this->view->title = 'Типы справочников';

        return $this->render('index');
    }
}
