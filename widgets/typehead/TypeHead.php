<?php

namespace linex\base\widgets\typehead;

use yii\widgets\InputWidget;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\base\Widget;
use yii\web\JsExpression;

/**
 * Class TypeHead
 * @package linex\base\widgets\typehead
 */
class TypeHead extends InputWidget
{
    public $clientOptions = [];
    public $typeaHeadOptions = [];

    public $scrollable = false;

    public $addon = [];

    public $limit = 5;

    protected $hasGroup = false;

    public $groupOptions = [];

    public $source = [];

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function __set($name, $value)
    {
        if (isset($this->{$name}))
            parent::__set($name, $value);
        else
            $this->clientOptions[$name] = $value;
    }

    public function init()
    {
        if (!empty($this->addon))
            $this->hasGroup = true;

        if (!$this->hasModel() && $this->name === null) {
            throw new InvalidConfigException("Either 'name', or 'model' and 'attribute' properties must be specified.");
        }

        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->hasModel() ? Html::getInputId($this->model, $this->attribute) : $this->getId();
        }
        Html::addCssClass($this->options, 'typeahead');
        Html::addCssClass($this->options, 'form-control');
    }

    public function run()
    {
        $this->registerPlugin($this->options['id']);

        return $this->renderInput();
    }

    /**
     * @return string
     */
    protected function renderInput()
    {
        if (isset($this->size) && in_array($this->size, ['sm', 'lg'])) {
            Html::addCssClass($this->options, 'input-' . $this->size);
            Html::addCssClass($this->groupOptions, 'input-group-' . $this->size);
        }

        $input = $this->hasModel() ?
            Html::activeTextInput($this->model, $this->attribute, $this->options) :
            Html::textInput($this->name, $this->value, $this->options);

        $this->groupOptions['id'] = $this->options['id'] . '_typeahead';

        if ($this->scrollable)
            Html::addCssClass($this->groupOptions, 'scrollable');

        if ($this->hasGroup) {
            Html::addCssClass($this->groupOptions, 'input-group');
            $input = Html::tag('div', $this->prepareAddon($input), $this->groupOptions);
        } else
            $input = Html::tag('div', $input, $this->groupOptions);

        return $input;
    }

    protected function prepareAddon($input)
    {
        extract($this->addon);

        $template = "{prepend}\n{input}\n{append}";
        $appendContent = [];
        $prependContent = [];

        if (isset($append)) {
            if (is_array($append)) {
                if (is_string($append[0]) && is_bool($append[1])) {
                    if ($append[1] == true)
                        $appendContent[] = Html::tag('span', $append[0], ['class' => 'input-group-btn']);
                } else {
                    foreach ($append as $content) {
                        $appendContent[] = $this->prepareContent($content);
                    }
                }
            } else {
                $appendContent[] = Html::tag('span', $append, ['class' => 'input-group-addon']);
            }
        }
        if (isset($prepend)) {
            if (is_array($prepend)) {
                if (is_string($prepend[0]) && is_bool($prepend[1])) {
                    if ($prepend[1] == true)
                        $prependContent[] = Html::tag('span', $prepend[0], ['class' => 'input-group-btn']);
                } else {
                    foreach ($prepend as $content) {
                        $prependContent[] = $this->prepareContent($content);
                    }
                }
            } else {
                $prependContent[] = Html::tag('span', $prepend, ['class' => 'input-group-addon']);
            }
        }

        return strtr($template, [
            '{prepend}' => implode('', $prependContent),
            '{input}'   => $input,
            '{append}'  => implode('', $appendContent),
        ]);
    }

    protected function prepareContent($content)
    {
        if (is_array($content)) {
            if (!isset($content['class']) && isset($content[0]))
                $content['class'] = ArrayHelper::remove($content, 0);
            if (!isset($content['options']) && isset($content[1])) {
                if (is_bool($content[1]))
                    $content['asButton'] = ArrayHelper::remove($content, 1);
                else
                    $content['options'] = ArrayHelper::remove($content, 1);
            }
            if (!isset($content['asButton']))
                $content['asButton'] = ArrayHelper::remove($content, 2, false);
            $class = $content['class'];
            $options = isset($content['options']) ? $content['options'] : [];
            $asButton = $content['asButton'];

            if ($asButton)
                Html::addCssClass($tagOptions, 'input-group-btn');
            else
                Html::addCssClass($tagOptions, 'input-group-addon');

            if (class_exists($class)) {
                $inContent = $class::widget($options);
            } else {
                $inContent = $class;
            }

            $content = Html::tag('span', $inContent, $tagOptions);
        } else {
            if ($content instanceof Widget)
                Html::addCssClass($tagOptions, 'input-group-btn');
            else
                Html::addCssClass($tagOptions, 'input-group-addon');
            $content = Html::tag('span', $content, $tagOptions);
        }

        return $content;
    }

    protected function registerPlugin($selector)
    {
        $view = $this->getView();

        Assets::register($view);

        if ($this->limit == false)
            $this->limit = count($this->source);

        $selectored = str_replace('-', '_', $selector);
        $options1 = JSON::encode($this->clientOptions);
        $options2 = Json::encode(['name' => $selector, 'displayKey' => 'value', 'source' => new JsExpression("$selectored.ttAdapter()")]);
        $js = "var {$selectored}_data = " . JSON::encode($this->source) . ";" . PHP_EOL;

        $js .= "var {$selectored} = new Bloodhound({
		limit:{$this->limit},
		datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
		queryTokenizer: Bloodhound.tokenizers.whitespace,
		local: $.map({$selectored}_data, function(data) { return { value: data }; })\n});" . PHP_EOL;

        $js .= "{$selectored}.initialize();" . PHP_EOL;

        $js .= "jQuery('#{$selector}_typeahead .typeahead').typeahead({$options1},{$options2});";

        $view->registerJs($js);
    }
}