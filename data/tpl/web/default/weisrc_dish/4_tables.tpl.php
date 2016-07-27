<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<?php  echo $this -> set_tabbar($action, $storeid);?>
<?php  if($operation == 'display') { ?>
<link rel="stylesheet" type="text/css" href="<?php echo RES;?>/css/main.css"/>
<div class="main">
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <ul class="nav nav-pills" role="tablist">
                    <li>
                        <a href="<?php  echo $this->createWebUrl('tablezones', array('op' => 'display', 'storeid' => $storeid))?>">餐桌类型</a>
                    </li>
                    <li class="active">
                        <a href="<?php  echo $this->createWebUrl('tables', array('op' => 'display', 'storeid' => $storeid))?>">餐桌管理</a>
                    </li>
                </ul>
            </div>
            <div class="header">
                <h3>桌台 列表</h3>
            </div>
            <div class="form-group">
                <a class="btn btn-success btn-sm" href="<?php  echo $this->createWebUrl('tables', array('op' => 'display', 'storeid' => $storeid, 'type' => 'state'))?>"><i class="fa fa-circle-o"></i> 桌台状态</a>
                <a class="btn btn-success btn-sm" href="<?php  echo $this->createWebUrl('tables', array('op' => 'display', 'storeid' => $storeid, 'type' => 'qrcode'))?>"><i class="fa fa-qrcode"></i> 二维码</a>
                <a class="btn btn-primary btn-sm" href="<?php  echo $this->createWebUrl('tables', array('op' => 'post', 'storeid' => $storeid))?>">新建 桌台</a>
                <a class="btn btn-primary btn-sm" href="<?php  echo $this->createWebUrl('tables', array('op' => 'batch', 'storeid' => $storeid))?>">批量新建</a>
                <div class="form-group inline-form" style="display: inline-block;">
                    <form accept-charset="UTF-8" action="./index.php" class="form-inline" id="diandanbao/table_search" method="get" role="form">
                        <div style="margin:0;padding:0;display:inline">
                        <input name="utf8" type="hidden" value="✓"></div>
                        <input type="hidden" name="c" value="site" />
                        <input type="hidden" name="a" value="entry" />
                        <input type="hidden" name="m" value="weisrc_dish" />
                        <input type="hidden" name="do" value="tables" />
                        <input type="hidden" name="storeid" value="<?php  echo $storeid;?>" />
                        <div class="form-group">
                            <label class="sr-only" for="q_name">名字(桌台号)</label>
                            <input class="form-control" id="keyword" name="keyword" placeholder="名字(桌台号)" type="search">
                        </div>
                        <div class="form-group">
                            <label class="sr-only" for="q_table_zone_id_eq">Table zone 等于</label>
                            <select id="tablezonesid" name="tablezonesid" class="form-control-excel">
                                <option value="">桌台类型</option>
                                <?php  if(is_array($tablezones)) { foreach($tablezones as $row) { ?>
                                <option value="<?php  echo $row['id'];?>" <?php  if($row['id'] == $item['tablezonesid'] || $row['id'] == $tablezonesid) { ?> selected="selected"<?php  } ?>><?php  echo $row['title'];?></option>
                                <?php  } } ?>
                            </select>
                        </div>
                        <input class="btn btn-sm btn-success" name="commit" type="submit" value="搜索">
                        <!--<a class="btn btn-success btn-sm" data-remote="true" href="">批量导出桌子二维码供打印(横版)</a>-->
                        <!--<a class="btn btn-primary btn-sm" data-remote="true" href="">批量导出桌子二维码供打印(竖版)</a>-->
                    </form>
                </div>
            </div>
            <div id="queue-setting-index-body">
            <?php  if($type == 'state') { ?>
            <div class="table-state-tables">
                <div class="col-xs-12">
                    <?php  if(is_array($list)) { foreach($list as $item) { ?>
                    <?php  if($item['status']==0) { ?>
                    <?php  $status = 'idle';?>
                    <?php  $title = '空闲';?>
                    <?php  } else if($item['status']==1) { ?>
                    <?php  $status = 'opened';?>
                    <?php  $title = '已开台';?>
                    <?php  } else if($item['status']==2) { ?>
                    <?php  $status = 'ordered';?>
                    <?php  $title = '已下单';?>
                    <?php  } else if($item['status']==3) { ?>
                    <?php  $status = 'paid';?>
                    <?php  $title = '已支付';?>
                    <?php  } ?>
                    <div class="state-table" data-id="<?php  echo $item['id'];?>">
                        <a class="<?php  echo $status;?> round" href="<?php  echo $this->createWebUrl('tables', array('op' => 'detail', 'storeid' => $storeid, 'tablesid' => $item['id']))?>" data-remote="" title="点击查看订单详情">
                            <div class="state"><?php  echo $title;?></div>
                        </a>
                        <div class="name overflow-ellipsis">
                            <span><a href="<?php  echo $this->createWebUrl('tables', array('op' => 'detail', 'storeid' => $storeid, 'tablesid' => $item['id']))?>"><?php  echo $item['title'];?></a></span>
                            <form accept-charset="UTF-8" action="<?php  echo $this->createWebUrl('tables', array('op' => 'updatestate', 'storeid' => $storeid, 'tablesid' => $item['id']))?>" data-remote="true" method="post" style="display:inline-block;">
                                <div style="margin:0;padding:0;display:inline"><input name="utf8" type="hidden" value="✓">
                                    <input name="_method" type="hidden" value="PUT">
                                </div>
                                <select id="workflow_state" name="workflow_state" onchange="$(this.form).submit();">
                                    <option value="0" <?php  if($item['status']==0) { ?>selected="selected"<?php  } ?>>空闲</option>
                                    <option value="1" <?php  if($item['status']==1) { ?>selected="selected"<?php  } ?>>已开台</option>
                                    <option value="2" <?php  if($item['status']==2) { ?>selected="selected"<?php  } ?>>已下单</option>
                                    <!--<option selected="selected" value="check_outing">结帐中</option>-->
                                    <option value="3" <?php  if($item['status']==3) { ?>selected="selected"<?php  } ?>>已支付</option>
                                </select>
                            </form>
                        </div>
                    </div>
                    <?php  } } ?>
                </div>
                <div class="col-xs-4">
                    <div class="table-order"></div>
                </div>
                <div class="clearfix"></div>
            </div>
            <?php  } else { ?>
            <div class="alert alert-success">
                将如下桌台二维码打印并分别贴在对应桌台上，即可实现扫码下单的功能。微信用户到店后只需拿起微信轻轻一扫，即可实现全自动点菜下单。
            </div>
            <div class="qr-code-table">
                <?php  if(is_array($list)) { foreach($list as $item) { ?>
                <?php  if($item['status']==0) { ?>
                <?php  $status = 'idle';?>
                <?php  $title = '空闲';?>
                <?php  } else if($item['status']==1) { ?>
                <?php  $status = 'opened';?>
                <?php  $title = '已开台';?>
                <?php  } else if($item['status']==2) { ?>
                <?php  $status = 'ordered';?>
                <?php  $title = '已下单';?>
                <?php  } else if($item['status']==3) { ?>
                <?php  $status = 'paid';?>
                <?php  $title = '已支付';?>
                <?php  } ?>
                    <div class="qr-code-item">
                        <div class="qr-code-op">
                            <a data-rel="tooltip" href="<?php  echo $this->createWebUrl('tables', array('id' => $item['id'], 'storeid' => $storeid, 'op' => 'post'))?>" title="编辑"><icon class="fa fa-edit"></icon></a>
                            <a data-confirm="确定删除?" data-method="delete" data-rel="tooltip" href="<?php  echo $this->createWebUrl('tables', array('id' => $item['id'], 'storeid' => $storeid, 'op' => 'delete'))?>" onclick="return confirm('确认操作吗？');return false;" rel="nofollow" title="删除"><icon class="fa fa-trash-o"></icon></a>
                        </div>
                        <a href="<?php  echo $this->createWebUrl('tables', array('op' => 'detail', 'storeid' => $storeid, 'tablesid' => $item['id']))?>">
                            <div class="qr-code-box">
                                <div class="qr-code-item-image">
                                    <img alt="<?php  echo $item['title'];?>" src="<?php echo $this->fm_qrcode($_W['siteroot'] . 'app/index.php?i=' . $_W['uniacid'] . '&c=entry&mode=1&storeid=' . $storeid . '&tablesid=' . $item['id'] . '&do=waplist&m=weisrc_dish', 'qrcode_' . $item['id'], '', $logo);?>" width="100%">
                                </div>
                                <div class="qr-code-item-info">
                                    <?php  echo $item['title'];?>
                                </div>
                            </div>
                            <div class="qr-code-item-footer">
                                扫描次数: <?php  if(empty($tablesorder[$item['id']]['count'])) { ?>0<?php  } else { ?><?php  echo $tablesorder[$item['id']]['count'];?><?php  } ?>
                                <br>
                                当前状态
                                :
                                <span class="label label-info"><?php  echo $title;?></span>
                                <br>
                                桌台类型: <?php  echo $tablezones[$item['tablezonesid']]['title'];?>
                            </div>
                        </a>
                    </div>
                <?php  } } ?>
                <div class="space"></div>
                </div>
            <?php  } ?>
            <div class="clearfix"></div>
        </div>
        </div>
    </div>
</div>
<?php  } else if($operation == 'batch') { ?>
<div class="main">
    <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
        <input type="hidden" name="storeid" value="<?php  echo $storeid;?>" />
        <input type="hidden" name="id" value="<?php  echo $item['id'];?>" />
        <div class="panel panel-default">
            <div class="panel-heading">
                批量创建桌台
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">起始桌台号</label>
                    <div class="col-sm-9">
                        <input type="text" name="title" class="form-control" value="<?php  echo $item['title'];?>"  placeholder=""/>
                        <span class="help-block">例如：C001</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">可供就餐人数</label>
                    <div class="col-sm-9">
                        <input type="number" name="user_count" class="form-control" value="<?php  echo $item['user_count'];?>" placeholder=""/>
                        <span class="help-block">
                            设置为自动排号时，当排号客户的用餐人数少于等于此人数时，系统将自动为排号客户分配此队列
                        </span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">桌台类型</label>
                    <div class="col-sm-9">
                        <select class="form-control" style="margin-right:15px;" name="tablezonesid" autocomplete="off" class="form-control">
                            <?php  if(is_array($tablezones)) { foreach($tablezones as $row) { ?>
                            <option value="<?php  echo $row['id'];?>" <?php  if($row['id'] == $item['tablezonesid'] || $row['id'] == $tablezonesid) { ?> selected="selected"<?php  } ?>><?php  echo $row['title'];?></option>
                            <?php  } } ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">创建桌台数量</label>
                    <div class="col-sm-9">
                        <input type="number" name="table_count" class="form-control" value="<?php  echo $item['table_count'];?>" placeholder=""/>
                        <span class="help-block">
                            根据创建的桌台数量，系统会自动依据起始桌台号依次递增,<br/> 例如C001, C002, C003, C004.....,一次最多创建10张桌台
                        </span>
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
            <input type="submit" name="submit" value="创建" class="btn btn-primary col-lg-1" />
            <input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
        </div>
    </form>
</div>
<?php  } else if($operation == 'post') { ?>
<div class="main">
    <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
        <input type="hidden" name="storeid" value="<?php  echo $storeid;?>" />
        <input type="hidden" name="id" value="<?php  echo $item['id'];?>" />
        <div class="panel panel-default">
            <div class="panel-heading">
                桌台 详情
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">名字(桌台号)</label>
                    <div class="col-sm-9">
                        <input type="text" name="title" class="form-control" value="<?php  echo $item['title'];?>"  placeholder=""/>
                        <span class="help-block">例如：C001</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">可供就餐人数</label>
                    <div class="col-sm-9">
                        <input type="number" name="user_count" class="form-control" value="<?php  echo $item['user_count'];?>" placeholder=""/>
                        <span class="help-block">
                            设置为自动排号时，当排号客户的用餐人数少于等于此人数时，系统将自动为排号客户分配此队列
                        </span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">桌台类型</label>
                    <div class="col-sm-9">
                        <select class="form-control" style="margin-right:15px;" name="tablezonesid" autocomplete="off" class="form-control">
                            <?php  if(is_array($tablezones)) { foreach($tablezones as $row) { ?>
                            <option value="<?php  echo $row['id'];?>" <?php  if($row['id'] == $item['tablezonesid'] || $row['id'] == $tablezonesid) { ?> selected="selected"<?php  } ?>><?php  echo $row['title'];?></option>
                            <?php  } } ?>
                        </select>
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
<?php  } else if($operation == 'detail') { ?>
<link rel="stylesheet" type="text/css" href="<?php echo RES;?>/css/main.css"/>
<div class="main">
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="header">
                <h3>桌台 详情</h3>
            </div>
            <div class="model-show">
                <p>
                    <b>
                        名字(桌台号)
                        :
                    </b>
                    <?php  echo $item['title'];?>
                </p>
                <p>
                    <b>
                        桌台类型
                        :
                    </b>
                    <?php  echo $cate['title'];?>
                </p>
                <p>
                    <b>
                        可供就餐人数
                        :
                    </b>
                    <?php  echo $item['user_count'];?>
                </p>
                <p>
                    <b>
                        当前状态
                        :
                    </b>
                    <?php  if($item['status']==0) { ?>空闲<?php  } else if($item['status']==1) { ?>已开台<?php  } else if($item['status']==2) { ?>已下单<?php  } else if($item['status']==3) { ?>已支付<?php  } ?>
                </p>
                <p>
                    <b>
                        扫描人数
                        :
                    </b>
                    <?php  if(empty($tablesorderuser)) { ?>0<?php  } else { ?><?php  echo $tablesorderuser;?><?php  } ?>
                </p>
                <p>
                    <b>
                        所属门店
                        :
                    </b>
                    <?php  echo $store['title'];?>
                </p>
                <p>
                    <b>
                        扫描次数
                        :
                    </b>
                    <?php  if(empty($tablesorder)) { ?>0<?php  } else { ?><?php  echo $tablesorder;?><?php  } ?>
                </p>
                <p>
                    <b>
                        排序
                        :
                    </b>
                    <?php  echo $item['displayorder'];?>
                </p>
                <p>
                    <b>
                        二维码图片
                        :
                    </b>
                    <img alt="" src="<?php echo$this->fm_qrcode($_W['siteroot'] . 'app/index.php?i=' . $_W['uniacid'] . '&c=entry&mode=1&storeid=' . $storeid . '&tablesid=' . $item['id'] . '&do=waplist&m=weisrc_dish', 'qrcode_' . $item['id'], '', $logo);?>">
                </p>
                <div class="space"></div>
            </div>
            <div class="qrcode-scan-relations">
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>扫描者</th>
                        <th>扫描时间</th>
                        <th>扫描者</th>
                        <th>扫描时间</th>
                        <th>扫描者</th>
                        <th>扫描时间</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php  $isbegin=false;?>
                    <?php  $rowindex=1;?>
                    <?php  if(is_array($orderlist)) { foreach($orderlist as $item) { ?>
                    <?php  $r = $rowindex%3?>
                    <?php  if($isbegin==false) { ?>
                    <?php  if($r==1) { ?>
                    <tr>
                        <?php  $isbegin=true;?>
                    <?php  } ?>
                    <?php  } ?>
                        <td>
                            <img alt="0" src="<?php  echo $item['headimgurl'];?>" width="50">
                            <a href="#"><?php  echo $item['nickname'];?> - <?php  echo $item['from_user'];?></a>
                        </td>
                        <td><?php  echo date('Y-m-d <br> H:i:s', $item['dateline'])?></td>
                        <?php  if($isbegin==true) { ?>
                        <?php  if($r==0) { ?>
                        </tr>
                        <?php  $isbegin=false;?>
                        <?php  } ?>
                        <?php  } ?>
                    <?php  $rowindex++;?>
                    <?php  } } ?>

                    <?php  if($isbegin==true) { ?>
                    </tr>
                    <?php  $isbegin=false;?>
                    <?php  } ?>
                    </tbody>
                </table>
                <br>
                <div class="pull-left"></div>
                <br>
                <div class="clearfix"></div>
            </div>
            <a class="btn btn-primary btn-sm" href="<?php  echo $this->createWebUrl('tables', array('op' => 'post', 'storeid' => $storeid, 'id' => $tablesid))?>">编辑</a>
            <a class="btn btn-default btn-sm" href="<?php  echo $this->createWebUrl('tables', array('op' => 'display', 'storeid' => $storeid))?>">返回</a>
        </div>
    </div>
</div>
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>