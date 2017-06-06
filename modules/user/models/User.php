<?php

namespace linex\base\modules\user\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\web\IdentityInterface;
use yii\caching\TagDependency;
use yii\base\NotSupportedException;

use linex\base\BaseModule;
use linex\base\behaviors\BanDelete;
use linex\base\helpers\ActiveRecordHelper;
use linex\base\modules\user\UserModule;

/**
 * Class User
 * @package vendor\vilderr\linex\modules\user\models
 *
 * @property int      $id
 * @property string   $username
 * @property resource $auth_key
 * @property string   $password_hash
 * @property resource $password_reset_token
 * @property string   $email
 * @property int      $status
 * @property int      $created_at
 * @property int      $updated_at
 * @property string   $first_name
 * @property string   $last_name
 * @property int      $username_is_temporary
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;
    const USER_GUEST = 0;

    public $confirmPassword;
    public $newPassword;
    public $password;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            ActiveRecordHelper::className(),
            'ban' => [
                'class'   => BanDelete::className(),
                'message' => BaseModule::t('Not allowed!'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],

            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'required'],
            ['username', 'string', 'min' => 2, 'max' => 255],
            ['username', 'unique', 'message' => UserModule::t('This username has already been taken')],

            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required', 'except' => ['registerService']],
            ['email', 'email'],
            ['email', 'unique', 'message' => UserModule::t('This email address has already been taken')],
            ['email', 'exist', 'message' => UserModule::t('There is no user with such email.'), 'on' => 'requestPasswordResetToken'],

            ['password', 'required', 'on' => ['adminSignup', 'changePassword']],
            ['password', 'string', 'min' => 8],
            [['first_name', 'last_name'], 'string', 'max' => 255],

            // change password
            [['newPassword', 'confirmPassword'], 'required'],
            [['newPassword', 'confirmPassword'], 'string', 'min' => 8],
            [['confirmPassword'], 'compare', 'compareAttribute' => 'newPassword'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                    => UserModule::t('ID'),
            'username'              => UserModule::t('Username'),
            'auth_key'              => UserModule::t('Auth Key'),
            'password_hash'         => UserModule::t('Password Hash'),
            'password_reset_token'  => UserModule::t('Password Reset Token'),
            'email'                 => UserModule::t('Email'),
            'status'                => UserModule::t('Status'),
            'created_at'            => UserModule::t('Created At'),
            'updated_at'            => UserModule::t('Updated At'),
            'first_name'            => UserModule::t('First Name'),
            'last_name'             => UserModule::t('Last Name'),
            'username_is_temporary' => UserModule::t('Username Is Temporary'),
            'password'              => UserModule::t('Password'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'signup'                    => ['username', 'email', 'password', 'first_name', 'last_name'],
            'resetPassword'             => ['password_hash', 'password_reset_token'],
            'requestPasswordResetToken' => ['email'],

            'registerService'      => ['email', 'first_name', 'last_name'],
            'updateProfile'        => ['email', 'first_name', 'last_name'],
            'completeRegistration' => ['first_name', 'last_name', 'username', 'email'],
            'changePassword'       => ['password', 'newPassword', 'confirmPassword'],
            // admin
            'search'               => ['id', 'username', 'email', 'status', 'create_time', 'first_name', 'last_name'],
            'admin'                => ['username', 'status', 'email', 'password', 'first_name', 'last_name'],
            'adminSignup'          => ['username', 'status', 'email', 'password', 'first_name', 'last_name', 'auth_key'],
            'passwordResetToken'   => ['password_reset_token'],
        ];
    }

    /**
     * List of all possible statuses for User instance
     * @return array
     */
    public static function getStatusList()
    {
        return [
            self::STATUS_ACTIVE  => UserModule::t('Status Active'),
            self::STATUS_DELETED => UserModule::t('Status Deleted'),
        ];
    }

    /**
     * Finds an identity by the given ID.
     *
     * @param string|integer $id the ID to be looked for
     *
     * @return IdentityInterface|null the identity object that matches the given ID.
     */
    public static function findIdentity($id)
    {

        if (is_numeric($id)) {
            $model = Yii::$app->cache->get("User:$id");
            if ($model === false) {
                $model = static::find()
                    ->where('id=:id', [':id' => $id])
                    ->one();
                if ($model !== null) {
                    Yii::$app->cache->set(
                        "User:$id",
                        $model,
                        3600,
                        new TagDependency([
                            'tags' => [
                                ActiveRecordHelper::getObjectTag($model->className(), $model->id),
                            ],
                        ])
                    );
                }
            }

            return $model;
        } else {
            return null;
        }
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        // see https://github.com/yiisoft/yii2/issues/2689
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     *
     * @return null|User
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => static::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     *
     * @return User|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (static::isPasswordResetTokenValid($token) === false) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status'               => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     *
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token) === true) {
            return false;
        }
        $expire = Yii::$app->modules['user']->passwordResetTokenExpire;
        $parts = explode('_', $token);
        $timestamp = (int)end($parts);

        return $timestamp + $expire >= time();
    }

    /**
     * @return int|string current user ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string current user auth key
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @param string $authKey
     *
     * @return boolean if auth key is valid for current user
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * @param string $password password to validate
     *
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * @return mixed|string Display name for the user visual identification
     */
    public function getDisplayName()
    {
        $name = trim($this->first_name . ' ' . $this->last_name);

        return $name ? $name : $this->username;
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        if ($insert) {
            $this->setPassword($this->password);
        }

        return true;
    }

    /**
     * Returns gravatar image link for user
     *
     * @param int $size Avatar size in pixels
     *
     * @return string
     */
    public function gravatar($size = 40)
    {
        $hash = md5(strtolower(trim($this->email)));

        return '//www.gravatar.com/avatar/' . $hash . '?s=' . $size;
    }
}