<?php

namespace linex\base\behaviors;

use Yii;
use yii\base\Behavior;
use yii\caching\TagDependency;
use yii\db\ActiveRecord;
use linex\base\helpers\ActiveRecordHelper;
use linex\base\components\BackendController;
use linex\base\modules\dashboard;

/**
 * Class Tree
 * @package linex\base\behaviors
 * @property \yii\db\ActiveRecord   $owner
 * @property \yii\db\ActiveRecord[] $children
 * @property \yii\db\ActiveRecord   $parent
 */
class Tree extends Behavior
{
    public $idAttribute = 'id';
    public $parentIdAttribute = 'parent_id';
    public $sortOrder = ['id' => SORT_ASC];
    public $cascadeDeleting = false;
    public $activeAttribute = 'active';

    /**
     * @return ActiveRecord
     */
    public function getParent()
    {
        $cacheKey = 'TreeParent:' . $this->owner->className() . ':' . $this->owner->getAttribute($this->idAttribute);
        /** @var $parent ActiveRecord */
        $parent = Yii::$app->cache->get($cacheKey);
        if ($parent === false) {
            $className = $this->owner->className();
            $parent = new $className;
            $parent_id = $this->owner->getAttribute($this->parentIdAttribute);
            if ($parent_id < 1) {
                return null;
            }

            /*
            if ($parent instanceof Category) {
                $parent = Category::findById($parent_id, null);
            } else {
                $parent = $parent->findOne($parent_id);
            }
            */
            $parent = $parent->findOne($parent_id);

            Yii::$app->cache->set(
                $cacheKey,
                $parent,
                86400,
                new TagDependency(
                    [
                        'tags' => [
                            ActiveRecordHelper::getCommonTag($className),
                        ],
                    ]
                )
            );

        }

        return $parent;
    }

    /**
     * @return ActiveRecord[]
     */
    public function getChildren()
    {
        $cacheKey = 'TreeChildren:' . $this->owner->className() . ':' . $this->owner->{$this->idAttribute};
        $children = Yii::$app->cache->get($cacheKey);
        if ($children === false) {
            /** @var $className ActiveRecord */
            $className = $this->owner->className();
            $children = $className::find()
                ->where([$this->parentIdAttribute => $this->owner->{$this->idAttribute}])
                ->orderBy($this->sortOrder);
            if ($this->activeAttribute !== false) {
                $children = $children->andWhere([$this->activeAttribute => 1]);
            }
            $children = $children->all();
            Yii::$app->cache->set(
                $cacheKey,
                $children,
                86400,
                new TagDependency(
                    [
                        'tags' => [
                            ActiveRecordHelper::getCommonTag($className),
                        ],
                    ]
                )
            );
        }

        return $children;
    }

    /**
     * Helper function - converts 2D-array of rows from db to tree hierarchy for use in Menu widget sorted by parent_id
     * ASC.
     *
     * Attributes needed for use with \yii\widgets\Menu:
     * - name
     * - route or url - if empty, then url attribute of menu item will be unset!
     * - rbac_check _optional_ - will be used to determine if this menu item is allowed to user in rbac
     * - parent_id, id - for hierarchy
     *
     * Optional attributes needed for use with \app\backend\widgets\Menu:
     * - icon
     *
     * For example use see \app\backend\models\BackendMenu::getAllMenu()
     *
     * @param  array   $rows              Array of rows. Example query: `$rows = static::find()->orderBy('parent_id
     *                                    ASC, sort_order ASC')->asArray()->all();`
     * @param  integer $start_index       Start index of array to go through
     * @param  integer $current_parent_id ID of current parent
     * @param  boolean $native_menu_mode  Use output for \yii\widgets\Menu
     *
     * @return array Tree suitable for 'items' attribute in Menu widget
     */
    public static function rowsArrayToMenuTree($rows, $start_index = 0, $current_parent_id = 0, $native_menu_mode = true)
    {
        $index = $start_index;
        $tree = [];

        while (isset($rows[$index]) === true && $rows[$index]['parent_id'] <= $current_parent_id) {
            if ($rows[$index]['parent_id'] != $current_parent_id) {
                $index++;
                continue;
            }
            $item = $rows[$index];

            $url = isset($item['route']) ? $item['route'] : $item['url'];

            $tree_item = [
                'label' => $item['name'],
                'url'   => preg_match("#^(/|https?://)#Usi", $url) ? $url : ['/' . $url],
            ];
            if (empty($url)) {
                unset($tree_item['url']);
            }

            if ($item['rbac_check'] != '') {
                $tree_item['visible'] = Yii::$app->user->can($item['rbac_check']);
            }

            if ($native_menu_mode === false) {
                $attributes_to_check = ['icon', 'class'];
                foreach ($attributes_to_check as $attribute) {
                    if (array_key_exists($attribute, $item)) {
                        $tree_item[$attribute] = $item[$attribute];
                    }
                }
            }
            $index++;
            $tree_item['items'] = static::rowsArrayToMenuTree($rows, $index, $item['id'], $native_menu_mode);

            $tree[] = $tree_item;
        }

        return $tree;
    }

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
        ];
    }

    /**
     * After delete event.
     * It deletes children models.
     *
     * @param $event
     *
     * @throws \Exception
     */
    public function afterDelete($event)
    {
        if ($this->cascadeDeleting) {
            foreach ($this->children as $child) {
                $child->delete();
            }
        }
    }
}