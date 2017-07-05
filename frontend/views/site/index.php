<?php

/* @var $this yii\web\View */

$this->title = 'My Yii Application';
?>
<div class="site-index">
    <div class="overhill-app-left">
        <div id="overhill-map"></div>
        <div id="overhill-page"></div>
        <div class="overhill-app-tabs">
            <div class="overhill-app-tabs-bar">
                <a class="toggle active" href="javascript:void(0)" rel="1" onclick="overhill.bottomTabsControl(this)">Приоритетные объявления</a>
            </div>
            <div class="overhill-app-tabs-box">
                <div class="tab tab-1 active" id="overhill-vip-ads"></div>
            </div>
        </div>
    </div>
</div>

<!-- BEGIN JIVOSITE CODE {literal} -->
<script type='text/javascript'>
(function(){ var widget_id = 'n0LnCHlwtD';var d=document;var w=window;function l(){
var s = document.createElement('script'); s.type = 'text/javascript'; s.async = true; s.src = '//code.jivosite.com/script/widget/'+widget_id; var ss = document.getElementsByTagName('script')[0]; ss.parentNode.insertBefore(s, ss);}if(d.readyState=='complete'){l();}else{if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})();
</script>
<!-- {/literal} END JIVOSITE CODE -->