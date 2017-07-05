<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        'assets/map/map.css'
    ];
    public $js = [
        '//maps.googleapis.com/maps/api/js?v=3.exp&libraries=places',
        'assets/map/map.js',
        'assets/map/markerclusterer_compiled.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
