<?php
use yii\helpers\Html;
use linex\base\widgets\Alert;
use linex\base\modules\dashboard\assets\EmptyAsset;

/**
 * @var $this    yii\web\View;
 * @var $content string
 */
EmptyAsset::register($this);
$this->beginPage(); ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500&amp;subset=latin,latin-ext,cyrillic,cyrillic-ext"
              rel="stylesheet" type="text/css">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <body<?= YII_DEBUG ? ' class="debug"' : ''; ?>>
    <?php $this->beginBody() ?>
    <div id="wrapper">
        <div id="wrap">
            <div id="header">
                <div class="logo"><?= Yii::$app->id ?></div>
            </div>
            <div id="content" class="container">
                <div class="content-header">
                    <h1><?= $this->title; ?></h1>
                </div>
                <div class="content-body">
                    <?= Alert::widget(); ?>
                    <?= $content ?>
                </div>
            </div>
        </div>
        <div id="foot"></div>
    </div>
    <?php $this->endBody() ?>
    </body>
    </html>
<? $this->endPage();