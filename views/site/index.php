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
	<p></p>
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
	                	document.querySelector( "p" ).innerHTML = id + " --- " + name;
	                }
	                
	            }
            });
        })
        
    </script>


<?php
//var_dump(Yii::getAlias('@yii'));
/*	echo '今天：'.date('Y-m-d');
	echo "<br>";
	echo '今天0点：'.strtotime(date('Y-m-d'));
	echo "<br>";
	echo '那天：'.date('Y-m-d',1564783200);
	$day = date('Y-m-d',1564783200);
	echo "<br>";
	echo '那天0点：'.strtotime($day);
	echo "<br>";
	echo 1564783200;
	echo '<br>';

	echo '<hr>';
	echo date('YmdHis').mt_rand(100,999);
	echo '<hr>';*/
?>

