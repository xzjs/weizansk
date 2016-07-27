<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header-gw', TEMPLATE_INCLUDEPATH)) : (include template('common/header-gw', TEMPLATE_INCLUDEPATH));?>
<ol class="breadcrumb">
	<li><a href="./?refresh"><i class="fa fa-home"></i></a></li>
	<li><a href="<?php  echo url('system/welcome');?>">系统</a></li>
	<li class="active">查看用户权限</li>
</ol>
<ul class="nav nav-tabs">
	<li><a href="<?php  echo url('user/display');?>">用户列表</a></li>
	<li><a href="<?php  echo url('user/create');?>">添加用户</a></li>
	<li><a href="<?php  echo url('user/edit', array('uid' => $uid));?>">编辑用户</a></li>
	<li class="active"><a href="<?php  echo url('user/permission', array('uid' => $uid));?>">查看用户权限</a></li>
</ul>

<div class="clearfix">
	<div class="form form-horizontal" >
		<div class="panel panel-default">
			<div class="panel-heading">用户组基本权限</div>
			<div class="panel-body table-responsive">
				<table class="table table-hover">
					<tr>
						<td style="width:250px">用户组名</td>
						<td>
							<p class="form-control-static"><?php  echo $group['name'];?>&nbsp;&nbsp;<a href="<?php  echo url('user/group/post', array('id' => $group['id']));?>" title="编辑当前用户所在用户组"><i class="fa fa-edit"></i></a></p>
						</td>
					</tr>
					<tr>
						<td>最多公众号数量</td>
						<td><?php  echo $group['maxaccount'];?></td>
					</tr>
				</table>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">可使用公众号</div>
			<div class="panel-body table-responsive">
				<table class="table table-hover">
					<thead>
					<tr>
						<th style="width:250px;">公众号</th>
						<th>角色</th>
						<th style="width:450px;">操作</th>
					</tr>
					</thead>
					<tbody>
					<?php  if(is_array($wechats)) { foreach($wechats as $item) { ?>
					<tr>
						<td><?php  echo $item['name'];?></td>
						<td><?php  if($weids[$item['uniacid']]['role'] == 'operator') { ?><span class="label label-success">操作员<?php  } else { ?><span class="label label-info">管理员<?php  } ?></span></td>
						<td><a href="<?php  echo url('account/post', array('uniacid' => $item['uniacid']));?>" target="_blank">编辑公众号</a>&nbsp;|&nbsp;<a href="<?php  echo url('account/permission', array('uniacid' => $item['uniacid'], 'fromuid' => $uid));?>" target="_blank">编辑权限</a></td>
					</tr>
					<?php  } } ?>
					</tbody>
				</table>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">当前用户所在用户组可使用的公众号权限</div>
			<div class="panel-body table-responsive">
				<table class="table table-hover">
					<thead>
						<tr>
							<th style="width:250px;">公众服务套餐</th>
							<th>模块权限</th>
							<th style="width:450px;">模板权限</th>
						</tr>
					</thead>
					<tbody>
					<?php  if(is_array($group['package'])) { foreach($group['package'] as $item) { ?>
					<tr>
						<td style="line-height:30px;width:250px;"><?php  echo $item['name'];?></td>
						<td style="line-height:30px; white-space:normal;">
							<span class="label label-success">系统模块</span>
							<?php  if(is_array($item['modules'])) { foreach($item['modules'] as $module) { ?>
							<span class="label label-info"><?php  echo $module['title'];?></span>
							<?php  } } ?>
						</td>
						<td style="line-height:30px; white-space:normal;">
							<span class="label label-success">微站默认模板</span>
							<?php  if(is_array($item['templates'])) { foreach($item['templates'] as $template) { ?>
							<span class="label label-info"><?php  echo $template['title'];?></span>
							<?php  } } ?>
						</td>
					</tr>
					<?php  } } ?>
					</tbody>
				</table>
			</div>
		</div>
		<div class="form-group">
			<div class="col-xs-12 col-sm-10 col-md-10 col-lg-10">
				<a class="btn btn-primary" href="<?php  echo url('user/edit', array('uid' => $uid));?>">编辑当前用户资料</a>
			</div>
		</div>
	</div>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer-gw', TEMPLATE_INCLUDEPATH)) : (include template('common/footer-gw', TEMPLATE_INCLUDEPATH));?>
