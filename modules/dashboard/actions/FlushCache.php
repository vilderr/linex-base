<?php
/**
 * Created by PhpStorm.
 * User: vilderr
 * Date: 03.06.17
 * Time: 1:03
 */

namespace linex\base\modules\dashboard\actions;

use RecursiveDirectoryIterator;
use yii\base\Action;
use yii\base\Module;
use yii\caching\Cache;
use linex\base\modules\dashboard;

/**
 * Class FlushCache
 * @package vendor\vilderr\linex\modules\dashboard\actions
 */
class FlushCache extends Action
{
    public $view = 'actions/message';

    /**
     * Recursive flush all app cache
     *
     * @param null|Module $current Current Module
     *
     * @return string execute message
     */
    protected function flushCache(Module $current = null)
    {
        $message = '';
        if ($current === null) {
            $current = \Yii::$app;
        }

        $modules = $current->getModules();
        foreach ($modules as $moduleName => $module) {
            if (is_array($module)) {
                $module = $current->getModule($moduleName, true);
            }
            if ($module instanceof Module) {
                $message .= $this->flushCache($module);
            }
        }
        $components = $current->getComponents();
        foreach ($components as $componentName => $component) {
            if (is_array($component)) {
                $component = $current->get($componentName);
            }
            if ($component instanceof Cache) {
                $message .= $component->flush() ?
                    dashboard\DashboardModule::t('Cache is flushed') . '<br>' :
                    '';
            }
        }

        return $message;
    }

    /**
     * @return string
     */
    protected function flushAssets()
    {
        $message = '';
        $except = [\Yii::getAlias('@webroot/assets/.gitignore'), \Yii::getAlias('@webroot/assets/index.html')];
        $dir = \Yii::getAlias('@webroot/assets');
        $it = new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS);
        /* @var RecursiveDirectoryIterator[] $files */
        $files = new \RecursiveIteratorIterator($it, \RecursiveIteratorIterator::CHILD_FIRST);
        $hasErrors = false;
        if (stristr(PHP_OS, 'WIN') === false) {
            foreach ($files as $file) {
                if (!in_array($file->getRealPath(), $except)) {
                    if ($file->isDir() && $file->isLink() === false) {
                        $result = @rmdir($file->getRealPath());
                    } elseif ($file->isLink() === true) {
                        $result = @unlink($file->getPath() . DIRECTORY_SEPARATOR . $file->getFilename());
                    } else {
                        $result = @unlink($file->getRealPath());
                    }
                    if (!$result) {
                        $hasErrors = true;
                    }
                }
            }
        }
        $message .= $hasErrors
            ? dashboard\DashboardModule::t('Some assets are not flushed')
            : dashboard\DashboardModule::t('Assets are flushed');

        return $message;
    }

    /**
     * @return string
     */
    public function run()
    {
        $message = $this->flushCache();
        $message .= $this->flushAssets();

        return $this->controller->renderPartial($this->view, ['message' => $message]);
    }
}