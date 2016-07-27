<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<style>
    /*top1.html*/
    .topleft1{background-color:#f8f8f8; height:58px; border:1px solid #ebebeb;margin-bottom: 10px;}
    .topright1 li{display:inline-block; line-height:60px; font-size:16px; color:#666; width:210px; padding-left:10px;}
    .topright1 li a{font-size:16px;}
    .xian{border-left:1px solid #DCDCDC; line-height:45px; display:block; padding-left:10px;}
    .topright1 li img{margin-left:5px; width:28px; vertical-align:middle; margin-top:-2px;}
</style>
<?php  if(!empty($storeid)) { ?>
<?php  echo $this -> set_tabbar($action, $storeid);?>
<?php  } else { ?>
<ul class="nav nav-tabs">
    <li><a href="{$this->createWebUrl('stores', array('op' => 'display'))}"></a></li>
    <li class="active"><a href="#">订单管理</a></li>
</ul>
<?php  } ?>
<?php  if($operation == 'display') { ?>
<style>
    .page-nav {
        margin: 0;
        width: 100%;
        min-width: 800px;
    }

    .page-nav > li > a {
        display: block;
    }

    .page-nav-tabs {
        background: #EEE;
    }

    .page-nav-tabs > li {
        line-height: 40px;
        float: left;
        list-style: none;
        display: block;
        text-align: -webkit-match-parent;
    }

    .page-nav-tabs > li > a {
        font-size: 14px;
        color: #666;
        height: 40px;
        line-height: 40px;
        padding: 0 10px;
        margin: 0;
        border: 1px solid transparent;
        border-bottom-width: 0px;
        -webkit-border-radius: 0;
        -moz-border-radius: 0;
        border-radius: 0;
    }

    .page-nav-tabs > li > a, .page-nav-tabs > li > a:focus {
        border-radius: 0 !important;
        background-color: #f9f9f9;
        color: #999;
        margin-right: -1px;
        position: relative;
        z-index: 11;
        border-color: #c5d0dc;
        text-decoration: none;
    }

    .page-nav-tabs >li >a:hover {
        background-color: #FFF;
    }

    .page-nav-tabs > li.active > a, .page-nav-tabs > li.active > a:hover, .page-nav-tabs > li.active > a:focus {
        color: #576373;
        border-color: #c5d0dc;
        border-top: 2px solid #4c8fbd;
        border-bottom-color: transparent;
        background-color: #FFF;
        z-index: 12;
        margin-top: -1px;
        box-shadow: 0 -2px 3px 0 rgba(0, 0, 0, 0.15);
    }
</style>
<div class="main">
    <div class="panel panel-info">
        <div class="panel-heading">筛选</div>
        <div class="panel-body">
            <form action="./index.php" method="get" class="form-horizontal" role="form">
                <input type="hidden" name="c" value="site" />
                <input type="hidden" name="a" value="entry" />
                <input type="hidden" name="m" value="weisrc_dish" />
                <input type="hidden" name="do" value="order" />
                <input type="hidden" name="op" value="display" />
                <input type="hidden" name="storeid" value="<?php  echo $storeid;?>" />
                <div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width:100px;">订单号</label>
                    <div class="col-sm-2 col-lg-3">
                        <input class="form-control" name="ordersn" id="" type="text" value="<?php  echo $_GPC['ordersn'];?>">
                    </div>
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label" style="width: 100px;">客户手机</label>
                    <div class="col-sm-2 col-lg-3">
                        <input class="form-control" name="tel" id="" type="text" value="<?php  echo $_GPC['tel'];?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width:100px;">订单状态</label>
                    <div class="col-sm-8 col-lg-3 col-xs-12">
                        <select name="status" class="form-control">
                            <option value="">不限</option>
                            <option value="3" <?php  if($_GPC['status'] == 3) { ?> selected="selected" <?php  } ?>>已完成</option>
                            <option value="1" <?php  if($_GPC['status'] == 1) { ?> selected="selected" <?php  } ?>>已确认</option>
                            <option value="0" <?php  if($_GPC['status'] == 0 && isset($_GPC['status']) && $_GPC['status'] != '') { ?> selected="selected" <?php  } ?>>待处理</option>
                            <option value="-1" <?php  if($_GPC['status'] == -1) { ?> selected="selected" <?php  } ?>>已取消</option>
                        </select>
                    </div>
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label" style="width: 100px;">客户姓名</label>
                    <div class="col-sm-2 col-lg-3">
                        <input class="form-control" name="username" id="" type="text" value="<?php  echo $_GPC['username'];?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width: 100px;">支付方式</label>
                    <div class="col-sm-7 col-lg-3 col-xs-12">
                        <select name="paytype" class="form-control">
                            <option value="">不限</option>
                            <option value="0" <?php  if($_GPC['paytype'] == 0 && isset($_GPC['paytype']) && $_GPC['paytype'] != '') { ?> selected="selected" <?php  } ?>>未确认</option>
                            <option value="1" <?php  if($_GPC['paytype'] == 1) { ?> selected="selected" <?php  } ?>>余额支付</option>
                            <option value="2" <?php  if($_GPC['paytype'] == 2) { ?> selected="selected" <?php  } ?>>在线支付</option>
                            <option value="3" <?php  if($_GPC['paytype'] == 3) { ?> selected="selected" <?php  } ?>>现金付款</option>
                        </select>
                    </div>
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width: 100px;">下单时间</label>
                    <div class="col-sm-7 col-lg-3 col-xs-12">
                        <?php  echo tpl_form_field_daterange('time', array('starttime'=>date('Y-m-d', $starttime),'endtime'=>date('Y-m-d', $endtime)));?>
                    </div>
                    <div class="col-sm-3 col-lg-3" style="width: 18%;">
                        <button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
                        <button class="btn btn-success" name="out_put" value="output"><i class="fa fa-file"></i> 导出</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="panel panel-default">
        <form action="" method="post" class="form-horizontal form" >
            <input type="hidden" name="storeid" value="<?php  echo $storeid;?>" />
            <div class="table-responsive panel-body">
                <table class="table table-hover">
                    <thead class="navbar-inner">
                    <tr>
                        <th style="width:12%;">订单号</th>
                        <th style="width:8%;">订单总额</th>
                        <th style="width:16%;">联系信息</th>
                        <th style="width:8%;">类型</th>
                        <th style="width:8%;">状态</th>
                        <th style="width:8%;">支付状态</th>
                        <th style="width:15%;">下单时间</th>
                        <th style="width:25%; text-align:right;"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php  if(is_array($list)) { foreach($list as $item) { ?>
                    <tr>
                        <td>
                            <?php  echo $item['ordersn'];?>
                        </td>
                        <td>￥<?php  echo $item['totalprice'];?></td>
                        <td>
                            <?php  echo $item['username'];?>
                            <br/><?php  echo $item['tel'];?>
                            <?php  if(!empty($item['address'])) { ?>
                            <br/><?php  echo $item['address'];?>
                            <?php  } ?>
                        </td>
                        <td>

                            <?php  if($item['dining_mode']==1) { ?><span class="btn btn-info btn-sm" title="堂点" style="background-color: #9585bf;border-color: #9585bf;"><i class="fa fa-cutlery"></i></span><?php  } ?>
                            <?php  if($item['dining_mode']==2) { ?><span class="btn btn-info btn-sm" title="外卖"  style="background-color: #4f99c6;border-color: #4f99c6;"><i class="fa fa-truck"></i></span><?php  } ?>
                            <?php  if($item['dining_mode']==3) { ?><span class="btn btn-info btn-sm" title="预定" style="background-color: #fee188;border-color: #fee188;"><i class="fa fa-calendar"></i></span><?php  } ?>
                            <?php  if($item['dining_mode']==4) { ?><span class="btn btn-info btn-sm" title="快餐" style="background-color: #be386a;border-color: #be386a;"><i class="fa fa-delicious"></i></span><?php  } ?>
                        </td>
                        <td>
                            <?php  if($item['status'] == 0) { ?><span class="label label-info">待处理</span><?php  } ?>
                            <?php  if($item['status'] == 1) { ?><span class="label label-warning">已确认</span><?php  } ?>
                            <?php  if($item['status'] == 2) { ?><span class="label label-success">已并台</span><?php  } ?>
                            <?php  if($item['status'] == 3) { ?><span class="label label-success">已完成</span><?php  } ?>
                            <?php  if($item['status'] == -1) { ?><span class="label label-danger">已取消</span><?php  } ?>
                        </td>
                        <td>
                            <?php  if($item['paytype'] == 0) { ?>未确认<?php  } ?>
                            <?php  if($item['paytype'] == 1) { ?>余额支付<?php  } ?>
                            <?php  if($item['paytype'] == 2) { ?>在线支付<?php  } ?>
                            <?php  if($item['paytype'] == 3) { ?>现金付款<?php  } ?>
                            <br/>
                            <?php  if($item['ispay'] == 0) { ?><span class="label label-default">未支付</span><?php  } ?>
                            <?php  if($item['ispay'] == 1) { ?><span class="label label-success">已支付</span><?php  } ?>
                        </td>
                        <td><?php  echo date("Y-m-d H:i:s", $item['dateline'])?></td>
                        <td style="text-align:left;">
                            <a class="btn btn-info btn-sm" href="<?php  echo $this->createWebUrl('order', array('op' => 'detail', 'id' => $item['id'], 'storeid' => $storeid))?>" title="详情">详情</a>
                            <?php  if($item['status'] != -1) { ?>
                            <a class="btn btn-warning btn-sm" href="<?php  echo $this->createWebUrl('order', array('op' => 'print', 'id' => $item['id'], 'storeid' => $storeid))?>" title="打印订单" onclick="return confirm('确认打印吗？');return false;">打印</a><?php  } ?>
                            <?php  if($item['ispay'] == 0 && $item['status'] != -1) { ?>
                            <a class="btn btn-danger btn-sm" href="<?php  echo $this->createWebUrl('order', array('op' => 'detail', 'id' => $item['id'], 'storeid' => $storeid, 'confrimpay' => 'confrimpay'))?>"  onclick="return confirm('确认设置该订单为完成支付吗？'); return false;" title="付款">付款</a>
                            <?php  } ?>
                            <?php  if($item['status'] == 0) { ?>
                            <a class="btn btn-info btn-sm" href="<?php  echo $this->createWebUrl('order', array('op' => 'detail', 'id' => $item['id'], 'storeid' => $storeid, 'confirm' => 'confirm'))?>"  onclick="return confirm('确认设置该订单为已确认吗？'); return false;" title="确认">确认</a><?php  } ?>
                            <?php  if($item['status'] == 1) { ?>
                            <?php  if($item['ispay'] == 0) { ?>
                            <a class="btn btn-success btn-sm" href="#"  onclick="alert('请先支付订单，再完成订单');return false;" title="完成">完成</a><?php  } else { ?><a class="btn btn-success btn-sm" href="<?php  echo $this->createWebUrl('order', array('op' => 'detail', 'id' => $item['id'], 'storeid' => $storeid, 'finish' => 'finish'))?>"  onclick="return confirm('确认设置该订单为完成支付吗？'); return false;" title="完成">完成</a>
                            <?php  } ?>
                            <?php  } ?>
                            <?php  if($item['status'] != -1) { ?>
                            <a class="btn btn-default btn-sm" href="<?php  echo $this->createWebUrl('order', array('op' => 'detail', 'id' => $item['id'], 'storeid' => $storeid, 'close' => 'close'))?>"  onclick="return confirm('确认取消订单吗？'); return false;" title="取消">取消</a>
                            <?php  } ?>

                            <!--<a class="btn btn-danger btn-sm" href="<?php  echo $this->createWebUrl('order', array('op' => 'delete', 'id' => $item['id'], 'storeid' => $storeid))?>" title="删除订单" onclick="return confirm('此操作不可恢复，确认删除？');return false;"><i class="fa fa-times"></i></a>-->
                            <?php  if(!empty($blacklist[$item['from_user']])) { ?>
                            <!--<a class="btn btn-default btn-sm" style="color:red;" href="<?php  echo $this->createWebUrl('order', array('op' => 'black', 'id' => $item['id'], 'storeid' => $storeid))?>" title="拉黑名单"><i class="fa fa-trash"></i></a>-->
                            <?php  } else { ?>
                            <!--<a class="btn btn-default btn-sm" href="<?php  echo $this->createWebUrl('order', array('op' => 'black', 'id' => $item['id'], 'storeid' => $storeid))?>" title="拉黑名单"><i class="fa fa-trash"></i></a>-->
                            <?php  } ?>
                        </td>
                    </tr>
                    <?php  } } ?>
                    </tbody>
                </table>
                <?php  echo $pager;?>
            </div>
        </form>
    </div>
    </form>
</div>
<?php  } else if($operation == 'detail') { ?>
<style>
    .text-currency {
        color: #f60;
    }
    .big {
        font-size: 120%;
    }
    .form-group {
        margin-bottom: 0px;
    }
</style>
<style type="text/css">
    .rank img{width:16px; height:16px;}
    ul.order-process li {float : left; width : 33%; text-align : center; overflow : hidden;}
    ul.order-process li p {margin-bottom : 10px;}
    ul.order-process .order-process-time {color : #CCC;}
    ul.order-process .order-process-state {color : #999;}
    ul.order-process .square {display : inline-block; width : 20px; height : 20px; border-radius : 10px; background-color : #E6E6E6; color : #FFF;font-style : normal; position : absolute; left : 50%; z-index : 2; top : 50%; margin : -10px 0 0 -10px;}
    ul.order-process .square.finish{padding-top:2px; padding-right:2px;}
    ul.order-process .bar {position : relative; height : 20px;}
    ul.order-process .bar:after {content : " "; display : block; width : 100%; height : 4px; background-color : #E6E6E6; position : absolute; top : 50%; margin-top : -2px; z-index : 1;}
    ul.order-process li:first-child .bar:after {margin-left : 50%;}
    ul.order-process li:last-child .bar:after {margin-left :-50%;}
    ul.order-process .active .square,ul.order-process .active .bar:after {background-color : #80CCFF;}
    ul.order-process .active .order-process-state {color : #80CCFF;}
    .order-detail-info>div{margin-bottom:10px; padding-left:15px;}
    .page-trade-order h4{font-size:14px; font-weight:700;}
    .page-trade-order .form-group{margin-bottom:0;}
    .page-trade-order .form-group .control-label{font-weight:normal; color:#999;}
    .page-trade-order .order-infos{border-right:1px solid #ddd;}
    .page-trade-order .parting-line{height:1px;border-top:1px dashed #e5e5e5; margin:3px 0;}
    .page-trade-order .order-state{padding-left:40px; position:relative; margin:20px 0 40px;}
    .page-trade-order .order-state>span{color:#07d; position:absolute; left:0; top:5px; font-size:25px; display:inline-block; width:30px; height:30px; border:1px solid #07d; border-radius:30px; text-align:center; line-height:30px;}
    #close-order ul li{padding:5px 15px; cursor:pointer;}
    #close-order ul li:hover{background:#eee;}
    .fix a.js-order-edit-address{display:none; color:red;}
    .fix:hover a.js-order-edit-address{display:inline;}
    .page-trade-order .col-sm-9{word-break: break-word; overflow:hidden;}
</style>
<div class="main">
    <div class="freight-content">
        <div class="freight-template-item panel panel-default">
            <div class="panel-heading clearfix">
                <div class="pull-left">
                    <strong>订单号：<?php  echo $item['ordersn'];?></strong>
                </div>
            </div>
            <div class="panel-body table-responsive collapse in" id="freight-template-item-0" style="padding:0;  overflow-y:hidden;">
                <div style="margin-top:20px;">
                    <ul class="order-process clearfix">
                        <li class="active">
                            <p class="order-process-state">买家下单</p>
                            <p class="bar"><i class="square finish">√</i></p>
                            <p class="order-process-time"><?php  echo date('Y-m-d H:i:s', $item['dateline'])?></p>
                        </li>
                        <?php  if($item['status'] != -1) { ?>
                        <li <?php  if($item['status'] == 1 || $item['status'] == 3) { ?>class="active"<?php  } ?>>
                        <p class="order-process-state">已确认</p>
                        <p class="bar"><i class="square finish">√</i></p>
                        <p class="order-process-time"></p>
                        </li>
                        <li <?php  if($item['status'] == 3) { ?>class="active"<?php  } ?>>
                        <p class="order-process-state">交易完成</p>
                        <p class="bar"><i class="square">√</i></p>
                        <p class="order-process-time"></p>
                        </li>
                        <?php  } else { ?>
                        <li class="active">
                        <p class="order-process-state"></p>
                        <p class="bar"></p>
                        <p class="order-process-time"></p>
                        </li>
                        <li class="active">
                            <p class="order-process-state">已关闭</p>
                            <p class="bar"><i class="square">√</i></p>
                            <p class="order-process-time"></p>
                        </li>
                        <?php  } ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="page-trade-order">
        <div class="order-list">
            <div class="freight-content">
                <div class="freight-template-item panel panel-default">
                    <div class="panel-body clearfix">
                        <form class="form-horizontal form" action="" method="post" enctype="multipart/form-data">
                            <div class="col-xs-12 col-sm-6 order-infos">
                                <h4>订单信息</h4>
                                <div class="form-group clearfix">
                                    <label class="col-xs-3 col-sm-3 control-label">订单编号：</label>
                                    <div class="col-xs-9 col-sm-9 form-control-static">
                                        <?php  echo $item['ordersn'];?>
                                    </div>
                                </div>
                                <div class="form-group clearfix">
                                    <label class="col-xs-3 col-sm-3 control-label">支付流水：</label>
                                    <div class="col-xs-9 col-sm-9 form-control-static">
                                        <?php  if(empty($item['transid'])) { ?>-<?php  } else { ?><?php  echo $item['transid'];?><?php  } ?>
                                    </div>
                                </div>
                                <div class="form-group clearfix">
                                    <label class="col-xs-3 col-sm-3 control-label">订单类型：</label>
                                    <div class="col-xs-9 col-sm-9 form-control-static">
                                        <b><span class="text-currency big"><?php  if($item['dining_mode']==1) { ?>堂点<?php  } ?>
                                            <?php  if($item['dining_mode']==2) { ?>外卖<?php  } ?>
                                            <?php  if($item['dining_mode']==3) { ?>预订<?php  } ?>
                                            <?php  if($item['dining_mode']==4) { ?>快餐<?php  } ?>
                                                </span></b>
                                    </div>
                                </div>
                                <div class="form-group clearfix">
                                    <label class="col-xs-3 col-sm-3 control-label">付款类型：</label>
                                    <div class="col-xs-9 col-sm-9 form-control-static">
                                        <?php  if($item['paytype'] == 0) { ?>未确认<?php  } ?>
                                        <?php  if($item['paytype'] == 1) { ?>余额支付<?php  } ?>
                                        <?php  if($item['paytype'] == 2) { ?>在线支付<?php  } ?>
                                        <?php  if($item['paytype'] == 3) { ?>现金付款<?php  } ?>
                                        <?php  if($item['ispay']==1) { ?>(<font color="#228b22">已支付</font>)<?php  } else { ?>(<font color="#b22222">未支付</font>)<?php  } ?>
                                    </div>
                                </div>

                                <div class="form-group clearfix hidden">
                                    <label class="col-xs-3 col-sm-3 control-label">买家：</label>
                                    <div class="col-xs-9 col-sm-9 form-control-static">
                                        非粉丝
                                    </div>
                                </div>
                                <div class="parting-line"></div>
                                <div class="form-group clearfix">
                                    <label class="col-xs-3 col-sm-3 control-label">下单时间：</label>
                                    <div class="col-xs-9 col-sm-9 form-control-static">
                                        <?php  echo date('Y-m-d H:i:s', $item['dateline'])?>
                                    </div>
                                </div>
                                <?php  if($item['dining_mode']==2) { ?>
                                <div class="form-group clearfix">
                                    <label class="col-xs-3 col-sm-3 control-label">配送时间：</label>
                                    <div class="col-xs-9 col-sm-9 form-control-static">
                                        <?php  echo $item['meal_time'];?>
                                    </div>
                                </div>
                                <?php  } ?>
                                <div class="form-group clearfix">
                                    <label class="col-xs-3 col-sm-3 control-label">收货信息：</label>
                                    <div class="col-xs-9 col-sm-9 form-control-static fix">
                                        <p class="js-receive-address" order-id="164">
                                            <span><?php  echo $item['username'];?> <?php  echo $item['tel'];?> <?php  echo $item['address'];?>(<a href="<?php  echo $this->createWebUrl('fans', array('id' => $fans['id'], 'op' => 'post', 'storeid' => $storeid))?>">查看用户</a>)</span>
                                        </p>
                                    </div>
                                </div>
                                <?php  if($item['dining_mode']==1) { ?>
                                <div class="form-group clearfix">
                                    <label class="col-xs-3 col-sm-3 control-label">用餐人数：</label>
                                    <div class="col-xs-9 col-sm-9 form-control-static">
                                        <?php  echo $item['counts'];?>
                                    </div>
                                </div>
                                <div class="form-group clearfix">
                                    <label class="col-xs-3 col-sm-3 control-label">桌台信息：</label>
                                    <div class="col-xs-9 col-sm-9 form-control-static">
                                        <?php  echo $table_title;?>
                                    </div>
                                </div>
                                <?php  } ?>
                                <?php  if($item['dining_mode']==3) { ?>
                                <div class="form-group">
                                    <label class="col-xs-12 col-sm-3 control-label">预订时间：</label>
                                    <div class="col-sm-9">
                                        <p class="form-control-static">
                                            <?php  echo $tablezones['title'];?> <?php  echo $item['meal_time'];?>
                                        </p>
                                    </div>
                                </div>
                                <?php  } ?>
                                <div class="parting-line"></div>
                                <div class="form-group clearfix">
                                    <label class="col-xs-3 col-sm-3 control-label">买家留言：</label>
                                    <div class="col-xs-9 col-sm-9 form-control-static">
                                        <?php  echo $item['remark'];?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <div class="order-state">
                                    <span><i class="fa fa-exclamation"></i></span>
                                    <h4>
                                        订单状态 : <span id="order_status_text" class="big">
                                        <?php  if($item['status'] == 0) { ?>待处理<?php  } ?>
                                        <?php  if($item['status'] == 1) { ?>已确认<?php  } ?>
                                        <?php  if($item['status'] == 2) { ?>已并台<?php  } ?>
                                        <?php  if($item['status'] == 3) { ?>已完成<?php  } ?>
                                        <?php  if($item['status'] == -1) { ?>已关闭<?php  } ?>
                                    </span>
                                    </h4>
                                    <!--<h5 class="text-gray" id="order_status_content">系统关闭订单</h5>-->
                                    <!--<h5 class="js-cancel-reason b">关闭原因 : 超时未付款被系统关闭</h5>-->
                                </div>
                                <div style="padding:0 0 30px 40px;" class="clearfix">
                                    <div class="pull-left">
                                        <a href="javascript:;" class="js-order-remark" order-id="164" onclick="$('#order-remark-container').modal();">[备注]</a>
                                    </div>&nbsp;
                                    <div class="clearfix pull-left">

                                    </div>
                                </div>
                                <div class="form-group clearfix js-fee">
                                    <label class="col-xs-3 col-sm-3 control-label">总金额：</label>
                                    <div class="col-xs-9 col-sm-9 form-control-static">
                                        <b><span class="text-currency big">￥</span><span class="js-payment text-currency big"><?php  echo $item['totalprice'];?></span></b> ( 货价:<span class="js-total-fee"><?php  echo $item['goodsprice'];?></span> + 配送费:<span><?php  echo $item['dispatchprice'];?></span>)
                                    </div>
                                </div>

                                <div class="form-group clearfix js-fee">
                                    <label class="col-xs-3 col-sm-3 control-label">改价：</label>
                                    <div class="col-sm-3 col-xs-12">
                                        <input type="text" name="updateprice" id="updateprice" class="form-control" value="<?php  echo $item['price'];?>" />
                                    </div>
                                    <div class="col-sm-3 col-xs-12">
                                        <button type="submit" class="btn btn-danger span2" name="confirmprice" value="yes" onclick="return confirm('确认操作？');">修改</button>
                                    </div>
                                </div>

                                <?php  if($item['credit']) { ?>
                                <div class="form-group clearfix js-fee">
                                    <label class="col-xs-3 col-sm-3 control-label">赠送积分：</label>
                                    <div class="col-xs-9 col-sm-9 form-control-static">
                                        <b><span class="js-payment"><?php  echo $item['credit'];?></span></b>
                                    </div>
                                </div>
                                <?php  } ?>
                                <?php  if(!empty($item['paydetail'])) { ?>
                                <div class="form-group clearfix js-fee">
                                    <label class="col-xs-3 col-sm-3 control-label">付款详情：</label>
                                    <div class="col-xs-9 col-sm-9 form-control-static">
                                        <?php  echo $item['paydetail'];?>
                                    </div>
                                </div>
                                <?php  } ?>
                                <div class="parting-line"></div>
                                <div class="form-group clearfix">
                                    <label class="col-xs-3 col-sm-3 control-label">卖家备注：</label>
                                    <div class="col-xs-9 col-sm-9 form-control-static js-admin-remark">
                                        <?php  if(empty($item['reply'])) { ?>-<?php  } else { ?><?php  echo $item['reply'];?><?php  } ?>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
	<form class="form-horizontal form" action="" method="post" enctype="multipart/form-data">
		<input type="hidden" name="id" value="<?php  echo $item['id'];?>">
        <div class="panel panel-default">
            <div class="panel-heading">
                清单
            </div>
            <div class="table-responsive panel-body">
		    <table class="table table-hover">
			<thead class="navbar-inner">
				<tr>
					<th style="width:10%;">ID</th>
					<th style="width:15%;">商品名称</th>
                    <th style="text-align:center;width:15%;">图片</th>
                    <th style="text-align:center;width:15%;">单价(元)</th>
                    <th style="text-align:center;width:15%;">奖励积分</th>
					<th style="text-align:center;width:15%;">数量</th>
					<th style="text-align:center; width:15%;">小计(元)</th>
				</tr>
			</thead>
            <?php  $totalprice = 0;?>
			<?php  if(is_array($goods)) { foreach($goods as $row) { ?>
			<tr>
				<td><?php  echo $row['id'];?></td>
                <td><?php  if(!empty($category[$row['pcate']])) { ?><span class="text-error">[<?php  echo $category[$row['pcate']]['name'];?>] </span><?php  } ?>
                    <a href="<?php  echo $this->createWebUrl('goods', array('id' => $row['id'], 'op' => 'post', 'storeid' => $item['storeid']))?>"><?php  echo $row['title'];?></a></td>
                <td style="text-align:center;">
                    <img src="<?php  echo tomedia($row['thumb']);?>" width="50" />
                </td>
                <td style="text-align:center;">
                    <?php  echo $row['price'];?>
                </td>
                <td style="text-align:center;">
                    <?php  echo $row['credit'];?>
                </td>
				<td style="text-align:center;">
                    <?php  echo $row['total'];?>
                </td>
                <td style="text-align:center;">
                    <?php  $price = floatval($row['price']);?>
                    <?php  $total = intval($row['total']);?>
                    <?php  $goodprice = $price * $total;?>
                    <?php  $totalprice = $totalprice+$goodprice;?>
                    <?php  echo $goodprice?>
				</td>
			</tr>
			<?php  } } ?>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td style="text-align:center;font-weight: bold;">合计</td>
                <td style="text-align:center;font-weight: bold;">
                    <?php  echo $totalprice;?>
                </td>
            </tr>
		    </table>
            </div>
        </div>
        <div class="modal fade" id="order-remark-container" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">×</span></button>
                        <h4 class="modal-title">卖家备注</h4></div>
                    <div class="modal-body">
                        <textarea name="reply" class="form-control" rows="5" oninput="$(this).parent().next().find('.js-count').text(255 - $(this).val().length);;" onpropertychange="$(this).parent().next().find('.js-count').text(255 - $(this).val().length);;" maxlength="255" placeholder="最多填写 255 字"></textarea>
                    </div>
                    <div class="modal-footer" style="padding: 5px 15px;">
                <span class="help-block pull-left">					您还可以输入：<storng>
                    <span style="color:red; font-size:18px;" name="count" class="js-count">255</span></storng> 个字符</span>
                        <a class="btn btn-default js-cancel" data-dismiss="modal">取消</a>
                        <button type="submit" class="btn btn-primary" name="confrimsign" value="正常">提交</button>
                        <!--<a class="btn btn-primary js-order-remark-post">确定</a>-->
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group col-sm-12" style="margin-bottom: 15px;">
            <?php  if($item['ispay']==0) { ?>
            <button type="submit" class="btn btn-danger span2" onclick="return confirm('确认设置该订单为完成支付吗？'); return false;" name="confrimpay"  value="确认付款">确认付款</button>
            <?php  } ?>
            <?php  if($item['status'] == 0) { ?>
            <button type="submit" class="btn btn-primary span2" onclick="return confirm('确认设置该订单为已确认吗？'); return false;" name="confirm" value="确认订单">确认订单</button>
            <?php  } ?>
            <?php  if($item['status'] == 1) { ?>
            <?php  if($item['ispay'] == 0) { ?>
            <a class="btn btn-success span2" href="#"  onclick="alert('请先支付订单，再完成订单');return false;" title="完成">完成订单</a><?php  } else { ?><a class="btn btn-success span2" href="<?php  echo $this->createWebUrl('order', array('op' => 'detail', 'id' => $item['id'], 'storeid' => $storeid, 'finish' => 'finish'))?>"  onclick="return confirm('确认设置该订单为完成支付吗？'); return false;" title="完成">完成订单</a>
            <?php  } ?>
            <?php  } ?>
            <?php  if($item['status'] != -1) { ?>
            <button type="submit" class="btn span2" name="close" onclick="return confirm('确认关闭此订单吗？'); return false;" value="关闭">关闭订单</button>
            <?php  } else { ?>
            <button type="submit" class="btn span2 btn-primary" name="cancelpay" onclick="return confirm('确认开启此订单吗？'); return false;" value="关闭">开启订单</button>
            <?php  } ?>
            <input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
        </div>
	</form>
</div>

<script>

</script>
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>