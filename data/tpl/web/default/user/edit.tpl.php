<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header-gw', TEMPLATE_INCLUDEPATH)) : (include template('common/header-gw', TEMPLATE_INCLUDEPATH));?>
<ol class="breadcrumb">
	<li><a href="./?refresh"><i class="fa fa-home"></i></a></li>
	<li><a href="<?php  echo url('user/display');?>">系统</a></li>
	<li><a href="<?php  echo url('user/display');?>">用户列表</a></li>
	<li class="active">编辑用户</li>
</ol>
<ul class="nav nav-tabs">
	<li><a href="<?php  echo url('user/display');?>">用户列表</a></li>
	<li><a href="<?php  echo url('user/create');?>">添加用户</a></li>
	<li class="active"><a href="<?php  echo url('user/edit', array('uid' => $uid));?>">编辑用户</a></li>
	<li><a href="<?php  echo url('user/permission', array('uid' => $uid));?>">查看用户权限</a></li>
</ul>
<div class="clearfix">
	<form action="" method="post" class="form-horizontal" role="form" id="form1">
		<h5 class="page-header">编辑用户基本资料</h5>
		<div class="form-group">
			<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">用户名</label>
			<div class="col-sm-10 col-xs-12">
				<span class="uneditable-input form-control"><?php  echo $user['username'];?></span>
				<span class="help-block">当前编辑的用户名</span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">新密码</label>
			<div class="col-sm-10 col-xs-12">
				<input id="password" name="password" type="password" class="form-control" autocomplete="off" value="" />
				<span class="help-block">请填写密码，最小长度为 8 个字符。如果不更改密码此处请留空</span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">确认新密码</label>
			<div class="col-sm-10 col-xs-12">
				<input id="repassword" type="password" class="form-control" value="" autocomplete="off" />
				<span class="help-block">重复输入密码，确认正确输入。如果不更改密码此处请留空</span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">服务时间</label>
			<div class="col-sm-10 col-xs-12">
				<p class="form-control-static">
					<strong class="text-danger">
						开始时间：<?php  echo date('Y-m-d', $user['starttime'])?>
						~~
						到期时间：<?php  if(!$user['endtime']) { ?>永久有效<?php  } else { ?><?php  echo date('Y-m-d', $user['endtime'])?><?php  } ?>
					</strong>
				</p>
				<span class="help-block">重复输入密码，确认正确输入。如果不更改密码此处请留空</span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">设置到期时间</label>
			<div class="col-sm-10 col-xs-12">
				<label class="radio-inline"><input class="" name="endtype" value="2" onclick="$(this).parent().parent().parent().next().show();" <?php  if(!empty($user['endtime'])) { ?>checked<?php  } ?> type="radio">设置期限</label>
				<label class="radio-inline"><input class="" name="endtype" value="1" onclick="$(this).parent().parent().parent().next().hide();" <?php  if(empty($user['endtime'])) { ?>checked<?php  } ?> type="radio">永久</label>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label"></label>
			<div class="col-sm-10 col-xs-12">
				<?php  echo tpl_form_field_date('endtime', $user['endtime']);?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">所属用户组</label>
			<div class="col-sm-10 col-xs-12">
				<select name="groupid" class="form-control" id="groupid">
					<option value="0">请选择所属用户组</option>
					<?php  if(is_array($groups)) { foreach($groups as $row) { ?>
					<option value="<?php  echo $row['id'];?>" <?php  if($user['groupid'] == $row['id']) { ?>selected<?php  } ?>><?php  echo $row['name'];?></option>
					<?php  } } ?>
				</select>
				<span class="help-block">分配用户所属用户组后，该用户会自动拥有此用户组内的模块操作权限</span>
				<span class="help-block"><strong class="text-danger">更改用户的所属会员组后，该用户的服务到期时间为：当前时间 + 更改用户组的服务有效期。</strong></span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">备注</label>
			<div class="col-sm-10 col-xs-12">
				<textarea id="" name="remark" style="height:80px;" class="form-control"><?php  echo $user['remark'];?></textarea>
				<span class="help-block">方便注明此用户的身份</span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">上次登录时间</label>
			<div class="col-sm-10 col-xs-12">
				<span class="uneditable-input form-control"><?php  echo date('Y-m-d H:i:s', $user['lastvisit']);?></span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">上次登录IP</label>
			<div class="col-sm-10 col-xs-12">
				<span class="uneditable-input form-control"><?php  echo $user['lastip'];?></span>
			</div>
		</div>
<?php  if(!empty($extendfields)) { ?>
		<h5 class="page-header">编辑用户基本资料</h5>
	<?php  if($extendfields) { ?>
		<?php  if(is_array($extendfields)) { foreach($extendfields as $item) { ?>
			<?php  if($item['field']=='birthyear') { ?>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label"><?php  echo $item['title'];?>：<?php  if($item['required']) { ?><span style="color:red">*</span><?php  } ?></label>
					<div class="col-sm-10 col-xs-12">
						<?php  echo tpl_fans_form($item['field'],$user['profile']['birth']);?>
					</div>
				</div>
			<?php  } else if($item['field']=='resideprovince') { ?>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label"><?php  echo $item['title'];?>：<?php  if($item['required']) { ?><span style="color:red">*</span><?php  } ?></label>
					<div class="col-sm-10 col-xs-12">
						<?php  echo tpl_fans_form($item['field'],$user['profile']['reside']);?>
					</div>
				</div>
			<?php  } else { ?>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label"><?php  echo $item['title'];?>：<?php  if($item['required']) { ?><span style="color:red">*</span><?php  } ?></label>
					<div class="col-sm-10 col-xs-12">
						<?php  echo tpl_fans_form($item['field'], $user['profile'][$item['field']]);?>
					</div>
				</div>
			<?php  } ?>
		<?php  } } ?>
	<?php  } ?>
<?php  } ?>
		<div class="form-group">
			<div class="col-sm-offset-2 col-md-offset-2 col-lg-offset-2 col-xs-12 col-sm-10 col-md-10 col-lg-10">
				<input type="submit" class="btn btn-primary" name="profile_submit" value="保存用户资料" /> &nbsp; &nbsp; 
				<a class="btn btn-default" href="<?php  echo url('user/permission', array('uid' => $uid));?>">查看当前用户操作权限</a>
				<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
			</div>
		</div>
	</form>
</div>
<script type="text/javascript">
	$(function () {
		if ($('[name="endtype"]:checked').val() == 1) {
			$('[name="endtime"]').parent().parent().hide();
		}
	});
	$('#form1').submit(function(){
		if($('#password').val().trim() != '') {
			if($('#password').val().length < 8) {
				util.message('密码长度不能小于8个字符.', '', 'error');
				return false;
			}
			if($('#password').val() != $('#repassword').val()) {
				util.message('两次输入的密码不一致.', '', 'error');
				return false;
			}
		}
		if($('#groupid option:selected').val() == 0) {
			util.message('请选择所属用户组.', '', 'error');
			return false;
		}
		if ($('[name="endtype"]:checked').val() == '1') {
			$('[name="endtime"]').val(0);
		}
	});
</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer-gw', TEMPLATE_INCLUDEPATH)) : (include template('common/footer-gw', TEMPLATE_INCLUDEPATH));?>
