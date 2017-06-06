<?php

namespace linex\base\modules\user;

use Yii;
use yii\base\BootstrapInterface;

/**
 * user module definition class
 *
 * Class Module
 * @package linex\base\modules\user
 */
class UserModule extends \yii\base\Module implements BootstrapInterface
{
    /**
     * Duration of login session for users in seconds.
     * By default 30 days.
     * @var int
     */
    public static $loginSessionDuration = 2592000;
    /**
     * Expiration time in seconds for user password reset generated token.
     * @var int
     */
    public $passwordResetTokenExpire = 3600;

    public function bootstrap($app)
    {
        $app->i18n->translations['modules/users/*'] = [
            'class'            => 'yii\i18n\PhpMessageSource',
            'forceTranslation' => true,
            'basePath'         => '@vendor/linex/base/modules/user/messages',
            'fileMap'          => [
                'modules/users/Module' => 'module.php',
            ],
        ];

        $app->getUrlManager()->addRules([
            [
                'class'       => 'yii\web\GroupUrlRule',
                'prefix'      => 'dashboard',
                'routePrefix' => 'dashboard',
                'rules'       => [
                    '<_m:(users)>'                                           => '<_m>/default/index',
                    '<_m:(users)>/<_a:(add|edit|delete|delete-all)>'         => '<_m>/default/<_a>',
                    '<_m:(users)>/rbac/<_c:(roles|permissions)>'             => '<_m>/rbac/<_c>/index',
                    '<_m:(users)>/rbac/<_c:(roles|permissions)>/<_a[\w\-]+>' => '<_m>/rbac/<_c>/<_a>',
                ],
            ],
        ]);
    }

    /**
     * @param string $message
     * @param string $category
     * @param array  $params
     * @param null   $language
     *
     * @return string
     */
    public static function t($message, $params = [], $category = 'Module', $language = null)
    {
        return Yii::t('modules/users/' . $category, $message, $params, $language);
    }
}
