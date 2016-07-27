<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<ul class="nav nav-tabs">
	<li<?php  if($do == 'display') { ?> class="active"<?php  } ?>><a href="<?php  echo url('activity/store/dispaly');?>">商家列表</a></li>
	<li<?php  if($do == 'post') { ?> class="active"<?php  } ?>><a href="<?php  echo url('activity/store/post');?>"><?php  if($id > 0) { ?>编辑商家<?php  } else { ?>添加商家<?php  } ?></a></li>
</ul>
<?php  if($do == 'post') { ?>
<div class="clearfix">
	<form action="" method="post" class="form-horizontal form" enctype="multipart/form-data" id="form1">
		<div class="panel panel-default" id="step1">
			<div class="panel-heading">
				商家信息
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="text-danger">*</span> 商家名</label>
					<div class="col-sm-8 col-xs-12">
						<input type="text" class="form-control" name="business_name" value="<?php  echo $item['business_name'];?>"/>
						<span class="help-block">商家名不得含有区域地址信息（如，北京市XXX公司）</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"> 分店名(选填)</label>
					<div class="col-sm-8 col-xs-12">
						<input type="text" class="form-control" name="branch_name" value="<?php  echo $item['branch_name'];?>"/>
						<span class="help-block">分店名不得含有区域地址信息（如，“北京国贸店”中的“北京”）</span>
					</div>
				</div>

				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="text-danger">*</span> 类目</label>
					<div class="col-sm-8 col-xs-12">
						<?php  echo tpl_form_field_location_category('class',array('cate' => $item['category']['cate'], 'sub' => $item['category']['sub'], 'clas' =>$item['category']['clas']));?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="text-danger"></span> </label>
					<div class="col-sm-8 col-xs-12">
						<span class="help-block">请选择商家类目。商家类目必须合法有效。</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="text-danger">*</span> 地址</label>
					<div class="col-sm-8 col-xs-12">
						<?php  echo tpl_fans_form('reside',array('province' => $item['province'], 'city' => $item['city'],'district' => $item['district']));?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="text-danger">*</span> 详细地址</label>
					<div class="col-sm-8 col-xs-12">
						<input type="text" name="address" id="addresss" class="form-control" placeholder="输入详细地址，请勿重复填写省市区信息" value="<?php  echo $item['address'];?>"/>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="text-danger">*</span> 定位</label>
					<div class="col-sm-8 col-xs-12" id="map">
						<?php  echo tpl_form_field_coordinate('baidumap', array('lng' => $item['longitude'], 'lat' => $item['latitude']));?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="text-danger">*</span> 电话</label>
					<div class="col-sm-8 col-xs-12">
						<input type="text" class="form-control" name="telephone" value="<?php  echo $item['telephone'];?>"/>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="text-danger">*</span> 图片列表</label>
					<div class="col-sm-8 col-xs-12">
						<?php  if($_W['account']['level'] == ACCOUNT_SERVICE_VERIFY) { ?>
							<?php  echo tpl_form_field_wechat_multi_image('photo_list', '', '', array('mode' => 'file_upload'));?>
						<?php  } else { ?>
							<?php  echo tpl_form_field_multi_image('photo_list', $item['photo_list'],'');?>
						<?php  } ?>
						<span class="help-block">图片只支持jpg格式,大小不超过1M</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="text-danger"></span> 人均价格</label>
					<div class="col-sm-8 col-xs-12">
						<input type="text" name="avg_price" class="form-control" value="<?php  echo $item['avg_price'];?>"/>
						<span class="help-block">人均价格，大于0的整数,单位为人民币（元）</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="text-danger">*</span> 营业时间</label>
					<div class="col-sm-9 col-xs-4 col-md-3">
						<div class="input-group">
							<input type="text" class="form-control" placeholder="8:00" name="open_time_start" value="<?php  echo $item['open_time_start'];?>">
							<span class="input-group-addon" id="basic-addon2">-</span>
							<input type="text" class="form-control" placeholder="24:00" name="open_time_end" value="<?php  echo $item['open_time_end'];?>">
						</div>
						<span class="help-block">营业时间，24小时制表示，如 8:00-20:00</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="text-danger"></span> 推荐</label>
					<div class="col-sm-8 col-xs-12">
						<textarea name="recommend" class="form-control" cols="30" rows="3" ><?php  echo $item['recommend'];?></textarea>
						<span class="help-block">推荐品，餐厅可为推荐菜；酒店为推荐套房；景点为 推荐游玩景点等，针对自己行业的推荐内容</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="text-danger">*</span> 特色服务</label>
					<div class="col-sm-8 col-xs-12">
						<textarea name="special" class="form-control" cols="30" rows="3"><?php  echo $item['special'];?></textarea>
						<span class="help-block">特色服务，如免费wifi，免费停车，送货上门等商户 能提供的特色功能或服务</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="text-danger"></span> 简介</label>
					<div class="col-sm-8 col-xs-12">
						<textarea name="introduction" class="form-control js-a" cols="30" rows="3"><?php  echo $item['introduction'];?></textarea>
						<span class="help-block">商户简介，主要介绍商户信息等 </span>
					</div>
				</div>
			</div>
		</div>
		<div class="form-group col-sm-12">
			<input name="submit" id="submit" type="submit" value="提交" class="btn btn-primary col-lg-1">
			<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
		</div>
	</form>
</div>
<?php  } ?>
<?php  if($do == 'display') { ?>
<div class="main">
	<div class="main table-responsive">
		<form method="post" class="form-horizontal" id="form2">
			<div class="panel panel-default">
				<div class="panel-body table-responsive">
					<table class="table table-hover">
						<thead class="navbar-inner">
						<tr>
							<th width="150">门店名称</th>
							<th width="150">分店名</th>
							<th>类型</th>
							<th>营业时间</th>
							<th>电话</th>
							<th>地址</th>
							<th width="170" style="text-align:right">操作</th>
						</tr>
						</thead>
						<tbody id="list">
						<?php  if(is_array($list)) { foreach($list as $item) { ?>
						<tr>
							<td><?php  echo $item['business_name'];?></td>
							<td><?php  echo $item['branch_name'];?></td>
							<td><?php  echo $item['category_'];?></td>
							<td><?php  echo $item['open_time'];?></td>
							<td><?php  echo $item['telephone'];?></td>
							<td><?php  echo $item['province'];?> <?php  echo $li['city'];?> <?php  echo $li['district'];?> <?php  echo $li['address'];?></td>
							<td align="right">
								<a href="<?php  echo url('activity/store/post',array('id' => $item['id'],'do' =>'post'));?>" title="编辑" class="btn btn-default">编辑</a>
								<a href="<?php  echo url('activity/store/delete', array('id' => $item['id'], 'do' => 'delete'))?>" onclick="return confirm('此操作不可恢复，确认删除？');return false;" title="删除" class="btn btn-default">删除</a>
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
<?php  } ?>
<script>
	$('#form1').submit(function() {
		if(!$.trim($(':text[name="business_name"]').val())) {
			util.message('请填写商家名');
			return false;
		}
		if(!$.trim($('select[name="class[cate]"]').val())||!$.trim($('select[name="class[sub]"]').val())) {
			util.message('请填写商家类目');
			return false;
		}
		if(!$.trim($('select[name="reside[province]"]').val())||!$.trim($('select[name="reside[city]"]').val())||!$.trim($('select[name="reside[district]"]').val())) {
			util.message('请填写完整的地址');
			return false;
		}
		if(!$.trim($(':text[name="address"]').val())) {
			util.message('请填写详细地址');
			return false;
		}
		if(!$.trim($(':text[name="baidumap[lng]"]').val())||!$.trim($(':text[name="baidumap[lat]"]').val())) {
			util.message('请选择坐标');
			return false;
		}
		if(!$.trim($(':text[name="telephone"]').val())) {
			util.message('请填写电话号码');
			return false;
		}
		if($('input[name="photo_list[]"]').size()<1) {
			util.message('请选择图片');
			return false;
		}
		if(!$.trim($(':text[name="open_time_start"]').val())|| !$.trim($(':text[name="open_time_end"]'))) {
			util.message('请填写营业时间');
			return false;
		}
		if(!$.trim($('textarea[name="special"]').val())) {
			util.message('请填写特色服务');
			return false;
		}
	});
</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>
