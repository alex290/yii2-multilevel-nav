<?php


declare(strict_types=1);

namespace alex290\multinav;

use alex290\multinav\assets\Asset;
use Exception;
use Throwable;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * Nav renders a nav HTML component.
 *
 * For example:
 *
 * ```php
 * echo Nav::widget([
 *     'items' => [
 *         [
 *             'label' => 'Home',
 *             'url' => ['site/index'],
 *             'linkOptions' => [...],
 *         ],
 *         [
 *             'label' => 'Dropdown',
 *              'items' => [
 *                  [
 *                      'label' => 'Dropdown',
 *                      'items' => [
 *                          ['label' => 'Level 1 - Dropdown A', 'url' => '#'],
 *                          ['label' => 'Level 1 - Dropdown B', 'url' => '#'],
 *                      ],
 *                  ],
 *                  ['label' => 'Level 1 - Dropdown A', 'url' => '#'],
 *                  ['label' => 'Level 1 - Dropdown B', 'url' => '#'],
 *              ],
 *         ],
 *     ],
 *     'options' => ['class' =>'nav-pills'], // set this to nav-tabs to get tab-styled navigation
 * ]);
 * ```

 */

$fff = [
    'label' => 'Dropdown',
    'items' => [
        [
            'label' => 'Dropdown',
            'items' => [
                ['label' => 'Level 1 - Dropdown A', 'url' => '#'],
                ['label' => 'Level 1 - Dropdown B', 'url' => '#'],
            ],
        ],
        ['label' => 'Level 1 - Dropdown A', 'url' => '#'],
        ['label' => 'Level 1 - Dropdown B', 'url' => '#'],
    ],
];
class Nav extends Widget
{
    /**
     * @var array list of items in the nav widget. Each array element represents a single
     * menu item which can be either a string or an array with the following structure:
     *
     * - label: string, required, the nav item label.
     * - url: optional, the item's URL. Defaults to "#".
     * - visible: bool, optional, whether this menu item is visible. Defaults to true.
     * - disabled: bool, optional, whether this menu item is disabled. Defaults to false.
     * - linkOptions: array, optional, the HTML attributes of the item's link.
     * - options: array, optional, the HTML attributes of the item container (LI).
     * - active: bool, optional, whether the item should be on active state or not.
     * - dropdownOptions: array, optional, the HTML options that will passed to the [[Dropdown]] widget.
     * - items: array|string, optional, the configuration array for creating a [[Dropdown]] widget,
     *   or a string representing the dropdown menu. Note that Bootstrap does not support sub-dropdown menus.
     * - encode: bool, optional, whether the label will be HTML-encoded. If set, supersedes the $encodeLabels option for only this item.
     *
     * If a menu item is a string, it will be rendered directly without HTML encoding.
     */
    public $items = [];

    public $options = null;
    /**
     * @var bool whether the nav items labels should be HTML-encoded.
     */
    public $encodeLabels = true;
    /**
     * @var bool whether to automatically activate items according to whether their route setting
     * matches the currently requested route.
     * @see isItemActive
     */
    public $activateItems = true;
    /**
     * @var bool whether to activate parent menu items when one of the corresponding child menu items is active.
     */
    public $activateParents = false;
    /**
     * @var string|null the route used to determine if a menu item is active or not.
     * If not set, it will use the route of the current request.
     * @see params
     * @see isItemActive
     */
    public $route = null;
    /**
     * @var array|null the parameters used to determine if a menu item is active or not.
     * If not set, it will use `$_GET`.
     * @see route
     * @see isItemActive
     */
    public $params = null;
    /**
     * @var string name of a class to use for rendering dropdowns within this widget. Defaults to [[Dropdown]].
     */



    public function init()
    {
        parent::init();
        if ($this->route === null && Yii::$app->controller !== null) {
            $this->route = Yii::$app->controller->getRoute();
        }
        if ($this->params === null) {
            $this->params = Yii::$app->request->getQueryParams();
        }
        Html::addCssClass($this->options, ['widget' => 'nav']);
    }


    public function run(): string
    {
        Asset::register($this->getView());

        return $this->renderItems();
    }


    public function renderItems(): string
    {
        $items = [];
        foreach ($this->items as $item) {
            if (isset($item['visible']) && !$item['visible']) {
                continue;
            }
            // 
            $items[] = $this->renderItem($item);
        }
        // debug($items);

        return '<div class="cm-e-menu">' . Html::tag('ul', implode("\n", $items), $this->options) . '</div>';
    }


    public function renderItem($item): string
    {
        if (is_string($item)) {
            return $item;
        }
        if (!isset($item['label'])) {
            throw new InvalidConfigException("The 'label' option is required.");
        }
        $encodeLabel = $item['encode'] ?? $this->encodeLabels;
        $label = $encodeLabel ? Html::encode($item['label']) : $item['label'];
        $options = ArrayHelper::getValue($item, 'options', []);
        $items = ArrayHelper::getValue($item, 'items');
        $url = ArrayHelper::getValue($item, 'url');
        $linkOptions = ArrayHelper::getValue($item, 'linkOptions', []);
        $active = $this->isItemActive($item);

        $htmlRet = '';

        if ($this->activateItems && $active) {
            Html::addCssClass($options, ['activate' => 'active']);
        }
        Html::addCssClass($options, ['widget' => 'topmenu nav-link']);
        Html::addCssClass($linkOptions, ['widget' => 'text-light']);

        if (empty($items)) {
            $items = '';

            $htmlRet = Html::tag('li', Html::a($label, $url, $linkOptions), $options);
        } else {
            if (is_array($items)) {
                // $items = $this->isChildActive($items, $active);
                $htmlRet = $htmlRet = Html::tag('li', Html::a($label, $url, $linkOptions) . $this->renderDropdown($items), $options);
            }
        }

        return $htmlRet;
    }


    protected function renderDropdown(array $items): string
    {
        $htmlRet = '<ul class="submenu">';
        foreach ($items as $key => $item) {
            $encodeLabel = $item['encode'] ?? $this->encodeLabels;
            $label = $encodeLabel ? Html::encode($item['label']) : $item['label'];
            $options = ArrayHelper::getValue($item, 'options', []);
            $itemsChild = ArrayHelper::getValue($item, 'items');
            $url = ArrayHelper::getValue($item, 'url');
            $linkOptions = ArrayHelper::getValue($item, 'linkOptions', []);
            $disabled = ArrayHelper::getValue($item, 'disabled', false);
            $active = $this->isItemActive($item);
            Html::addCssClass($linkOptions, ['widget' => 'text-light']);
            Html::addCssClass($options, ['widget' => 'nav-link']);

            if ($active) {
                Html::addCssClass($options, ['activate' => 'active']);
            }

            if (empty($itemsChild)) {
                $htmlRet = $htmlRet .  Html::tag('li', Html::a($label, $url, $linkOptions), $options);
            } else {
                $htmlRet = $htmlRet .  Html::tag('li', Html::a($label, $url, $linkOptions) . $this->renderDropdown($itemsChild), $options);
            }
        }
        $htmlRet = $htmlRet . '</ul>';
        return $htmlRet;
    }


    protected function isItemActive(array $item): bool
    {
        $itemsChild = ArrayHelper::getValue($item, 'items');

        if (!$this->activateItems) {
            return false;
        }
        $thRoute = $this->route;
        if ($thRoute === "site/index")
            $thRoute = "/";

        if (isset($item['active'])) {
            return ArrayHelper::getValue($item, 'active', false);
        }
        if (isset($item['url']) && is_array($item['url']) && isset($item['url'][0])) {
            $route = $item['url'][0];
            if ($route == $thRoute) {
                return true;
            }
        }

        $actChild = false;

        if (!empty($itemsChild)) {
            foreach ($itemsChild as $valueChild) {
                if (!$actChild)
                    $actChild = $this->isItemActive($valueChild);
            }
        }

        return $actChild;
    }
}
