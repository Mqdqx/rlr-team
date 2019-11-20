<?php

/* @var $this yii\web\View */
use yii\helpers\Url;
use yii\helpers\Html;

$this->title = '人恋人公益平台';
?>
<ul class="nav nav-tabs" id="nav_option">
</ul>
<ul class="nav nav-pills" id="menu">
</ul>

	<div id="map"></div>
	<script src="./jsmap.min.js"></script>
    <script>
    	var region = getUrlParam(window.location.href,'region');
    	if (region == null) {region='china';}
        jsMap.getJSON( "all-map.json", function ( mapJSON ) {
            jsMap.config("#map", mapJSON, {
                name: region,
                width: "100%",
                areaName: {show: true},
                clickCallback: function ( id, name ) {
	                if (region == 'china') {
	                	window.location.href=window.location.href+'&region='+id;
	                } else {
	                	//document.querySelector( "p" ).innerHTML = id + " --- " + name;
	                }
	            }
            });
        });   
    </script>
    <h3 id="tip" class="text-center">点击地图中省市检索对应区域的社区</h3>
