<?php

namespace linex\base\modules\dashboard\models;

use Yii;
use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;
use yii\caching\TagDependency;
use yii\behaviors\TimestampBehavior;

use linex\base\BaseModule;
use linex\base\modules\user\UserModule;
use linex\base\helpers\ActiveRecordHelper;
use linex\base\behaviors\Tree;
use linex\base\behaviors\BanDelete;

/**
 * Class Menu
 * @package vendor\vilderr\linex\modules\dashboard\models
 */
class BackendMenu extends ActiveRecord
{
    /**
     * @var array
     */
    private static $identity_map = [];

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'ban'  => [
                'class'   => BanDelete::className(),
                'message' => BaseModule::t('Not allowed!'),
            ],
            'tags' => [
                'class' => ActiveRecordHelper::className(),
            ],
            [
                'class'           => Tree::className(),
                'cascadeDeleting' => true,
                'activeAttribute' => false,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%backend_menu}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id', 'sort'], 'integer'],
            [['name'], 'required'],
            [['rbac_check'], 'string', 'max' => 64],
            [['name', 'route'], 'string', 'max' => 255],
            ['sort', 'default', 'value' => 100],
            ['added_by', 'default', 'value' => 'user'],
        ];
    }

    /**
     * Scenarios
     * @return array
     */
    public function scenarios()
    {
        return [
            'default' => [
                'parent_id',
                'name',
                'route',
                'icon',
                'rbac_check',
                'sort',
                'added_by',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'         => BaseModule::t('ID'),
            'parent_id'  => BaseModule::t('Parent ID'),
            'name'       => BaseModule::t('Name'),
            'route'      => BaseModule::t('Route'),
            'icon'       => BaseModule::t('Icon'),
            'sort'       => BaseModule::t('Sort'),
            'rbac_check' => UserModule::t('Permission'),
        ];
    }

    /**
     * @return ActiveDataProvider
     */
    public function getProvider()
    {
        $query = self::find()->where(['parent_id' => $this->id]);

        $dataProvider = new ActiveDataProvider(
            [
                'query'      => $query,
                'sort'       => ['defaultOrder' => ['sort' => SORT_ASC]],
                'pagination' => [
                    'pageSize' => 10,
                ],
            ]
        );

        return $dataProvider;
    }

    /**
     * Returns model instance by ID(primary key) with cache support
     *
     * @param  integer $id ID of record
     *
     * @return BackendMenu BackendMenu instance
     */
    public static function findById($id)
    {
        if (!isset(static::$identity_map[$id])) {
            $cacheKey = static::tableName() . ":$id";
            if (false === $model = Yii::$app->cache->get($cacheKey)) {
                $model = static::find()->where(['id' => $id]);

                if (null !== $model = $model->one()) {
                    Yii::$app->cache->set(
                        $cacheKey,
                        $model,
                        86400,
                        new TagDependency([
                            'tags' => [
                                ActiveRecordHelper::getCommonTag(static::className()),
                            ],
                        ])
                    );
                }
            }
            static::$identity_map[$id] = $model;
        }

        return static::$identity_map[$id];
    }

    /**
     * Returns all available to logged user BackendMenu items in yii\widgets\Menu acceptable format
     * @return BackendMenu[] Tree representation of items
     */
    public static function getAllMenu()
    {
        $rows = Yii::$app->cache->get("BackendMenu:all");
        if (false === is_array($rows)) {
            $rows = static::find()
                ->orderBy('parent_id ASC, sort ASC')
                ->asArray()
                ->all();
            Yii::$app->cache->set(
                "BackendMenu:all",
                $rows,
                86400,
                new TagDependency([
                    'tags' => [
                        ActiveRecordHelper::getCommonTag(static::className()),
                    ],
                ])
            );
        }
        // rebuild rows to tree $all_menu_items
        $all_menu_items = Tree::rowsArrayToMenuTree($rows, 0, 0, false);

        return $all_menu_items;
    }

    /**
     * Возвращает массив родительских моделей для цепочки навигации
     *
     * @param BackendMenu $model
     *
     * @return array|mixed
     */
    public static function getNavChain(BackendMenu $model)
    {
        $cacheKey = "BackendMenu:NavChain:$model->id";

        if (false === $tree = Yii::$app->cache->get($cacheKey)) {
            $tree = [];
            if (!$model->isNewRecord) {
                $tree[] = $model;
            }

            do {
                if (($model = self::findOne($model->parent_id)) !== null) {
                    $tree[] = $model;
                }
            } while ($model !== null);

            if (is_array($tree))
                $tree = array_reverse($tree);

            Yii::$app->cache->set(
                $cacheKey,
                $tree,
                86400,
                new TagDependency([
                    'tags' => [
                        ActiveRecordHelper::getCommonTag(static::className()),
                    ],
                ])
            );
        }

        return $tree;
    }
}