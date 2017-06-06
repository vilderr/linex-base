<?php

namespace linex\base\modules\dashboard\widgets;

use Closure;
use Yii;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

class BackendMenu extends Widget
{
    /**
     * @var array list of menu items. Each menu item should be an array of the following structure:
     *
     * - label: string, optional, specifies the menu item label. When [[encodeLabels]] is true, the label
     *   will be HTML-encoded. If the label is not specified, an empty string will be used.
     * - encode: boolean, optional, whether this item`s label should be HTML-encoded. This param will override
     *   global [[encodeLabels]] param.
     * - url: string or array, optional, specifies the URL of the menu item. It will be processed by [[Url::to]].
     *   When this is set, the actual menu item content will be generated using [[linkTemplate]];
     *   otherwise, [[labelTemplate]] will be used.
     * - visible: boolean, optional, whether this menu item is visible. Defaults to true.
     * - items: array, optional, specifies the sub-menu items. Its format is the same as the parent items.
     * - active: boolean or Closure, optional, whether this menu item is in active state (currently selected).
     *   When using a closure, its signature should be `function ($item, $hasActiveChild, $isItemActive, $widget)`.
     *   Closure must return `true` if item should be marked as `active`, otherwise - `false`.
     *   If a menu item is active, its CSS class will be appended with [[activeCssClass]].
     *   If this option is not set, the menu item will be set active automatically when the current request
     *   is triggered by `url`. For more details, please refer to [[isItemActive()]].
     * - template: string, optional, the template used to render the content of this menu item.
     *   The token `{url}` will be replaced by the URL associated with this menu item,
     *   and the token `{label}` will be replaced by the label of the menu item.
     *   If this option is not set, [[linkTemplate]] or [[labelTemplate]] will be used instead.
     * - submenuTemplate: string, optional, the template used to render the list of sub-menus.
     *   The token `{items}` will be replaced with the rendered sub-menu items.
     *   If this option is not set, [[submenuTemplate]] will be used instead.
     * - options: array, optional, the HTML attributes for the menu container tag.
     */
    public $items = [];
    /**
     * @var array list of HTML attributes shared by all menu [[items]]. If any individual menu item
     * specifies its `options`, it will be merged with this property before being used to generate the HTML
     * attributes for the menu item tag. The following special options are recognized:
     *
     * - tag: string, defaults to "li", the tag name of the item container tags.
     *   Set to false to disable container tag.
     *   See also [[\yii\helpers\Html::tag()]].
     *
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $itemOptions = [];
    /**
     * @var string the template used to render the body of a menu which is a link.
     * In this template, the token `{url}` will be replaced with the corresponding link URL;
     * while `{label}` will be replaced with the link text.
     * This property will be overridden by the `template` option set in individual menu items via [[items]].
     */
    public $linkTemplate = '<a href="{url}"><i class="fa fa-{icon}"></i><span>{label}</span></a>';
    /**
     * @var string the template used to render a list of sub-menus.
     * In this template, the token `{items}` will be replaced with the rendered sub-menu items.
     */
    public $submenuTemplate = "\n<ul>\n{items}\n</ul>\n";
    /**
     * @var array the HTML attributes for the menu's container tag. The following special options are recognized:
     *
     * - tag: string, defaults to "ul", the tag name of the item container tags. Set to false to disable container tag.
     *   See also [[\yii\helpers\Html::tag()]].
     *
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $options = [];
    /**
     * @var bool whether the labels for menu items should be HTML-encoded.
     */
    public $encodeLabels = true;
    /**
     * @var string the CSS class to be appended to the active menu item.
     */
    public $activeCssClass = 'active';
    /**
     * @var bool whether to automatically activate items according to whether their route setting
     * matches the currently requested route.
     * @see isItemActive()
     */
    public $activateItems = true;
    /**
     * @var bool whether to activate parent menu items when one of the corresponding child menu items is active.
     * The activated parent menu items will also have its CSS classes appended with [[activeCssClass]].
     */
    public $activateParents = true;
    /**
     * @var bool whether to hide empty menu items. An empty menu item is one whose `url` option is not
     * set and which has no visible child menu items.
     */
    public $hideEmptyItems = true;
    /**
     * @var string the route used to determine if a menu item is active or not.
     * If not set, it will use the route of the current request.
     * @see params
     * @see isItemActive()
     */
    public $route;

    /**
     * Executes the widget
     */
    public function run()
    {
        if ($this->route === null && Yii::$app->controller !== null) {
            $this->route = Yii::$app->controller->getRoute();
        }

        $items = $this->normalizeItems($this->items, $hasActiveChild);
        if (!empty($items)) {
            $options = $this->options;
            $tag = ArrayHelper::remove($options, 'tag', 'ul');

            echo Html::tag($tag, $this->renderItems($items), $options);
        }
    }

    /**
     * @param $items
     *
     * @return string
     */
    public function renderItems($items)
    {
        $lines = [];
        foreach ($items as $i => $item) {
            $options = array_merge($this->itemOptions, ArrayHelper::getValue($item, 'options', []));
            $tag = ArrayHelper::remove($options, 'tag', 'li');
            $class = [];

            if ($item['active']) {
                $class[] = $this->activeCssClass;
            }
            Html::addCssClass($options, $class);
            $menu = $this->renderItem($item);
            if (!empty($item['items'])) {
                $submenuTemplate = ArrayHelper::getValue($item, 'submenuTemplate', $this->submenuTemplate);
                $menu .= strtr($submenuTemplate, [
                    '{items}' => $this->renderItems($item['items']),
                ]);
            }
            $lines[] = Html::tag($tag, $menu, $options);
        }

        return implode("\n", $lines);
    }

    /**
     * Renders the content of a menu item.
     * Note that the container and the sub-menus are not rendered here.
     *
     * @param array $item the menu item to be rendered. Please refer to [[items]] to see what data might be in the item.
     *
     * @return string the rendering result
     */
    protected function renderItem($item)
    {
        $template = ArrayHelper::getValue($item, 'template', $this->linkTemplate);

        return strtr(
            $template,
            [
                '{url}'   => isset($item['url']) ? Url::to($item['url']) : '#',
                '{label}' => $item['label'],
                '{icon}'  => isset($item['icon']) ? $item['icon'] : 'angle-right',
            ]
        );
    }

    /**
     * @param $items
     * @param $active
     *
     * @return array
     */
    protected function normalizeItems($items, &$active)
    {
        foreach ($items as $i => $item) {
            if (isset($item['visible']) && !$item['visible']) {
                unset($items[$i]);
                continue;
            }
            if (!isset($item['label'])) {
                $item['label'] = '';
            }

            $encodeLabel = isset($item['encode']) ? $item['encode'] : $this->encodeLabels;
            $items[$i]['label'] = $encodeLabel ? Html::encode($item['label']) : $item['label'];
            $hasActiveChild = false;
            if (isset($item['items'])) {
                $items[$i]['items'] = $this->normalizeItems($item['items'], $hasActiveChild);
                if (empty($items[$i]['items']) && $this->hideEmptyItems) {
                    unset($items[$i]['items']);
                    if (!isset($item['url'])) {
                        unset($items[$i]);
                        continue;
                    }
                }
            }

            if (!isset($item['active'])) {
                if ($this->activateParents && $hasActiveChild || $this->activateItems && $this->isItemActive($item)) {
                    $active = $items[$i]['active'] = true;
                } else {
                    $items[$i]['active'] = false;
                }
            } elseif ($item['active'] instanceof Closure) {
                $active = $items[$i]['active'] = call_user_func($item['active'], $item, $hasActiveChild, $this->isItemActive($item), $this);
            } elseif ($item['active']) {
                $active = true;
            }
        }

        return array_values($items);
    }

    /**
     * Checks whether a menu item is active.
     * This is done by checking if [[route]] and [[params]] match that specified in the `url` option of the menu item.
     * When the `url` option of a menu item is specified in terms of an array, its first element is treated
     * as the route for the item and the rest of the elements are the associated parameters.
     * Only when its route and parameters match [[route]] and [[params]], respectively, will a menu item
     * be considered active.
     *
     * @param array $item the menu item to be checked
     *
     * @return bool whether the menu item is active
     */
    protected function isItemActive($item)
    {
        if (isset($item['url']) && is_array($item['url']) && isset($item['url'][0])) {
            $route = Yii::getAlias($item['url'][0]);
            if ($route[0] !== '/' && Yii::$app->controller) {
                $route = Yii::$app->controller->module->getUniqueId() . '/' . $route;
            }

            if (false === strpos($this->route, ltrim($route, '/'))) {
                return false;
            }

            unset($item['url']['#']);

            return true;
        }

        return false;
    }
}