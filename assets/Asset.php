<?php

/**
 * @copyright Copyright (c) 2015 Yiister
 * @license https://github.com/yiister/yii2-gentelella/blob/master/LICENSE
 * @link http://gentelella.yiister.ru
 */

namespace alex290\multinav\assets;

class Asset extends \yii\web\AssetBundle
{
    public $css = [
        'css/app.css',
    ];

    public $js = [];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapPluginAsset',
    ];

    public $sourcePath = '@alex290/multinav/assets/scr';
}
