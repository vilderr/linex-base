<?php
/**
 * @var $content string
 */
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\icons\Icon;
use linex\base\modules\dashboard;
use linex\base\BaseModule;
use linex\base\modules\dashboard\widgets\flushcache\Button;
use linex\base\widgets\Alert;

dashboard\assets\Asset::register($this);
?>
<? $this->beginPage(); ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <?php $this->head() ?>
    </head>
    <body class="hold-transition skin-default sidebar-mini">
    <?php $this->beginBody(); ?>
    <div class="wrapper">
        <header class="main-header">
            <a href="/dashboard" class="logo">
                <span class="logo-mini">LX</span>
                <span class="logo-lg"><?= Yii::$app->name; ?></span>
            </a>
            <nav class="navbar navbar-static-top" role="navigation">
                <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                    <?= \kartik\icons\Icon::show('bars') ?>
                    <span class="sr-only">Toggle navigation</span>
                </a>
                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
                        <? if (Yii::$app->user->can('cache manage')): ?>
                            <li>
                                <?= Button::widget(
                                    [
                                        'url'         => Url::to(['/dashboard/flush-cache']),
                                        'htmlOptions' => [
                                            'class' => '',
                                            'title' => dashboard\DashboardModule::t('Flush cache'),
                                        ],
                                        'label'       => Icon::show('trash-o'),
                                        'onSuccess'   => 'function(data) {
                                    $.amaran({message: data, position: "top right"});
                                }',
                                    ]
                                ) ?>
                            </li>
                        <? endif; ?>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-envelope-o"></i>
                                <span class="label label-success">4</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="header">You have 4 messages</li>
                                <li>
                                    <ul class="menu">
                                        <li>
                                            <a href="#">
                                                <h4>
                                                    Support Team
                                                    <small><i class="fa fa-clock-o"></i> 5 mins</small>
                                                </h4>
                                                <p>Why not buy a new awesome theme?</p>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#">
                                                <h4>
                                                    Support Team
                                                    <small><i class="fa fa-clock-o"></i> 5 mins</small>
                                                </h4>
                                                <p>Why not buy a new awesome theme?</p>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#">
                                                <h4>
                                                    Support Team
                                                    <small><i class="fa fa-clock-o"></i> 5 mins</small>
                                                </h4>
                                                <p>Why not buy a new awesome theme?</p>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#">
                                                <h4>
                                                    Support Team
                                                    <small><i class="fa fa-clock-o"></i> 5 mins</small>
                                                </h4>
                                                <p>Why not buy a new awesome theme?</p>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#">
                                                <h4>
                                                    Support Team
                                                    <small><i class="fa fa-clock-o"></i> 5 mins</small>
                                                </h4>
                                                <p>Why not buy a new awesome theme?</p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="footer"><a href="#">Читать все</a></li>
                            </ul>
                        </li>
                        <li class="dropdown user user-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <span class="hidden-xs"><?= Yii::$app->user->getIdentity()->displayName; ?></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="user-footer">
                                    <div>
                                        <a href="#"
                                           class="btn btn-default btn-flat btn-block"><?= dashboard\DashboardModule::t('Profile'); ?></a>
                                    </div>
                                    <div>
                                        <?= Html::a(
                                            dashboard\DashboardModule::t('Sign out'),
                                            ['/dashboard/sign/out'],
                                            ['data-method' => 'post', 'class' => 'btn btn-default btn-flat btn-block']
                                        ) ?>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        <aside class="main-sidebar">
            <section class="sidebar">
                <?=
                dashboard\widgets\BackendMenu::widget([
                    'options'         => [
                        'class' => 'sidebar-menu',
                    ],
                    'submenuTemplate' => '<ul class="treeview-menu">{items}</ul>',
                    'items'           => dashboard\models\BackendMenu::getAllMenu(),
                ]);
                ?>
            </section>
        </aside>
        <div class="content-wrapper">
            <section class="content-header">
                <h1><?= $this->title; ?></h1>
                <?= Breadcrumbs::widget([
                    'options'  => [
                        'class' => 'breadcrumbs',
                    ],
                    'homeLink' => [
                        'label' => dashboard\DashboardModule::t('Dashboard'),
                        'url'   => Url::to(['/dashboard']),
                    ],
                    'links'    => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]) ?>
            </section>
            <section class="content">
                <?= Alert::widget(['options' => ['class' => 'flat']]) ?>
                <?= $content; ?>
            </section>
        </div>
        <footer class="main-footer"></footer>
        <? \yii\bootstrap\Modal::begin(
            [
                'id'     => 'delete-confirmation',
                'footer' =>
                    Html::button(
                        BaseModule::t('Delete'),
                        [
                            'class'        => 'btn btn-danger btn-flat',
                            'data-action'  => 'confirm',
                            'data-dismiss' => 'modal',
                        ]
                    )
                    . Html::button(
                        BaseModule::t('Cancel'),
                        [
                            'class'        => 'btn btn-default btn-flat',
                            'data-dismiss' => 'modal',
                        ]
                    ),
                'header' => BaseModule::t('Are you sure you want to delete this object?'),
            ]
        )
        ?>
        <div class="alert flat alert-danger">
            <i class="fa fa-exclamation-triangle fa-lg"></i>
            <?= BaseModule::t('All data will be lost') ?>
        </div>
        <?php \yii\bootstrap\Modal::end() ?>
    </div>
    <?php $this->endBody(); ?>
    </body>
    </html>
<? $this->endPage(); ?>