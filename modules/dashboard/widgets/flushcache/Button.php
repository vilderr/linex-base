<?php

namespace linex\base\modules\dashboard\widgets\flushcache;

use yii\base\Widget;
use yii\helpers\Url;

/**
 * Class Button
 * @package linex\base\modules\dashboard\widgets\flushcache
 */
class Button extends Widget
{
    public $url = '';
    public $htmlOptions = [];

    public $onSuccess = "''";
    public $onError = "''";

    public $label = "Flush cache";

    public function init()
    {
        parent::init();
        if (!$this->url) {
            $this->url = Url::to(['flush-cache']);
        }
        if (!isset($this->htmlOptions['class'])) {
            $this->htmlOptions['class'] = 'btn btn-warning';
        }
        $this->htmlOptions['id'] = 'flush_cache';
    }

    public function run()
    {
        $view = $this->getView();
        Asset::register($view);

        $view->registerJs(
            "jQuery('#{$this->htmlOptions['id']}').flushCache('{$this->url}', {$this->onSuccess}, {$this->onError});"
        );

        return $this->render('Button', ['htmlOptions' => $this->htmlOptions, 'label' => $this->label]);
    }
}