<?php

namespace linex\base\modules\user\models\forms;

use Yii;
use yii\base\Model;
use linex\base\modules\user\models\User;
use linex\base\modules\user\UserModule as UserModule;
use linex\base\modules\dashboard\DashboardModule as DashboardModule;

/**
 * Class LoginForm
 * @package linex\base\modules\user\models\forms
 */
class LoginForm extends Model
{
    const SCENARIO_DASHBOARD = 'dashboard';

    private $user = false;
    public $username;
    public $password;
    public $rememberMe = true;

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_DASHBOARD] = ['username', 'password'];

        return $scenarios;
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            ['password', 'validatePassword'],
            ['rememberMe', 'boolean'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'username'   => UserModule::t('Username'),
            'password'   => UserModule::t('Password'),
            'rememberMe' => UserModule::t('Remember Me'),
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     */
    public function validatePassword()
    {
        if ($this->hasErrors() === false) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError('password', 'Incorrect username or password.');
            }
        }
    }

    /**
     * Метод для авторизации пользователя.
     * Логиним пользователя, если авторизация происходит в админку, проверяем права пользователя.
     * В случае если прав не хватает для, разлогиниваем пользователя и выводим сообщение
     *
     * @return bool
     */
    public function login()
    {
        if ($this->validate()) {
            $user = $this->getUser();
            $result = Yii::$app->user->login(
                $user,
                $this->rememberMe ? UserModule::$loginSessionDuration : 0
            );

            if ($this->scenario == self::SCENARIO_DASHBOARD) {
                if (!Yii::$app->user->can(DashboardModule::$administratePermission)) {
                    Yii::$app->user->logout();
                    Yii::$app->session->setFlash('error', 'Доступ запрещен');

                    return false;
                }
            }

            return $result;
        }

        return false;
    }

    /**
     * Поиск модели пользователя по логину
     *
     * @return User|null
     */
    private function getUser()
    {
        if ($this->user === false) {
            $this->user = User::findByUsername($this->username);
        }

        return $this->user;
    }
}