<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<?php  echo $this -> set_tabbar($action, $storeid);?>
<?php  if($operation == 'display') { ?>
<div class="main">
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
        .header {
            line-height: 28px;
            margin-bottom: 16px;
            margin-top: 18px;
            padding-bottom: 4px;
            border-bottom: 1px solid #CCC
        }
        .block-gray{
            background-color: #555555;
            color: white;
        }
        .block-red{
            background-color: #ef4437;
            color: white;
        }
        .block-primary{
            background-color: #428bca;
            color: white;
        }
        .block-success{
            background-color: #5cb85c;
            color: white;
        }
        .block-orange{
            background-color: orange;
            color: white;
        }
        #guest-queue-index-body .queue_setting, #queue-setting-index-body .queue_setting {
            display: block;
            float: left;
            height: 100px;
            width: 31.3%;
            margin-right: 2%;
            margin-bottom: 20px;
            text-align: center
        }
        #queue-setting-index-body .queue_setting {
            width: 150px;
            height: 150px;
            border-radius: 1000px;
            margin-right: 20px
        }
        #guest-queue-index-body .queue_setting .name, #queue-setting-index-body .queue_setting .name{
            display: table-cell;
            font-size: 20px;
            font-weight: bold;
            line-height: 30px;
            vertical-align: middle;
            height: 60px
        }
        #queue-setting-index-body .queue_setting .name {
            height: 90px;
        }
        .table-display{
            display: table;
            width: 100%;
        }
    </style>
    <div class="panel panel-default">
        <!--/backend/shops/yezi/branches/20459/queue_settings/board-->
        <div class="panel-body">
            <div class="row">
                <ul class="nav nav-pills" role="tablist">
                    <li>
                        <a href="<?php  echo $this->createWebUrl('queueorder', array('op' => 'display', 'storeid' => $storeid))?>">客人队列</a>
                    </li>
                    <li class="active">
                        <a href="<?php  echo $this->createWebUrl('queuesetting', array('op' => 'display', 'storeid' => $storeid))?>">队列设置</a>
                    </li>
                    <li>
                        <a href="<?php  echo $this->createWebUrl('queuesetting', array('op' => 'setting', 'storeid' => $storeid))?>">排号模式</a>
                    </li>
                </ul>
            </div>
            <div class="header">
                <h3>队列设置 列表</h3>
            </div>
            <div class="form-group">
                <a class="btn btn-primary btn-sm" href="<?php  echo $this->createWebUrl('queuesetting', array('op' => 'post', 'storeid' => $storeid))?>">新建 队列设置</a>
            </div>
        <div id="queue-setting-index-body">
            <?php  $itemindex = 1?>
            <?php  if(is_array($list)) { foreach($list as $item) { ?>
            <?php  if($itemindex%5==1) { ?><?php  $curcolor = 'block-gray';?><?php  } ?>
            <?php  if($itemindex%5==2) { ?><?php  $curcolor = 'block-red';?><?php  } ?>
            <?php  if($itemindex%5==3) { ?><?php  $curcolor = 'block-primary';?><?php  } ?>
            <?php  if($itemindex%5==4) { ?><?php  $curcolor = 'block-success';?><?php  } ?>
            <?php  if($itemindex%5==0) { ?><?php  $curcolor = 'block-orange';?><?php  } ?>
            <a class="<?php  echo $curcolor;?> queue_setting" href="<?php  echo $this->createWebUrl('queuesetting', array('op' => 'post', 'storeid' => $storeid, 'id' => $item['id']))?>">
                <div class="table-display">
                    <div class="name"><?php  echo $item['title'];?></div>
                </div>
                <div class="table-display">
                    <div class="queue-enabled">
                        <?php  if($item['status']==1) { ?>开放中<?php  } else { ?>已关闭<?php  } ?>
                    </div>
                </div>
            </a>
            <?php  $itemindex++;?>
            <?php  } } ?>
            <div class="clearfix"></div>
        </div>
        </div>
    </div>
</div>
<?php  } else if($operation == 'post') { ?>
<link rel="stylesheet" type="text/css" href="../addons/weisrc_dish/plugin/clockpicker/clockpicker.css" media="all">
<script type="text/javascript" src="../addons/weisrc_dish/plugin/clockpicker/clockpicker.js"></script>
<link rel="stylesheet" type="text/css" href="../addons/weisrc_dish/plugin/clockpicker/standalone.css" media="all">
<script>
    $(function(){
        $('.clockpicker').clockpicker();
    })
</script>
<div class="main">
    <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
        <input type="hidden" name="storeid" value="<?php  echo $storeid;?>" />
        <input type="hidden" name="id" value="<?php  echo $item['id'];?>" />
        <div class="panel panel-default">
            <div class="panel-heading">
                队列设置 详情
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">队列名称</label>
                    <div class="col-sm-9">
                        <input type="text" name="title" class="form-control" value="<?php  echo $item['title'];?>"  placeholder="例如：1-2桌"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">客人数量少于等于多少人排入此队列</label>
                    <div class="col-sm-9">
                        <input type="number" name="limit_num" class="form-control" value="<?php  echo $item['limit_num'];?>" placeholder="例如：2"/>
                        <span class="help-block">
                            设置为自动排号时，当排号客户的用餐人数少于等于此人数时，系统将自动为排号客户分配此队列
                        </span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">营业时间</label>
                    <div class="col-sm-3">
                        <div class="input-group clockpicker">
                            <input type="text" class="form-control" value="<?php  if($item['starttime']) { ?><?php  echo $item['starttime'];?><?php  } else { ?>00:00<?php  } ?>" name="starttime">
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-time"></span>
                            </span>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="input-group clockpicker">
                            <input type="text" class="form-control" value="<?php  if($item['endtime']) { ?><?php  echo $item['endtime'];?><?php  } else { ?>23:59<?php  } ?>" name="endtime">
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-time"></span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">队列编号前缀</label>
                    <div class="col-sm-9">
                        <input type="text" name="prefix" class="form-control" value="<?php  echo $item['prefix'];?>"  placeholder="例如：C-"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">提前通知人数</label>
                    <div class="col-sm-9">
                        <input type="number" name="notify_number" class="form-control" value="<?php  echo $item['notify_number'];?>" placeholder=""/>
                        <span class="help-block">
                            队列有状态变更时, 提前通知的人数
                        </span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否启用</label>
                    <div class="col-sm-9">
                        <label for="isshow1" class="radio-inline"><input type="radio" name="status" value="1" id="isshow1" <?php  if(empty($item) || $item['status'] == 1) { ?>checked="true"<?php  } ?> /> 是</label>
                        &nbsp;&nbsp;&nbsp;
                        <label for="isshow2" class="radio-inline"><input type="radio" name="status" value="0" id="isshow2"  <?php  if(!empty($item) && $item['status'] == 0) { ?>checked="true"<?php  } ?> /> 否</label>
                        <span class="help-block"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">排序</label>
                    <div class="col-sm-9">
                        <input type="text" name="displayorder" class="form-control" value="<?php  echo $item['displayorder'];?>" />
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group col-sm-12">
            <input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1" />
            <input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
        </div>
    </form>
</div>
<?php  } else if($operation=='setting') { ?>
<link rel="stylesheet" type="text/css" href="../addons/weisrc_dish/plugin/clockpicker/clockpicker.css" media="all">
<script type="text/javascript" src="../addons/weisrc_dish/plugin/clockpicker/clockpicker.js"></script>
<link rel="stylesheet" type="text/css" href="../addons/weisrc_dish/plugin/clockpicker/standalone.css" media="all">
<div class="main">
    <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
        <input type="hidden" name="storeid" value="<?php  echo $storeid;?>" />
        <input type="hidden" name="id" value="<?php  echo $item['id'];?>" />
        <div class="panel panel-default">
            <div class="panel-heading">
                排号设置 详情
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">模式</label>
                    <div class="col-sm-9">
                        <select class="form-control" name="queuemode" autocomplete="off" class="form-control">
                            <option value="1" <?php  if($config['queuemode'] == 1) { ?> selected="selected"<?php  } ?>>系统自动分配</option>
                            <option value="2" <?php  if($config['queuemode'] == 2) { ?> selected="selected"<?php  } ?>>用户自主选择</option>
                        </select>
                        <span class="help-block">
                            系统自动分配: 根据用户输入的人数自动分配队列. 队列可在队列设置中设置<br/>
                            用户自主选择: 用户可以自由选择不同的队列.
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group col-sm-12">
            <input type="submit" name="submit" value="更新排号设置" class="btn btn-primary" />
            <input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
        </div>
    </form>
</div>
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>