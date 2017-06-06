<?php

namespace linex\base\modules\user\models\backend;

use linex\base\BaseModule;
use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\rbac\Item;
use linex\base\modules\user\UserModule;

/**
 * Class AuthItem
 * @package linex\base\modules\user\models\backend
 */
class AuthItem extends Model
{
    private static $protectedItems = [
        'admin',
        'manager',
        'administrate',
        'cache manage',
        'content manage',
        'setting manage',
        'user manage',
    ];

    public $name;
    public $oldname;
    public $type;
    public $description = '';
    public $ruleName = null;
    public $isNewRecord = false;

    public $children = [];
    public $errorMessage = null;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['name', 'type'], 'required'],
            ['name', 'trim'],
            ['name', 'match', 'pattern' => '/^[a-z ]+$/', 'message' => 'Название может состоять из латинских букв в нижнем регистре, при необходимости разделенных пробелом'],
            ['name', 'check'],
            ['description', 'safe'],
            ['isNewRecord', 'boolean'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'name'        => UserModule::t('Rbac Name'),
            'oldname'     => UserModule::t('Rbac Old Name'),
            'type'        => UserModule::t('Rbac Type'),
            'description' => UserModule::t('Rbac Description'),
            'ruleName'    => UserModule::t('Rbac Biz Rule'),
            'children'    => UserModule::t('Rbac Children'),
        ];
    }

    /**
     * @return array
     */
    public function scenarios()
    {
        return [
            'default' => ['name', 'oldname', 'type', 'description', 'ruleName', 'children'],
        ];
    }

    /**
     * @return Item
     */
    public function createItem()
    {
        $item = new Item(
            [
                'name'        => $this->name,
                'type'        => $this->type,
                'description' => $this->description,
                'ruleName'    => trim($this->ruleName) ? trim($this->ruleName) : null,
            ]
        );
        Yii::$app->getAuthManager()->add($item);
        foreach ($this->children as $value) {
            try {
                Yii::$app->getAuthManager()->addChild($item, new Item(['name' => $value]));
            } catch (\Exception $ex) {
                $this->errorMessage .= UserModule::t("Item <strong>{value}</strong> is not assigned:", [
                        'value' => $value,
                    ])
                    . " " . $ex->getMessage() . "<br />";
            }
        }

        return $item;
    }

    public function updateItem()
    {
        $item = new Item();
        $item->name = $this->name;
        $item->type = $this->type;
        $item->description = $this->description;
        $item->ruleName = trim($this->ruleName) ? trim($this->ruleName) : null;

        if (!self::isProtectedItem($this->name)) {
            Yii::$app->getAuthManager()->update($this->oldname, $item);
            $children = Yii::$app->getAuthManager()->getChildren($item->name);

            foreach ($children as $value) {
                $key = array_search($value->name, $this->children);
                if ($key === false) {
                    Yii::$app->getAuthManager()->removeChild($item, $value);
                } else {
                    unset($this->children[$key]);
                }
            }
            foreach ($this->children as $value) {
                try {
                    Yii::$app->getAuthManager()->addChild($item, new Item(['name' => $value]));
                } catch (\Exception $ex) {
                    $this->errorMessage = UserModule::t("Item <strong>{value}</strong> is not assigned:", [
                            'value' => $value,
                        ])
                        . " " . $ex->getMessage() . "<br />";
                }
            }
        } else {
            $this->errorMessage = BaseModule::t('Not allowed!');
        }

        return $item;
    }

    /**
     * @param $attribute
     * @param $params
     */
    public function check($attribute, $params)
    {
        if (((strlen($this->oldname) == 0) || ($this->oldname != $this->name)) &&
            ((\Yii::$app->getAuthManager()->getRole($this->$attribute) !== null) ||
                \Yii::$app->getAuthManager()->getPermission($this->$attribute) !== null)
        ) {
            $this->addError($attribute, BaseModule::t('Duplicate item "{attribute}"', ['attribute' => $this->$attribute]));
        }
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    public static function getRbacType($type)
    {
        $types = [
            Item::TYPE_ROLE       => UserModule::t('Roles'),
            Item::TYPE_PERMISSION => UserModule::t('Permissions'),
        ];

        return $types[$type];
    }

    public static function isProtectedItem($id)
    {
        return ArrayHelper::isIn($id, self::$protectedItems);
    }
}