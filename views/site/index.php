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
<div class="col-lg-12">
	<div id="map"></div>
	<script src="./jsMap.min.js"></script>
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
	                	var params = {
                            'data':{'city':name},
                            'dataType':'json',
                            'type':'GET',
                            'success':function(res) {
                                $('#tip').html('未检索到你期望的社区？您可以积极联系其负责人注册入驻！');
                                $('#tip').attr('class','text-center text-info');
                                $('#hidden').attr('class','show');
                                $('#hidden').empty();
                                //console.log(res);
                                for (var i = 0; i < res.length; i++) {
                                    var child = "<div class='col-xs-6 col-sm-3 placeholder'><a href='"
                                    +"index.php?r=community/index&id="+res[i]['community_id']
                                    +"'><img src='./image/community.jpg' width='200' height='200' class='img-circle' alt='Generic placeholder thumbnail'><h4>"
                                    +res[i]['community_name']
                                    +"</h4></a></div>";
                                    $('#hidden').append(child);
                                }
                            }
                        }
                        $.ajax('<?=Url::to(['site/getcommunity']) ?>',params);
	                }
	            }
            });
        });   
    </script>
    <h3 id="tip" class="text-center">点击地图中省市检索对应区域的社区</h3>
    <div id="hidden" class="hidden">
        
    </div>

    <?php

    ?>
</div>
