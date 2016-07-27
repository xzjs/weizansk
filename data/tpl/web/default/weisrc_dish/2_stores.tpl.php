<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<ul class="nav nav-tabs">
    <?php  if(empty($returnid)) { ?>
    <li <?php  if($operation == 'post') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('stores', array('op' => 'post'))?>">添加门店</a></li>
    <?php  } ?>
    <li <?php  if($operation == 'display') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('stores', array('op' => 'display'))?>">门店管理</a></li>
    <?php  if($_W['isfounder']) { ?>
    <li <?php  if($operation == 'setting') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('stores', array('op' => 'setting'))?>">门店配置</a></li>
    <?php  } ?>
</ul>
<?php  if($operation == 'display') { ?>
<style>
    .form-control-excel {
        height: 34px;
        padding: 6px 12px;
        font-size: 14px;
        line-height: 1.42857143;
        color: #555;
        background-color: #fff;
        background-image: none;
        border: 1px solid #ccc;
        border-radius: 4px;
        -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
        box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
        -webkit-transition: border-color ease-in-out .15s,-webkit-box-shadow ease-in-out .15s;
        -o-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
        transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
    }
</style>
<div class="main">
    <?php  if(empty($returnid)) { ?>
    <?php  if(!empty($config['storecount']) && $config['storecount'] > 1) { ?>
    <?php  $tmpcount = intval($config['storecount']) - $total;?>
    <div class="alert alert-info">
        您当前使用的 [ 多店标准版 ] 可创建门店数量最多为<font color="#f00"><?php  echo $config['storecount'];?></font>家，已添加<font color="#f00"><?php  echo $total;?></font>家，还能添加<font color="#f00"><?php  echo $tmpcount;?></font>家
    </div>
    <?php  } else if($config['storecount']==1) { ?>
    <div class="alert alert-info">
        您当前使用的 [ 单店标准版 ] 可创建门店数量最多为<font color="#f00">1</font>家
    </div>
    <?php  } ?>
    <div class="panel panel-default" id="uploaddata" style="display: none;">
        <div class="panel-body">
            <form action="./index.php" method="post" class="form-horizontal form" enctype="multipart/form-data">
                <input type="hidden" name="leadExcel" value="true">
                <input type="hidden" name="c" value="site" />
                <input type="hidden" name="a" value="entry" />
                <input type="hidden" name="m" value="weisrc_dish" />
                <input type="hidden" name="do" value="UploadExcel" />
                <input type="hidden" name="ac" value="store" />
                &nbsp;<a class="btn btn-primary" href="javascript:location.reload()"><i class="fa fa-refresh"></i> 刷新</a>
                <input name="viewfile" id="viewfile" type="text" value="" style="margin-left: 40px;" class="form-control-excel" readonly>
                <a class="btn btn-primary"><label for="unload" style="margin: 0px;padding: 0px;">浏览...</label></a>
                <input type="file" class="pull-left btn-primary span3" name="inputExcel" id="unload" style="display: none;"
                       onchange="document.getElementById('viewfile').value=this.value;this.style.display='none';">
                <input type="submit" class="btn btn-primary " value="上传">
                <a class="btn btn-primary" href="../addons/weisrc_dish/example/example_store.xls">下载导入模板</a>
            </form>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">筛选</div>
        <div class="panel-body">
            <form action="./index.php" method="get" class="form-horizontal" role="form">
                <input type="hidden" name="c" value="site" />
                <input type="hidden" name="a" value="entry" />
                <input type="hidden" name="m" value="weisrc_dish" />
                <input type="hidden" name="do" value="stores" />
                <input type="hidden" name="op" value="display" />
                <div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">关键字</label>
                    <div class="col-sm-2 col-lg-2">
                        <input class="form-control" name="keyword" id="" type="text" value="<?php  echo $_GPC['keyword'];?>" placeholder="请输入门店名称">
                    </div>
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label" style="width: 100px;">门店类型</label>
                    <div class="col-sm-2 col-lg-2">
                        <select class="form-control" style="margin-right:15px;" name="shoptypeid" autocomplete="off">
                            <option value="0">请选择门店类型</option>
                            <?php  if(is_array($shoptype)) { foreach($shoptype as $row) { ?>
                            <option value="<?php  echo $row['id'];?>" <?php  if($row['id'] == $shoptypeid) { ?> selected="selected"<?php  } ?>><?php  echo $row['name'];?></option>
                            <?php  } } ?>
                        </select>
                    </div>
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label" style="width: 100px;">所属区域</label>
                    <div class="col-sm-2 col-lg-2">
                        <select class="form-control" style="margin-right:15px;" name="areaid" autocomplete="off">
                            <option value="0">请选择所属区域</option>
                            <?php  if(is_array($area)) { foreach($area as $row) { ?>
                            <option value="<?php  echo $row['id'];?>" <?php  if($row['id'] == $areaid) { ?> selected="selected"<?php  } ?>><?php  echo $row['name'];?></option>
                            <?php  } } ?>
                        </select>
                    </div>
                    <div class="col-sm-2 col-lg-2">
                        <button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
                        <a class="btn btn-success" href="#" onclick="$('#uploaddata').slideToggle();">批量导入</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php  } ?>
    <div class="panel panel-default">
        <div class="table-responsive panel-body">
            <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
                <table class="table table-hover">
                    <thead class="navbar-inner">
                    <tr>
                        <th style="width:10%;">顺序</th>
                        <th style="width:15%;">门店名称</th>
                        <th style="width:15%;">电话</th>
                        <th style="width:24%;">地址</th>
                        <th style="width:12%;">订餐类型</th>
                        <th style="width:8%;">状态</th>
                        <th style="width:18%;text-align: center;">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php  if(is_array($storeslist)) { foreach($storeslist as $item) { ?>
                    <tr>
                        <td><input type="text" class="form-control" name="displayorder[<?php  echo $item['id'];?>]" value="<?php  echo $item['displayorder'];?>"></td>
                        <td><a href="<?php  echo $this->createWebUrl('stores', array('id' => $item['id'], 'storeid' =>  $item['id'], 'op' => 'post'))?>" title="管理">
                            <img src="<?php  if(strstr($item['logo'], 'http') || strstr($item['logo'], './source/modules/')) { ?><?php  echo $item['logo'];?><?php  } else { ?><?php  echo $_W['attachurl'];?><?php  echo $item['logo'];?><?php  } ?>" onerror="this.src='./resource/images/nopic.jpg';" width="60px;" style="border-radius: 3px;">
                            <br/><?php  echo $item['title'];?></a>
                        </td>
                        <td><?php  echo $item['tel'];?></td>
                        <td><?php  echo $item['address'];?></td>
                        <td style="white-space:normal;">
                            <?php  if(!empty($item['is_meal'])) { ?><span class="label" style="background:#ff6a00;">店内</span><?php  } ?>
                            <?php  if(!empty($item['is_delivery'])) { ?><span class="label" style="background:#ff6a00;">外卖</span><?php  } ?>
                            <?php  if(!empty($item['is_snack'])) { ?><span class="label" style="background:#ff6a00;">快餐</span><?php  } ?>
                            <?php  if(!empty($item['is_reservation'])) { ?><span class="label" style="background:#ff6a00;">预定</span><?php  } ?>
                            <?php  if(!empty($item['is_queue'])) { ?><span class="label" style="background:#ff6a00;">排队</span><?php  } ?>
                            <?php  if(!empty($item['is_intelligent'])) { ?><span class="label" style="background:#ff6a00;">套餐</span><?php  } ?>
                        </td>
                        <td style="width:60px;">
                            <?php  if($item['is_show']==1) { ?>
                            <span class="label" style="background:#56af45;">启用</span>
                            <?php  } else { ?>
                            <span class="label" style="background:#f00;">禁用</span>
                            <?php  } ?>
                        </td>
                        <td style="max-width:70px;text-align: right;">
                            <a class="btn btn-default btn-sm" href="<?php  echo $this->createWebUrl('order', array('id' => $item['id'], 'storeid' =>  $item['id']))?>" title="管理">管理</a>
                            <a class="btn btn-default btn-sm" href="<?php  echo $this->createWebUrl('stores', array('id' => $item['id'], 'storeid' =>  $item['id'], 'op' => 'post'))?>" title="编辑">编辑</a>
                            <?php  if(empty($returnid)) { ?>
                            <a class="btn btn-default btn-sm" onclick="return confirm('确认删除吗？');return false;" href="<?php  echo $this->createWebUrl('stores', array('id' => $item['id'], 'storeid' =>  $item['id'], 'op' => 'delete'))?>" title="删除">删除</a>
                            <?php  } ?>
                        </td>
                    </tr>
                    <?php  } } ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="7">
                            <input name="submit" type="submit" class="btn btn-primary" value="批量排序">
                            <input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
                        </td>
                    </tr>
                    </tfoot>
                </table>
            </form>
        </div>
    </div>
    <?php  echo $pager;?>
</div>
<script>
    function drop_confirm(msg, url){
        if(confirm(msg)){
            window.location = url;
        }
    }
</script>
<?php  } else if($operation == 'post') { ?>
<style>
    .item_box img{
        width: 100%;
        height: 100%;
    }
</style>
<script type="text/html" id="time-form-html">
    <?php  include $this->template('_time_item');?>
</script>
<div class="main">
    <?php  if(!empty($reply['id'])) { ?>
    <div class="panel panel-default account">
        <div class="panel-body">
            <p style="margin: 0px"><strong>门店网址 :</strong> <a href="javascript:;" title="点击复制Token"><?php echo $_W['siteroot'] . 'app/index.php?i=' . $reply['weid'] . '&c=entry&id=' . $reply['id'] . '&do=detail&m=weisrc_dish'?></a></p>
            <p style="margin: 0px"><strong>外卖网址 :</strong> <a href="javascript:;" title="点击复制Token"><?php echo $_W['siteroot'] . 'app/index.php?i=' . $reply['weid'] . '&c=entry&storeid=' . $reply['id'] . '&do=waplist&m=weisrc_dish&mode=2'?></a></p>
            <p style="margin: 0px"><strong>快餐网址 :</strong> <a href="javascript:;" title="点击复制Token"><?php echo $_W['siteroot'] . 'app/index.php?i=' . $reply['weid'] . '&c=entry&storeid=' . $reply['id'] . '&do=waplist&m=weisrc_dish&mode=4'?></a></p>
            <p style="margin: 0px"><strong>排号网址 :</strong> <a href="javascript:;" title="点击复制Token"><?php echo $_W['siteroot'] . 'app/index.php?i=' . $reply['weid'] . '&c=entry&storeid=' . $reply['id'] . '&do=queue&m=weisrc_dish'?></a></p>
        </div>
    </div>
    <?php  } ?>
    <script>
        require(['jquery', 'util'], function($, u){
            $('.account p a').each(function(){
                u.clip(this, $(this).text());
            });
        });
    </script>
    <form action="" method="post" onsubmit="return check();" class="form-horizontal form" enctype="multipart/form-data">
        <div class="panel panel-default">
            <div class="panel-heading">
                门店信息
            </div>
            <div class="panel-body">
                <ul class="nav nav-tabs" id="myTab">
                    <li class="active"><a href="#tab_basic">基本信息</a></li>
                    <li><a href="#tab_high">高级设置</a></li>
                    <li><a href="#tab_out">外送设置</a></li>
                    <li><a href="#tab_nave_text">个性化信息</a></li>
                    <li><a href="#tab_link">外链设置</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane  active" id="tab_basic">

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">门店名称</label>
                            <div class="col-sm-9">
                                <input type="text" name="title" value="<?php  echo $reply['title'];?>" id="title" class="form-control" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">门店Logo</label>
                            <div class="col-sm-9">
                                <?php  echo tpl_form_field_image('logo', $reply['logo'])?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">公告</label>
                            <div class="col-sm-9">
                                <input type="text" name="announce" value="<?php  echo $reply['announce'];?>" id="announce" class="form-control" />
                                <div class="help-block">在门店详细页显示</div>
                            </div>

                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">门店简介</label>
                            <div class="col-sm-9">
                                <input type="text" name="info" value="<?php  echo $reply['info'];?>" id="info" class="form-control" />
                                <div class="help-block">在门店列表显示</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">门店类型</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="type" id="type">
                                    <option value="0">请选择</option>
                                    <?php  if(is_array($shoptype)) { foreach($shoptype as $item) { ?>
                                    <option value="<?php  echo $item['id'];?>" <?php  if($reply['typeid']==$item['id']) { ?>selected<?php  } ?>><?php  echo $item['name'];?></option>
                                    <?php  } } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">所属区域</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="area" id="area">
                                    <option value="0">请选择</option>
                                    <?php  if(is_array($area)) { foreach($area as $item) { ?>
                                    <option value="<?php  echo $item['id'];?>" <?php  if($reply['areaid']==$item['id']) { ?>selected<?php  } ?>><?php  echo $item['name'];?></option>
                                    <?php  } } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">门店级别</label>
                            <div class="col-sm-9">
                                <select name="level" id="level" class="form-control">
                                    <option value="1"<?php  if($reply['level']==1) { ?> selected<?php  } ?>>★</option>
                                    <option value="2"<?php  if($reply['level']==2) { ?> selected<?php  } ?>>★★</option>
                                    <option value="3"<?php  if(empty($reply) || $reply['level']==3) { ?> selected<?php  } ?>>★★★</option>
                                    <option value="4"<?php  if($reply['level']==4) { ?> selected<?php  } ?>>★★★★</option>
                                    <option value="5"<?php  if($reply['level']==5) { ?> selected<?php  } ?>>★★★★★</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">人均消费</label>
                            <div class="col-sm-9">
                                <input type="text" name="consume" class="form-control" value="<?php  if(empty($reply)) { ?>20.0<?php  } else { ?><?php  echo $reply['consume'];?><?php  } ?>" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">营业时间</label>
                            <div class="col-sm-3">
                                <div class="input-group clockpicker">
                                    <input type="text" class="form-control" value="<?php  echo $reply['begintime'];?>" name="begintime">
                                <span class="input-group-addon">
                                <span class="glyphicon glyphicon-time"></span>
                                </span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="input-group clockpicker">
                                    <input type="text" class="form-control" value="<?php  echo $reply['endtime'];?>" name="endtime">
                                <span class="input-group-addon">
                                <span class="glyphicon glyphicon-time"></span>
                                </span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">门店介绍</label>
                            <div class="col-sm-9">
                                <textarea style="height:200px; width:535px;" class="form-control richtext" name="content" cols="70" id="reply-add-text"><?php  echo $reply['content'];?></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">电话</label>
                            <div class="col-sm-9">
                                <input type="text" name="tel" id="tel" value="<?php  echo $reply['tel'];?>" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">地址</label>
                            <div class="col-sm-9">
                                <input type="text" name="address" id="address" value="<?php  echo $reply['address'];?>" class="form-control" >
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">商家QQ</label>
                            <div class="col-sm-9">
                                <input type="text" name="qq" class="form-control" value="<?php  echo $reply['qq'];?>" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">商家微信</label>
                            <div class="col-sm-9">
                                <input type="text" name="weixin" class="form-control" value="<?php  echo $reply['weixin'];?>" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">坐标</label>
                            <div class="col-sm-9">
                                <?php  echo tpl_form_field_coordinate('baidumap', $reply)?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label" style="color:#f00;">状态</label>
                            <div class="col-sm-9">
                                <label class="radio-inline">
                                    <input type="radio" name="is_show" value="1" <?php  if($reply['is_show']==1 || empty($reply)) { ?>checked<?php  } ?>>启用
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="is_show" value="0" <?php  if(isset($reply['is_show']) && empty($reply['is_show'])) { ?>checked<?php  } ?>>关闭
                                </label>
                            </div>
                        </div>
                        <!--<div class="form-group">-->
                            <!--<label class="col-xs-12 col-sm-3 col-md-2 control-label">店主微信ID</label>-->
                            <!--<div class="col-sm-9">-->
                                <!--<input type="text" name="from_user" class="form-control" value="<?php  echo $reply['from_user'];?>" />-->
                            <!--</div>-->
                        <!--</div>-->
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">排序</label>
                            <div class="col-sm-9">
                                <input type="text" name="displayorder" value="<?php  echo $reply['displayorder'];?>" id="displayorder" class="form-control" />
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="tab_high">
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">支持预定</label>
                            <div class="col-sm-9">
                                <label class="radio-inline">
                                    <input type="radio" name="is_reservation" value="1" <?php  if($reply['is_reservation']==1 || empty($reply)) { ?>checked<?php  } ?>>启用
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="is_reservation" value="0" <?php  if(isset($reply['is_reservation']) && empty($reply['is_reservation'])) { ?>checked<?php  } ?>>关闭
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">支持店内</label>
                            <div class="col-sm-9">
                                <label class="radio-inline">
                                    <input type="radio" name="is_meal" value="1" <?php  if($reply['is_meal']==1 || empty($reply)) { ?>checked<?php  } ?>>启用
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="is_meal" value="0" <?php  if(isset($reply['is_meal']) && empty($reply['is_meal'])) { ?>checked<?php  } ?>>关闭
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">支持外卖</label>
                            <div class="col-sm-9">
                                <label class="radio-inline">
                                    <input type="radio" name="is_delivery" value="1" <?php  if($reply['is_delivery']==1 || empty($reply)) { ?>checked<?php  } ?>>启用
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="is_delivery" value="0" <?php  if(isset($reply['is_delivery']) && empty($reply['is_delivery'])) { ?>checked<?php  } ?>>关闭
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">支持快餐</label>
                            <div class="col-sm-9">
                                <label class="radio-inline">
                                    <input type="radio" name="is_snack" value="1" <?php  if($reply['is_snack']==1 || empty($reply)) { ?>checked<?php  } ?>>启用
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="is_snack" value="0" <?php  if(isset($reply['is_snack']) && empty($reply['is_snack'])) { ?>checked<?php  } ?>>关闭
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">支持排队</label>
                            <div class="col-sm-9">
                                <label class="radio-inline">
                                    <input type="radio" name="is_queue" value="1" <?php  if($reply['is_queue']==1 || empty($reply)) { ?>checked<?php  } ?>>启用
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="is_queue" value="0" <?php  if(isset($reply['is_queue']) && empty($reply['is_queue'])) { ?>checked<?php  } ?>>关闭
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">支持套餐</label>
                            <div class="col-sm-9">
                                <label class="radio-inline">
                                    <input type="radio" name="is_intelligent" value="1" <?php  if($reply['is_intelligent']==1 || empty($reply)) { ?>checked<?php  } ?>>启用
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="is_intelligent" value="0" <?php  if(isset($reply['is_intelligent']) && empty($reply['is_intelligent'])) { ?>checked<?php  } ?>>关闭
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否推荐</label>
                            <div class="col-sm-9">
                                <label class="radio-inline">
                                    <input type="radio" name="is_hot" value="1" <?php  if($reply['is_hot']==1 || empty($reply)) { ?>checked<?php  } ?>>是
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="is_hot" value="0" <?php  if(isset($reply['is_hot']) && empty($reply['is_hot'])) { ?>checked<?php  } ?>>否
                                </label>
                                <div class="help-block">
                                    在搜索页显示
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">首次下单短信验证</label>
                            <div class="col-sm-9">
                                <label class="radio-inline">
                                    <input type="radio" name="is_sms" value="1" <?php  if($reply['is_sms']==1) { ?>checked<?php  } ?>>启用
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="is_sms" value="0" <?php  if(empty($reply['is_sms'])) { ?>checked<?php  } ?>>关闭
                                </label>
                                <?php  if(!empty($reply)) { ?>
                                <div class="help-block" style="color:#f00;">注意:如果没有配置短信，请不要开启</div>
                                <?php  } ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">提供服务</label>
                            <div class="col-sm-9">
                                <label class="checkbox-inline">
                                    <input type="checkbox" id="enable_wifi" name="enable_wifi" value=1 <?php  if($reply['enable_wifi']==1) { ?>checked<?php  } ?>/>wifi
                                </label>
                                <label class="checkbox-inline">
                                    <input type="checkbox" id="enable_card" name="enable_card" value=1 <?php  if($reply['enable_card']==1) { ?>checked<?php  } ?>/>刷卡
                                </label>
                                <label class="checkbox-inline">
                                    <input type="checkbox" id="enable_room" name="enable_room" value=1 <?php  if($reply['enable_room']==1) { ?>checked<?php  } ?>/>包厢
                                </label>
                                <label class="checkbox-inline">
                                    <input type="checkbox" id="enable_park" name="enable_park" value=1 <?php  if($reply['enable_park']==1) { ?>checked<?php  } ?>/>停车
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="tab_nave_text">
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">预定文本</label>
                            <div class="col-sm-9">
                                <input type="text" name="btn_reservation" class="form-control" value="<?php  if(empty($reply['btn_reservation'])) { ?>预定<?php  } else { ?><?php  echo $reply['btn_reservation'];?><?php  } ?>" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">点菜文本</label>
                            <div class="col-sm-9">
                                <input type="text" name="btn_eat" class="form-control" value="<?php  if(empty($reply['btn_eat'])) { ?>点菜<?php  } else { ?><?php  echo $reply['btn_eat'];?><?php  } ?>" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">外卖文本</label>
                            <div class="col-sm-9">
                                <input type="text" name="btn_delivery" class="form-control" value="<?php  if(empty($reply['btn_delivery'])) { ?>外卖<?php  } else { ?><?php  echo $reply['btn_delivery'];?><?php  } ?>" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">快餐文本</label>
                            <div class="col-sm-9">
                                <input type="text" name="btn_snack" class="form-control" value="<?php  if(empty($reply['btn_snack'])) { ?>快餐<?php  } else { ?><?php  echo $reply['btn_snack'];?><?php  } ?>" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">排队文本</label>
                            <div class="col-sm-9">
                                <input type="text" name="btn_queue" class="form-control" value="<?php  if(empty($reply['btn_queue'])) { ?>排队<?php  } else { ?><?php  echo $reply['btn_queue'];?><?php  } ?>" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">套餐文本</label>
                            <div class="col-sm-9">
                                <input type="text" name="btn_intelligent" class="form-control" value="<?php  if(empty($reply['btn_intelligent'])) { ?>套餐<?php  } else { ?><?php  echo $reply['btn_intelligent'];?><?php  } ?>" />
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="tab_link">
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">优惠名称1</label>
                            <div class="col-sm-9">
                                <input type="text" name="coupon_title1" class="form-control" value="<?php  echo $reply['coupon_title1'];?>" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">优惠链接1</label>
                            <div class="col-sm-9">
                                <input type="text" name="coupon_link1" class="form-control" value="<?php  echo $reply['coupon_link1'];?>" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">优惠名称2</label>
                            <div class="col-sm-9">
                                <input type="text" name="coupon_title2" class="form-control" value="<?php  echo $reply['coupon_title2'];?>" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">优惠链接2</label>
                            <div class="col-sm-9">
                                <input type="text" name="coupon_link2" class="form-control" value="<?php  echo $reply['coupon_link2'];?>" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">优惠名称3</label>
                            <div class="col-sm-9">
                                <input type="text" name="coupon_title3" class="form-control" value="<?php  echo $reply['coupon_title3'];?>" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">优惠链接3</label>
                            <div class="col-sm-9">
                                <input type="text" name="coupon_link3" class="form-control" value="<?php  echo $reply['coupon_link3'];?>" />
                            </div>
                        </div>

                    </div>
                    <div class="tab-pane" id="tab_out">
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">消费满多少元免配送费</label>
                            <div class="col-sm-9">
                                <input type="text" name="freeprice" class="form-control" value="<?php  echo $reply['freeprice'];?>" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">外卖配送费用</label>
                            <div class="col-sm-9">
                                <input type="text" name="dispatchprice" class="form-control" value="<?php  echo $reply['dispatchprice'];?>" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">外卖起送价格</label>
                            <div class="col-sm-9">
                                <input type="text" name="sendingprice" class="form-control" value="<?php  echo $reply['sendingprice'];?>" />
                                <div class="help-block">低于该金额用户无法下单，商家拒绝配送</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">允许提前几天点外卖</label>
                            <div class="col-sm-9">
                                <input type="text" name="delivery_within_days" class="form-control" value="<?php  echo $reply['delivery_within_days'];?>" />
                                <div class="help-block">单位：天，如果只接受当天订单，请填写0</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">配送半径</label>
                            <div class="col-sm-9">
                                <input type="text" name="delivery_radius" class="form-control" value="<?php  echo $reply['delivery_radius'];?>" />
                                <div class="help-block">单位：公里</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-9">
                                <label for="not_in_delivery_radius" class="checkbox-inline">
                                    <input type="checkbox" name="not_in_delivery_radius" value="1" id="not_in_delivery_radius" <?php  if($reply['not_in_delivery_radius'] == 1) { ?>checked="true"<?php  } ?> /> 在配送半径之外是否允许下单
                                </label>
                                <div class="help-block">距离大于配送半径时是否允许下单，注意：手机定位精确性受天气、用户终端设备是否开启GPS以及硬件配置等影响很大，若此项设置为不允许下单，可能会导致部分用户无法成功下单</div>
                            </div>
                        </div>
                        <div id="time-list">
                            <?php  $flag = true;?>
                            <?php  if(!empty($timelist)) { ?>
                            <?php  if(is_array($timelist)) { foreach($timelist as $row) { ?>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label"><?php  if($flag==true) { ?>配送时间<?php  } ?></label>
                                <div class="col-sm-3">
                                    <div class="input-group clockpicker">
                                        <input type="text" class="form-control" value="<?php  echo $row['begintime'];?>" name="begintimes[<?php  echo $row['id'];?>]">
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-time"></span>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="input-group clockpicker">
                                        <input type="text" class="form-control" value="<?php  echo $row['endtime'];?>" name="endtimes[<?php  echo $row['id'];?>]">
                                <span class="input-group-addon">
                                <span class="glyphicon glyphicon-time"></span>
                                </span>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <?php  if($flag==true) { ?><a href="javascript:;" id="add-time"><i class="fa fa-plus-sign-alt"></i> 添加时间</a><?php  } else { ?><a class="btn btn-default btn-sm" href="<?php  echo $this->createWebUrl('Deletemealtime', array('id' => $row['id'], 'storeid' => $id))?>" onclick="return confirm('确认删除吗？');return false;"><i class="fa fa-times"></i></a><?php  } ?>
                                </div>
                            </div>
                            <?php  $flag = false;?>
                            <?php  } } ?>
                            <?php  } else { ?>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">配送时间</label>
                                <div class="col-sm-3">
                                    <div class="input-group clockpicker">
                                        <input type="text" class="form-control" value="08:30" name="newbegintime[]">
                                <span class="input-group-addon">
                                <span class="glyphicon glyphicon-time"></span>
                                </span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="input-group clockpicker">
                                        <input type="text" class="form-control" value="18:00" name="newendtime[]">
                                <span class="input-group-addon">
                                <span class="glyphicon glyphicon-time"></span>
                                </span>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <a href="javascript:;" id="add-time"><i class="fa fa-plus-sign-alt"></i> 添加时间</a>
                                </div>
                            </div>
                            <?php  } ?>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-9">
                                <div class="help-block">请尽量以半小时为单位,方便顾客选择</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group col-sm-12">
            <input type="submit" name="submit" value="保存设置" class="btn btn-primary col-lg-1" />
            <input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
        </div>
    </form>
</div>
<script type="text/javascript">
    $(function () {
        window.optionchanged = false;
        $('#myTab a').click(function (e) {
            e.preventDefault();//阻止a链接的跳转行为
            $(this).tab('show');//显示当前选中的链接及关联的content
        })
    });
</script>
<link rel="stylesheet" type="text/css" href="../addons/weisrc_dish/plugin/clockpicker/clockpicker.css" media="all">
<script type="text/javascript" src="../addons/weisrc_dish/plugin/clockpicker/clockpicker.js"></script>
<link rel="stylesheet" type="text/css" href="../addons/weisrc_dish/plugin/clockpicker/standalone.css" media="all">
<link rel="stylesheet" type="text/css" href="../addons/weisrc_dish/template/css/uploadify_t.css?v=2" media="all" />
<script>
    $(function(){
        $('#add-time').click(function(){
            $('#time-list').append($('#time-form-html').html());
            $('.clockpicker').clockpicker();
        });
        $('.clockpicker').clockpicker();
    })
</script>
<script language='javascript'>
    require(['jquery', 'util'], function ($, u) {
        $(function () {
            u.editor($('.richtext')[0]);
        });
    });
</script>
<script type="text/javascript">
    function check() {
        if($.trim($('#title').val()) == '') {
            message('没有输入门店名称.', '', 'error');
            return false;
        }
        return true;
    }
</script>

<?php  } else if($operation == 'setting') { ?>
<?php  if($_W['isfounder']) { ?>
<div class="main">
    <form action="" method="post" onsubmit="return check();" class="form-horizontal form" enctype="multipart/form-data">
        <div class="panel panel-default">
            <div class="panel-heading">
                门店配置
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">公众号名称</label>
                    <div class="col-sm-9 form-control-static">
                        <?php  echo $_W['account']['name'];?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">门店数量</label>
                    <div class="col-sm-9">
                        <input type="text" name="storecount" value="<?php  if(empty($config)) { ?>0<?php  } else { ?><?php  echo $config['storecount'];?><?php  } ?>" id="storecount" class="form-control" />
                        <div class="help-block" style="color:#f00;">为0不限制门店数量</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group col-sm-12">
            <input type="submit" name="submit" value="保存设置" class="btn btn-primary col-lg-1" />
            <input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
        </div>
    </form>
</div>
<?php  } ?>
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>
