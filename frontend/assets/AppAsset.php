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
        'js/map/map.css',
        'js/scrollbar/jquery.mCustomScrollbar.min.css'
    ];
    public $js = [
        '//maps.googleapis.com/maps/api/js?key=AIzaSyCE_Zc_9w7-i_e2lkF7eR3exkN43hKW5hc&callback=initMap&libraries=places', //key AIzaSyBpXjxM_57LlqtTqxK8hDITlcs3BQt2_Rg 
        'js/map/markerclusterer_compiled.js',
        'js/new_map.js',
        'js/realty.js',
        'js/common.js',
        'js/scrollbar/jquery.mCustomScrollbar.concat.min.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\web\jQueryAsset',
        'rmrevin\yii\fontawesome\AssetBundle'
    ];
    //public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
}
