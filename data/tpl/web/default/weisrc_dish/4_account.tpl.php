<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<ul class="nav nav-tabs" xmlns="http://www.w3.org/1999/html">
    <li <?php  if($operation == 'post') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('account', array('op' => 'post'))?>">添加账号</a></li>
    <li <?php  if($operation == 'display' || empty($operation)) { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('account', array('op' => 'display'))?>">账号管理</a></li>
</ul>
<?php  if($operation == 'post') { ?>
<div class="main">
    <script type="text/javascript">
        require(['jquery', 'util'], function($, u){
            $('#form1').submit(function(e){
                if($.trim($(':text[name="username"]').val()) == '') {
                    u.message('没有输入用户名.', '', 'error');
                    return false;
                }
                if($('#password').val() == '') {
                    u.message('没有输入密码.', '', 'error');
                    return false;
                }
                if($('#password').val().length < 8) {
                    u.message('密码长度不能小于8个字符.', '', 'error');
                    return false;
                }
                if($('#password').val() != $('#repassword').val()) {
                    u.message('两次输入的密码不一致.', '', 'error');
                    return false;
                }
                if($('#storeid option:selected').val() == 0) {
                    u.message('请选择所属门店.', '', 'error');
                    return false;
                }

            });
        });
    </script>
	<form action="" method="post" class="form-horizontal form" enctype="multipart/form-data" id="form1">
	<input type="hidden" name="parentid" value="<?php  echo $parent['id'];?>" />
        <div class="panel panel-default">
            <div class="panel-heading">
                添加新用户
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">用户名</label>
                    <div class="col-sm-10 col-lg-9">
                        <input id="" name="username" type="text" class="form-control" value="<?php  echo $users['username'];?>" />
                        <span class="help-block">请输入用户名，用户名为 3 到 15 个字符组成，包括汉字，大小写字母（不区分大小写）</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">密码</label>
                    <div class="col-sm-10 col-lg-9">
                        <input id="password" name="password" type="password" class="form-control" value="" autocomplete="off" />
                        <span class="help-block">请填写密码，最小长度为 8 个字符</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">确认密码</label>
                    <div class="col-sm-10 col-lg-9">
                        <input id="repassword" type="password" class="form-control" value="" autocomplete="off" />
                        <span class="help-block">重复输入密码，确认正确输入</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">所属门店</label>
                    <div class="col-sm-10 col-lg-9">
                        <select name="storeid" class="form-control" id="groupid">
                            <option value="0">请选择所属门店</option>
                            <?php  if(is_array($stores)) { foreach($stores as $row) { ?>
                            <option value="<?php  echo $row['id'];?>" <?php  if($account['storeid']==$row['id']) { ?>selected<?php  } ?>><?php  echo $row['title'];?></option>
                            <?php  } } ?>
                        </select>
                        <span class="help-block">门店操作权限</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">电子邮箱</label>
                    <div class="col-sm-10 col-lg-9">
                        <input type="text" name="email" class="form-control" value="<?php  echo $account['email'];?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">手机</label>
                    <div class="col-sm-10 col-lg-9">
                        <input type="text" name="mobile" class="form-control" value="<?php  echo $account['mobile'];?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">对应用户OPENID</label>
                    <div class="col-sm-10 col-lg-9">
                        <input type="text" name="from_user" class="form-control" value="<?php  echo $account['from_user'];?>" />
                        <span class="help-block">请填写微信编号。系统根据微信编号获取对应公众号的openid</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">状态</label>
                    <div class="col-sm-9">
                        <label class="radio-inline">
                            <input type="radio" name="status" value="2" <?php  if($users['status']==2 || empty($users)) { ?>checked<?php  } ?>>启用
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="status" value="1" <?php  if($users['status'] == 1) { ?>checked<?php  } ?>>否
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">备注</label>
                    <div class="col-sm-10 col-lg-9">
                        <textarea name="remark" style="height:80px;" class="form-control"><?php  echo $users['remark'];?></textarea>
                        <span class="help-block">方便注明此用户的身份</span>
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
<?php  } else if($operation == 'display') { ?>
<div class="main">
    <div class="panel panel-default">
        <div class="table-responsive panel-body">
        <form action="" method="post" class="form-horizontal form" >
            <input type="hidden" name="storeid" value="<?php  echo $storeid;?>" />
            <table class="table table-hover">
                <thead class="navbar-inner">
                <tr>
                    <th style="width:10%;">顺序</th>
                    <th style="width:30%;">(ID)用户名称</th>
                    <th style="width:40%;">所属门店</th>
                    <th style="width:10%;">状态</th>
                    <th style="width:10%;text-align:right;">操作</th>
                </tr>
                </thead>
                <tbody id="level-list">
                <?php  if(is_array($list)) { foreach($list as $row) { ?>
                <tr>
                    <td><input type="text" class="form-control" name="displayorder[<?php  echo $row['id'];?>]" value="<?php  echo $row['displayorder'];?>"></td>
                    <td>(<?php  echo $row['id'];?>)<?php  echo $row['username'];?></td>
                    <td><div class="type-parent"><?php  echo $stores[$row['storeid']]['title'];?></div></td>
                    <td><?php  if($row['status']==2) { ?><span class="label label-success">启用</span><?php  } else { ?><span class="label label-danger">禁止</span><?php  } ?></td>
                    <td style="text-align:right;"><a class="btn btn-default btn-sm" href="<?php  echo $this->createWebUrl('account', array('op' => 'post', 'id' => $row['id'], 'storeid' => $storeid))?>" title="编辑"><i class="fa fa-pencil"></i></a></td>
                </tr>
                <?php  } } ?>
                </tbody>

            </table>
        </form>
        <?php  echo $pager;?>
        </div>
    </div>
</div>
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>