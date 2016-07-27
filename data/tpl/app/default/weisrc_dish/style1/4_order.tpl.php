<?php defined('IN_IA') or exit('Access Denied');?><html ng-app="diandanbao" class="ng-scope"><head><style type="text/css">@charset "UTF-8";[ng\:cloak],[ng-cloak],[data-ng-cloak],[x-ng-cloak],.ng-cloak,.x-ng-cloak,.ng-hide:not(.ng-hide-animate){display:none !important;}ng\:form{display:block;}</style>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta content="telephone=no" name="format-detection">
    <link rel="shortcut icon" href="data:image/x-icon;," type="image/x-icon">
    <meta content="production" name="env">
    <title>我的订单</title>
    <link data-turbolinks-track="true" href="<?php echo RES;?>/mobile/<?php  echo $this->cur_tpl?>/assets/diandanbao/weixin.css?v=1" media="all" rel="stylesheet">
    <style type="text/css">@media screen{.smnoscreen {display:none}} @media print{.smnoprint{display:none}}</style></head>
<body>

<div ng-view="" style="height: 100%;" class="ng-scope">
    <div class="ddb-nav-header ng-scope" common-header="">
    <div class="nav-left-item"  onclick="javascript :history.back(-1);"><i class="fa fa-angle-left"></i></div>
    <div class="header-title ng-binding">我的订单</div>
        <?php  if($is_permission == true) { ?>
        <a class="nav-right-item" href="<?php  echo $this->createMobileUrl('adminorder', array(), true)?>">
            <div class="operation-button red">管理</div>
        </a>
        <?php  } ?>
    </div>

    <?php  include $this->template($this->cur_tpl.'/_menu');?>
    <div class="ddb-secondary-nav-header orders-index-secondary-nav ng-scope">
        <div class="filter-switch">
            <div class="filter-tab<?php  if($status==0) { ?> active<?php  } ?>" onclick="jump(0);">
                待确认
            </div>
            <div class="filter-tab<?php  if($status==1) { ?> active<?php  } ?>" onclick="jump(1);">
                已确认
            </div>
            <div class="filter-tab<?php  if($status==3) { ?> active<?php  } ?>" onclick="jump(3);">
                已完成
            </div>
        </div>
    </div>
    <div class="orders-index-page main-view ng-scope" id="delivery-orders-index">
        <?php  if(is_array($order_list)) { foreach($order_list as $items) { ?>
        <div class="space-12"></div>
        <div class="order-item section ng-scope" onclick="go_detail(<?php  echo $items['id'];?>)">
            <a class="list-item">
                <div class="time ng-binding">下单时间：<?php  echo date('Y-m-d h:i:s',$items['dateline'])?></div>
            </a>
            <a class="list-item">
                <div class="name ng-binding"><?php  echo $storelist[$items['storeid']]['title'];?></div>
                <div class="variants overflow-ellipsis ng-binding">
                    <?php  $isfirst = false;?>
                    <?php  if(is_array($items['goods'])) { foreach($items['goods'] as $item) { ?>
                        <?php  if($isfirst == false) { ?>
                        <?php  echo $item['title'];?>
                        <?php  $isfirst = true;?>
                        <?php  } else { ?>
                        ,<?php  echo $item['title'];?>
                        <?php  } ?>
                    <?php  } } ?>
                </div>
            </a>
            <div class="list-item">
                <div class="total-amount ng-binding"><span class="amount-label">金额：</span>￥<?php  echo $items['totalprice'];?></div>
                <div class="operation">
                    <span class="button ng-binding ng-scope" style="color:<?php  if($items['dining_mode']==1) { ?>#28a267<?php  } else { ?>#ef4437;<?php  } ?>"><?php  if($items['dining_mode']==1) { ?>堂点<?php  } else if($items['dining_mode']==2) { ?>外卖<?php  } else if($items['dining_mode']==3) { ?>预订<?php  } else if($items['dining_mode']==4) { ?>快餐<?php  } ?></span>
                </div>
                <div class="space"></div>
            </div>
        </div>
        <?php  } } ?>
    </div>
</div>
<script>
    function jump(status) {
        window.location.href = "<?php  echo $this->createMobileUrl('order', array(), true)?>" + "&status=" + status;
    }

    function go_detail(id) {
        window.location.href = "<?php  echo $this->createMobileUrl('orderdetail', array(), true)?>" + "&orderid=" + id;
    }
</script>
</body>
</html>