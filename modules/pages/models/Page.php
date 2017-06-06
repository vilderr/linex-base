<?php

namespace linex\base\modules\pages\models;

use Yii;
use yii\caching\TagDependency;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;

use linex\base\BaseModule;
use linex\base\modules\pages\PagesModule;
use linex\base\helpers\ActiveRecordHelper;
use linex\base\behaviors\BanDelete;
use linex\base\behaviors\Tree;

/**
 * This is the model class for table "page".
 *
 * @property int    $id
 * @property int    $parent_id
 * @property string $slug
 * @property int    $published
 * @property string $name
 * @property string $content
 * @property int    $sort
 * @property int    $created_at
 * @property int    $updated_at
 * @property string $added_by
 */
class Page extends \yii\db\ActiveRecord
{
    public static $SLUG_PATTERN = '/^[0-9a-z-]{0,60}$/';
    private static $identity_map = [];

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
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
                'activeAttribute' => 'published',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%page}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['slug', 'name'], 'required'],
            [['parent_id', 'published', 'sort', 'created_at', 'updated_at'], 'integer'],
            [['content'], 'string'],
            ['slug', 'match', 'pattern' => self::$SLUG_PATTERN, 'message' => PagesModule::t('Slug can contain only 0-9, a-z and "-" characters (max: 60).')],
            ['slug', 'unique'],
            [['name'], 'string', 'max' => 255],
            ['added_by', 'default', 'value' => 'user'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'         => PagesModule::t('ID'),
            'parent_id'  => PagesModule::t('Parent ID'),
            'slug'       => BaseModule::t('Slug'),
            'slug_path'  => BaseModule::t('Slug Path'),
            'published'  => PagesModule::t('Published'),
            'name'       => BaseModule::t('Name'),
            'content'    => PagesModule::t('Content'),
            'sort'       => BaseModule::t('Sort'),
            'created_at' => BaseModule::t('Created At'),
            'updated_at' => BaseModule::t('Updated At'),
        ];
    }

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
     * @param int $id
     * @param int $is_published
     *
     * @return mixed
     */
    public static function findById($id, $is_published = 1)
    {
        if (!is_numeric($id)) {
            return null;
        }
        if (!isset(static::$identity_map[$id])) {

            $cacheKey = "Page:$id:$is_published";
            static::$identity_map[$id] = Yii::$app->cache->get($cacheKey);
            if (!is_object(static::$identity_map[$id])) {
                static::$identity_map[$id] = Page::find()->where(['id' => $id])->one();

                if (is_object(static::$identity_map[$id])) {
                    Yii::$app->cache->set(
                        $cacheKey,
                        static::$identity_map[$id],
                        86400,
                        new TagDependency([
                            'tags' => [
                                ActiveRecordHelper::getCommonTag(static::className()),
                            ],
                        ])
                    );
                }
            }
        }

        return static::$identity_map[$id];
    }

    /**
     * @param null $path
     *
     * @return array|mixed|null|\yii\db\ActiveRecord
     */
    public static function getByUrlPath($path = null)
    {
        if ((null === $path) || !is_string($path)) {
            return null;
        }

        $cacheKey = "Page:$path";
        if (($page = Yii::$app->cache->get($cacheKey)) === false) {
            $page = static::find()->where(['slug' => $path])->asArray()->one();
            $duration = 86400;
            if (!is_array($page)) {
                $duration = 3600;
            }

            Yii::$app->cache->set(
                $cacheKey,
                $page,
                $duration,
                new TagDependency([
                    'tags' => [
                        ActiveRecordHelper::getCommonTag(static::className()),
                    ],
                ])
            );
        }

        return $page;
    }

    public function beforeSave($insert)
    {
        if (!$insert) {
            // reset a cache tag to get a new parent model below
            TagDependency::invalidate(Yii::$app->cache, [ActiveRecordHelper::getCommonTag(self::className())]);
        }

        $this->slug_path = $this->makeSlugPath();

        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    private function makeSlugPath()
    {
        $parent_model = $this->parent;
        if ($parent_model !== null) {
            if ($parent_model->slug === 'mainpage') {
                return $this->slug;
            } else {
                return $parent_model->slug_path . '/' . $this->slug;
            }
        } elseif ($this->slug === 'mainpage') {
            return '';
        }

        return $this->slug; // it's main page
    }
}