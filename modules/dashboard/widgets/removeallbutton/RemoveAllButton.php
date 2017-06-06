<?php

namespace linex\base\modules\dashboard\widgets\removeallbutton;

use yii\base\Widget;
use yii\base\InvalidParamException;
use yii\helpers\Html;
use kartik\icons\Icon;
use linex\base\BaseModule;

/**
 * Class RemoveAllButton
 * @package linex\base\modules\dashboard\widgets\removeallbutton
 */
class RemoveAllButton extends Widget
{
    public $url;
    public $gridSelector;
    public $htmlOptions = [];
    public $modalSelector = '#delete-confirmation';

    public function init()
    {
        if (!isset($this->url, $this->gridSelector)) {
            throw new InvalidParamException('Attribute \'url\' or \'gridSelector\' is not set');
        }

        if (!isset($this->htmlOptions['id'])) {
            $this->htmlOptions['id'] = 'deleteItems';
        }
        Html::addCssClass($this->htmlOptions, 'btn');
    }

    /**
     * @return string
     */
    public function run()
    {
        $this->registerScript();

        return $this->renderButton();
    }

    /**
     * @return string
     */
    protected function renderButton()
    {
        return Html::button(
            Icon::show('trash-o') . ' ' .
            BaseModule::t('Delete selected'),
            $this->htmlOptions
        );
    }

    protected function registerScript()
    {
        $view = $this->getView();
        Asset::register($view);

        $this->view->registerJs("
            jQuery('#{$this->htmlOptions['id']}').on('click', function() {
                var items =  $('{$this->gridSelector}').yiiGridView('getSelectedRows');
                if (items.length) {
                    jQuery('{$this->modalSelector}').attr('data-url', '{$this->url}').attr('data-items', items).modal('show');
                }
                return false;
            });
        ");
    }
}