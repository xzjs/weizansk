<?php defined('IN_IA') or exit('Access Denied');?><html ng-app="diandanbao" class="ng-scope">
<head>
    <style type="text/css">@charset "UTF-8";
    [ng\:cloak], [ng-cloak], [data-ng-cloak], [x-ng-cloak], .ng-cloak, .x-ng-cloak, .ng-hide:not(.ng-hide-animate) {
        display: none !important;
    }

    ng\:form {
        display: block;
    }</style>
    <style type="text/css">@charset "UTF-8";
    [ng\:cloak], [ng-cloak], [data-ng-cloak], [x-ng-cloak], .ng-cloak, .x-ng-cloak, .ng-hide:not(.ng-hide-animate) {
        display: none !important;
    }
    ng\:form {
        display: block;
    }

    </style>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta content="telephone=no" name="format-detection">
    <title>门店列表</title>
    <link data-turbolinks-track="true" href="<?php echo RES;?>/mobile/<?php  echo $this->cur_tpl?>/assets/diandanbao/weixin.css?v=1" media="all" rel="stylesheet">
    <style type="text/css">
        @media screen {
            .smnoscreen {
                display: none
            }
        }
        @media print {
            .smnoprint {
                display: none
            }
        }</style>
    <script type="text/javascript" src="../addons/weisrc_dish/template/js/2/jQuery.js"></script>
    <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=5PESLgvMcSbSUbPjmDKgvGZ3"></script>
    <script type="text/javascript" src="../addons/weisrc_dish/template/js/postion.js"></script>
</head>
<body>
<div ng-view="" style="height: 100%;" class="ng-scope"><div class="ddb-nav-header ng-scope" common-header="">
    <div class="nav-left-item" onclick="javascript :history.back(-1);"><i class="fa fa-angle-left"></i></div><div class="header-title ng-binding">门店收藏</div></div>
    <div id="ddb-delivery-branch-index" class="main-view ng-scope">
        <?php  if(is_array($restlist)) { foreach($restlist as $item) { ?>
        <div class="morelist branch-item ng-scope <?php  if($this->checkStoreHour($item['begintime'], $item['endtime']) == 0) { ?>closed<?php  } ?>" >
            <input id="showlan" type="hidden" value="<?php  echo $item['lng'];?>,<?php  echo $item['lat'];?>"/>
            <a class="branch-info " href="<?php  echo $this->createMobileUrl('detail', array('id' => $item['id']), true)?>">
                <div class="branch-image">
                    <img src="<?php  echo tomedia($item['logo']);?>">
                </div>
                <div class="delivery-info">
                    <div class="first-line">
                        <div class="name ng-binding">
                            <?php  echo $item['title'];?>
                        </div>
                        <?php  if($this->checkStoreHour($item['begintime'], $item['endtime']) == 0) { ?>
                        <div class="tag label-red ng-scope">休息中</div>
                        <?php  } else { ?>
                        <div class="tag label-green ng-scope">营业中</div>
                        <?php  } ?>
                        <?php  if($item['is_meal']==1) { ?>
                        <div class="tag label-red ng-scope">店</div>
                        <?php  } ?>
                        <?php  if($item['is_delivery']==1) { ?>
                        <div class="tag label-blue ng-scope">外</div>
                        <?php  } ?>
                        <div class="distance right ng-binding" id="shopspostion"></div>
                    </div>
                    <div class="second-line">
                        <div class="comment-level red">
                            <div class="ng-isolate-scope">
                                <?php  for($i=0;$i < $item['level']; $i++){ ?>
                                <i class="fa fa-star-o ng-scope"></i>
                                <?php  }?>
                            </div>
                        </div>
                    </div>
                    <div class="third-line">
                        <div class="time ng-hide" ng-show="branch.delivery_times.length &gt; 0">
                            <i class="fa fa-clock-o"></i>
                            配送时间
                        </div>
                        <div class="fee ng-binding">
                            <?php  if(!empty($item['sendingprice'])) { ?>
                            <span class="ng-binding ng-scope">￥<?php  echo $item['sendingprice'];?>起送</span>
                            <span class="spliter"></span>
                            <?php  } ?>
                            <?php  if(!empty($item['dispatchprice'])) { ?>
                            <span class="ng-binding ng-scope">配送费￥<?php  echo $item['dispatchprice'];?></span>
                            <span class="spliter"></span>
                            <?php  } ?>
                        </div>
                        <div class="address ng-binding"><?php  echo $item['address'];?></div>
                    </div>
                </div>
            </a>
            <?php  if(!empty($item['info'])) { ?>
            <div class="top-sales ng-binding ng-scope">
                <?php  echo $item['info'];?>
            </div>
            <?php  } ?>
        </div>
        <?php  } } ?>
    </div>
    <!--footer-->
    <?php  include $this->template($this->cur_tpl.'/_menu');?>
</div>
</body>
</html>