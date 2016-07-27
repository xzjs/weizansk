<?php defined('IN_IA') or exit('Access Denied');?><style>
	.table td,th{text-align: center}
</style>
<?php  if($_W['debug_data']) { ?>
	<?php  if($_W['debug_data']['is_default']) { ?>
		<div class="alert alert-info">
			该关键字触发了默认回复。
			<?php  if(!empty($_W['debug_data']['params'])) { ?>
			系统的默认回复关键字对应以下几条回复, <a class="text-danger" href="<?php  echo url('platform/special/display');?>" target="_blank">查看默认回复</a>
			<?php  } else { ?>
			但系统默认回复没有设置关键字或设置的关键字没有有效的回复规则，<a class="text-danger" href="<?php  echo url('platform/special/display');?>" target="_blank">查看默认回复</a>
			<?php  } ?>
		</div>
	<?php  } ?>
	<?php  if(!empty($_W['debug_data']['params'])) { ?>
	<table class="table table-hover table-bordered">
		<thead>
		<tr>
			<th>模块</th>
			<th>标识</th>
			<th>关键字</th>
			<th>规则id</th>
		</tr>
		</thead>
		<tbody>
		<?php  if(is_array($_W['debug_data']['params'])) { foreach($_W['debug_data']['params'] as $row) { ?>
			<tr <?php  if($_W['debug_data']['hitparam']['rule'] == $row['rule']) { ?>class="danger"<?php  } ?>>
				<td><?php  echo $_W['modules'][$row['module']]['title'];?></td>
				<td><?php  echo $_W['modules'][$row['module']]['name'];?></td>
				<td><?php  echo $row['keyword']['content'];?></td>
				<td><?php  echo $row['keyword']['rid'];?></td>
		</tr>
		<?php  } } ?>
		</tbody>
	</table>
	<div class="help-block"><span class="text-danger">红色选中代表最终回复给粉丝的回复消息</span></div>
	<?php  } ?>
<?php  } ?>
