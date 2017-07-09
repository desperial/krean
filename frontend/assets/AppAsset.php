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
        //'css/site.css',
        'css/main.css',
        'assets/map/map.css'
    ];
    public $js = [
        'assets/map/map.js',
        '//maps.googleapis.com/maps/api/js?key=AIzaSyCE_Zc_9w7-i_e2lkF7eR3exkN43hKW5hc&callback=initMap&libraries=places', //key AIzaSyBpXjxM_57LlqtTqxK8hDITlcs3BQt2_Rg 
        'assets/map/markerclusterer_compiled.js',
        'js/realty.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\web\jQueryAsset',
    ];
    //public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
}
