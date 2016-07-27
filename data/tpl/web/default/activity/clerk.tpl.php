<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<style>
	.table>thead>tr>th{border-bottom:0;}
	.table>thead>tr>th .checkbox label{font-weight:bold;}
	.table>tbody>tr>td{border-top:0;}
	.table .checkbox{padding-top:4px;}
</style>
<ul class="nav nav-tabs">
	<li<?php  if($do == 'list') { ?> class="active"<?php  } ?>><a href="<?php  echo url('activity/clerk/list');?>">店员管理</a></li>
	<li<?php  if($do == 'post') { ?> class="active"<?php  } ?>><a href="<?php  echo url('activity/clerk/post');?>"><?php  if($id > 0) { ?>编辑店员<?php  } else { ?>添加店员<?php  } ?></a></li>
</ul>
<?php  if($do == 'list') { ?>
<div class="alert alert-info">
	<p>1、系统中所有的店员信息不可重复</p>
	<p>2、添加店员可以设置店员操作的权限</p>
	<p>3、店员可以登录系统后台（工作台）来进行相应的操作</p>
</div>

<div class="main">
<div class="main table-responsive">
	<form method="post" class="form-horizontal" id="form1">
		<div class="panel panel-default">
			<div class="panel-body table-responsive">
				<table class="table table-hover">
					<thead>
					<tr>
						<th>店员姓名</th>
						<th>所在门店</th>
						<th>登陆账号</th>
						<th>手机号</th>
						<th>微信昵称</th>
						<th>操作</th>
					</tr>
					</thead>
					<tbody id="list">
					<?php  if(is_array($list)) { foreach($list as $item) { ?>
					<tr>
						<td>
							<?php  echo $item['name'];?>
							<?php  if(empty($item['password'])) { ?>
							<span class="text-danger" style="cursor:pointer" data-toggle="popover" data-placement="top" data-trigger="hover" data-content="该店员尚未设置密码,请重新编辑店员信息密码"><i class="fa fa-info-circle"></i></span>
							<?php  } ?>
						</td>
						<td>
							<?php  if($item['storeid'] > 0) { ?>
								<?php  if(!empty($stores[$item['storeid']])) { ?>
									<span class="label label-success"><?php  echo $stores[$item['storeid']]['business_name'];?>-<?php  echo $stores[$item['storeid']]['branch_name'];?></span>
								<?php  } else { ?>
									<span class="label label-warning">门店已删除</span>
								<?php  } ?>
							<?php  } else { ?>
								<span class="label label-danger">未设置</span>
							<?php  } ?>
						</td>
						<td><?php  echo $users[$item['uid']]['username'];?></td>
						<td><?php  echo $item['mobile'];?></td>
						<td><?php  echo $item['nickname'];?></td>
						<td>
							<a onclick="if (confirm('使用该店员帐号后，会更改您当前登录的用户，是否继续？')) {alert('使用完毕后，您退出店员帐号重新登录管理帐号即可。')} else { return false;}" href="<?php  echo url('activity/clerk/switch',array('id' => $item['id']));?>" title="编辑">使用该店员帐号</a>&nbsp;-&nbsp;
							<a href="<?php  echo url('activity/clerk/post',array('id' => $item['id']));?>" title="编辑">编辑</a>&nbsp;-&nbsp;
							<a href="<?php  echo url('activity/clerk/del', array('id' => $item['id']))?>" onclick="return confirm('此操作不可恢复，确认删除？');return false;" title="删除">删除</a>
						</td>
					</tr>
					<?php  } } ?>
					</tbody>
				</table>
			</div>
		</div>
		<?php  echo $pager;?>
	</form>
</div>
</div>
<script>
	require(['bootstrap'],function($){
		$('[data-toggle="popover"]').popover()
	});
</script>
<?php  } ?>
<?php  if($do == 'post') { ?>
<?php  if(empty($stores)) { ?>
<div class="alert alert-info">
	<p style="color : black;">您还没有<a href="<?php  echo url('wechat/location/post')?>" >添加门店</a>，请先添加门店再进行操作。</p>
</div>
<?php  } else { ?>
<div class="alert alert-info">
	1、 添加微信店员需要您的公众号号为: 认证订阅号 或 认证服务号<br>
	2、因为添加店员是通过粉丝昵称搜索相应店员的信息,所以添加店员之前,需要 <a href="<?php  echo url('mc/fans');?>" target="_blank">下载粉丝列表</a> & <a href="<?php  echo url('mc/fans');?>" target="_blank">更新粉丝信息</a> & <a href="<?php  echo url('mc/fangroup');?>" target="_blank">更新粉丝分组</a><br>
	3、如果您不想使用昵称来搜索粉丝，可通过粉丝id进行搜索
</div>
<div class="clearfix">
	<form class="form-horizontal form" id="form1" action="" method="post" enctype="multipart/form-data">
		<input type="hidden" name="id" value="<?php  echo $id;?>">
		<div class="panel panel-default">
			<div class="panel-heading">基本信息</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">*</span>店员姓名</label>
					<div class="col-sm-9 col-xs-12">
						<div class="input-group">
							<input type="text" name="name"  value="<?php  echo $clerk['name'];?>" class="form-control" data-type=<?php  if(!empty($clerk['id'])) { ?>"1"<?php  } else { ?> ""<?php  } ?> placeholder="请填写店员姓名">
								<span class="input-group-addon" >
									<input type="checkbox" name="same" aria-label="">
									使用店员姓名作为登录账号
								</span>
						</div>
						<div class="help-block" id="warning" style="display: none"><p class="text-danger">登录账号已存在，请重新输入登录账号</p></div>
					</div>
				</div>
				<div class="form-group" id="username">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">*</span>登录账号</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" name="username" value="<?php  echo $clerk['username'];?>" data-type=<?php  if(!empty($clerk['id'])) { ?>"1"<?php  } else { ?> ""<?php  } ?> class="form-control">
						<div class="help-block">请输入登陆账号，登陆账号为 3 到 15 个字符组成，包括汉字，大小写字母（不区分大小写）</div>
						<div class="help-block" id="u_warning" style="display: none"><p class="text-danger">登录账号已存在，请重新输入登录账号</p></div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">*</span>密码</label>
					<div class="col-sm-9 col-xs-12">
						<input type="password" name="password" value="" class="form-control">
						<div class="help-block">请填写密码，最小长度为 8 个字符.<?php  if($clerk['uid'] > 0) { ?>如果不更改密码此处请留空<?php  } ?></div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">*</span>确认密码</label>
					<div class="col-sm-9 col-xs-12">
						<input type="password" name="repassword" value="" class="form-control">
						<div class="help-block">重复输入密码，确认正确输入.<?php  if($clerk['uid'] > 0) { ?>如果不更改密码此处请留空<?php  } ?></div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">*</span>手机号</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" name="mobile" value="<?php  echo $clerk['mobile'];?>" class="form-control" placeholder="请填写店员手机号">
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require">*</span>所属门店</label>
					<div class="col-sm-9 col-xs-12">
						<select name="storeid" class="form-control">
							<option value="">==选择所属门店==</option>
							<?php  if(is_array($stores)) { foreach($stores as $store) { ?>
							<option value="<?php  echo $store['id'];?>" <?php  if($store['id'] == $clerk['storeid']) { ?>selected<?php  } ?>><?php  echo $store['business_name'];?>-<?php  echo $store['branch_name'];?></option>
							<?php  } } ?>
						</select>
						<div class="help-block"><strong class="text-danger">如果您不选门店，员工账号登录进来将可以看见所有的支付订单和卡券，会员卡. <a href="<?php  echo url('activity/store');?>">创建门店</a></strong></div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require"> </span>店员微信昵称</label>
					<div class="col-sm-9 col-xs-12">
						<div class="input-group">
							<input type="text" name="nickname" value="<?php  echo $clerk['nickname'];?>" class="form-control">
							<div class="input-group-btn">
								<span class="btn btn-success btn-openid">检 测</span>
							</div>
						</div>
						<div class="help-block">请填写微信昵称。系统根据微信昵称获取该商家对应公众号的openid</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="require"> </span> 或 店员粉丝编号</label>
					<div class="col-sm-9 col-xs-12">
						<div class="input-group">
							<input type="text" name="openid" value="<?php  echo $clerk['openid'];?>" class="form-control">
							<div class="input-group-btn">
								<span class="btn btn-success btn-openid">检 测</span>
							</div>
						</div>
						<div class="help-block">请填写微信编号。系统根据微信编号获取该商家对应公众号的openid</div>
					</div>
				</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">权限设置 （<a href="<?php  echo url('profile/deskmenu');?>" target="_blank">自定义工作台菜单？</a>）</div>
			<div class="panel-body table-responsive">
				<table class="table">
					<?php  if(is_array($permission)) { foreach($permission as $name => $row) { ?>
						<thead>
						<tr class="info">
							<th colspan="6">
								<div class="checkbox">
									<label class="permission permission-<?php  echo $name;?>" data-name="<?php  echo $name;?>"><input type="checkbox"><?php  echo $row['title'];?></label>
								</div>
							</th>
						</tr>
						</thead>
						<?php  $i=1;?>
						<?php  if(is_array($row['items'])) { foreach($row['items'] as $item) { ?>
							<?php  if($i%6 == 1 || $i == 1) { ?><tr><?php  } ?>
							<td>
								<div class="checkbox">
									<label class="permission-child permission-child-<?php  echo $name;?>" data-name="<?php  echo $name;?>"><input type="checkbox" value="<?php echo empty($item['permission'])? 'clerk_'.$item['id'] : $item['permission'];?>" <?php  if(in_array($item['permission'], $clerk['permission'])) { ?>checked<?php  } ?> name="permission[]"><?php  echo $item['title'];?></label>
								</div>
							</td>
							<?php  if($i%6 == 0) { ?></tr><?php  } ?>
							<?php  $i++;?>
						<?php  } } ?>
					<?php  } } ?>
				</table>
			</div>
		</div>
		<div class="form-group col-sm-12">
			<input name="uid"  type="hidden" value="<?php  echo $clerk['uid'];?>" >
			<input name="id" type="hidden" value="<?php  echo $clerk['id'];?>" >
			<input name="submit" id="submit" type="submit" value="提交" class="btn btn-primary col-lg-1">
			<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
		</div>
	</form>
</div>
<script>
	<?php  if($clerk['name'] == $clerk['username']) { ?>
		$('[name="same"]').attr('checked', true);
		$('#username').hide();
	<?php  } ?>
	$('[name="name"]').change(function () {
		if ($('[name="same"]').is(':checked')) {
			$('[name="username"]').val($(this).val());
		}
	});
	var id = '<?php  echo $id;?>';
	$('[name="same"]').click(function() {
		if ($(this).is(':checked')) {
			$('#username').hide();
			$('[name="username"]').val($('[name="name"]').val());
		}else {
			$('#warning').hide();
			$('#username').show();
		}
	});
	$('[name="name"]').blur(function () {
		if ($('[name="same"]').is(':checked')) {
			var username = $.trim($(':text[name="name"]').val());
			var uid = $('[name="uid"]').val();
			$.post("<?php  echo url('activity/clerk/checkname')?>", {'uid' : uid, 'username' : username}, function(data) {
				var data = $.parseJSON(data);
				if (data.message.errno == '0') {
					$(':text[name="username"]').data('type', 0);
					$('#warning').show();
				}else {
					$(':text[name="username"]').data('type', 1);
					$('#warning').hide();
				}
			});
		}
	});
	$('[name="username"]').blur(function () {
		if (!$('[name="same"]').is(':checked')) {
			var username = $.trim($(':text[name="username"]').val());
			var uid = $('[name="uid"]').val();
			$.post("<?php  echo url('activity/clerk/checkname')?>", {'uid' : uid, 'username' : username}, function(data) {
				var data = $.parseJSON(data);
				if (data.message.errno == '0') {
					$(':text[name="username"]').data('type', 0);
					$('#warning').hide();
					$('#u_warning').show();
				}else {
					$(':text[name="username"]').data('type', 1);
					$('#warning').hide();
					$('#u_warning').hide();
				}
			});
		}
	});
	$('#form1').submit(function(){

		var name = $.trim($(':text[name="name"]').val());
		if (!name) {
			util.message('请填写店员名称');
			return false;
		}
		var username = $.trim($(':text[name="username"]').val());
		if (!username && !($('[name="same"]').is(':checked'))) {
			util.message('请填写登陆账号');
			return false;
		}
		if ($(':text[name="username"]').data('type') == 0) {
			util.message('登录账号已存在');
			return false;
		}
		var password = $.trim($('input[name="password"]').val());
		var repassword = $.trim($('input[name="repassword"]').val());
		<?php  if(!$clerk['uid']) { ?>
			if (!password || password.length < 8) {
				util.message('密码不能小于8位数');
				return false;
			}
			if (password != repassword) {
				util.message('两次密码输入不一致');
				return false;
			}
		<?php  } else { ?>
			if (password != ''&& password.length < 8) {
				util.message('密码不能小于8位数');
				return false;
			}
			if (password != '' && password != repassword) {
				util.message('两次密码输入不一致');
				return false;
			}
		<?php  } ?>
		var mobile = $.trim($(':text[name="mobile"]').val());
		if (!mobile) {
			util.message('请填写店员手机号');
			return false;
		}

		var store_id = $.trim($('select[name="storeid"]').val());
		if (!store_id) {
			util.message('请选择店员所在的门店.<br>');
			return false;
		}
		var phone = /^\d{11}$/;
		if(!phone.test(mobile)) {
			util.message('请填写正确的手机格式');
			return false;
		}
		return true;
	});

	$('.btn-openid').click(function(){
		var nickname = $.trim($(':text[name="nickname"]').val());
		var openid = $.trim($(':text[name="openid"]').val());
		if(!nickname && !openid) {
			util.message('请输入昵称或者openid');
			return false;
		}
		var param = {
			'nickname':nickname,
			'openid':openid
		};
		$.post("<?php  echo url('activity/clerk/verify')?>", param, function(data){
			var data = $.parseJSON(data);
			if(data.message.errno < 0) {
				util.message(data.message.message);
				return false;
			}
			$(':text[name="openid"]').val(data.message.message.openid);
			$(':text[name="nickname"]').val(data.message.message.nickname);
		});
		return false;
	});

	$('.permission').click(function(){
		var name = $(this).data('name');
		$('.permission-child-' + name).find(':checkbox').prop('checked', $(this).find(':checkbox').prop('checked'));
	});
	$('.permission-child').click(function() {
		var name = $(this).data('name');
		if (!$(this).find(':checkbox').prop('checked')) {
			$('.permission-' + name).find(':checkbox').prop('checked', false);
		} else {
			if ($('.permission-child-' + name).find(':checkbox:not(:checked)').size()) {
				$('.permission-' + name).find(':checkbox').prop('checked', false);
			} else {
				$('.permission-' + name).find(':checkbox').prop('checked', true);
			}
		}
	});

</script>
<?php  } ?>
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>