<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header-base', TEMPLATE_INCLUDEPATH)) : (include template('common/header-base', TEMPLATE_INCLUDEPATH));?>

<script>

	$('#form1').submit(function(){

		if($.trim($(':text[name="username"]').val()) == '') {

			util.message('û�������û���.', '', 'error');

			return false;

		}

		if($('#password').val() == '') {

			util.message('û����������.', '', 'error');

			return false;

		}

		if($('#password').val() != $('#repassword').val()) {

			util.message('������������벻һ��.', '', 'error');

			return false;

		}

/* 		<?php  if(is_array($extendfields)) { foreach($extendfields as $item) { ?>

		<?php  if($item['required']) { ?>

			if (!$.trim($('[name="<?php  echo $item['field'];?>"]').val())) {

				util.message('<?php  echo $item['title'];?>Ϊ������뷵���޸ģ�', '', 'error');

				return false;

			}

		<?php  } ?>

		<?php  } } ?>

 */		<?php  if($setting['register']['code']) { ?>

		if($.trim($(':text[name="code"]').val()) == '') {

			util.message('û��������֤��.', '', 'error');

			return false;

		}

		<?php  } ?>

	});

	var h = document.documentElement.clientHeight;

	$(".login").css('min-height',h);

</script>

<style>

	@media screen and (max-width:767px){.register .panel.panel-default{width:90%; min-width:300px;}}

	@media screen and (min-width:768px){.register .panel.panel-default{width:70%;}}

	@media screen and (min-width:1200px){.register .panel.panel-default{width:50%;}}

</style>

<div class="register">

	<div class="logo"><a href="./?refresh" <?php  if(!empty($_W['setting']['copyright']['flogo'])) { ?>style="background:url('<?php  echo tomedia($_W['setting']['copyright']['flogo']);?>') no-repeat;"<?php  } ?>></a></div>

	<div class="clearfix" style="margin-bottom:5em;">

		<div class="panel panel-default container">

			<div class="panel-body">

				<form action="" method="post" role="form" id="form1">

					<div class="form-group">

						<label>�û���:<span style="color:red">*</span></label>

						<input name="username" type="text" class="form-control" placeholder="�������û���">

					</div>

					<div class="form-group">

						<label>����:<span style="color:red">*</span></label>

						<input name="password" type="password" id="password" class="form-control" placeholder="�����벻����8λ������">

					</div>

					<div class="form-group">

						<label>ȷ������:<span style="color:red">*</span></label>

						<input name="password" type="password" id="repassword" class="form-control" placeholder="���ٴ����벻����8λ������">

					</div>

					<?php  if($extendfields) { ?>

						<?php  if(is_array($extendfields)) { foreach($extendfields as $item) { ?>

							<div class="form-group">

								<label><?php  echo $item['title'];?>��<?php  if($item['required']) { ?><span style="color:red">*</span><?php  } ?></label>

								<?php  echo tpl_fans_form($item['field'])?>

							</div>

						<?php  } } ?>

					<?php  } ?>

					<?php  if($setting['register']['code']) { ?>

						<div class="form-group">

							<label style="display:block;">��֤��:<span style="color:red;">*</span></label>

							<input name="code" type="text" class="form-control" placeholder="��������֤��" style="width:65%;display:inline;margin-right:17px">

							<img src="<?php  echo url('utility/code');?>" class="img-rounded" style="cursor:pointer;" onclick="this.src='<?php  echo url('utility/code');?>' + Math.random();" />

						</div>

					<?php  } ?>

					<!--div class="form-group">

						<label>������:<span style="color:red">*</span></label>

						<input name="invitation" type="text" class="form-control" placeholder="������������">

					</div-->

					<div class="pull-right">

						<a href="<?php  echo url('user/login');?>" class="btn btn-link">��¼</a>

						<input type="submit" name="submit" value="ע��" class="btn btn-default" />

						<input name="token" value="<?php  echo $_W['token'];?>" type="hidden" />

					</div>

				</form>

			</div>

		</div>

	</div>

	<div class="center-block footer" role="footer">

		<div class="text-center">

			<?php  if(empty($_W['setting']['copyright']['footerright'])) { ?><a href="http://#">��˳����</a>&nbsp;&nbsp;<a href="http://#">��˳����</a>&nbsp;&nbsp;<a href="#">��ϵ�ͷ�</a><?php  } else { ?><?php  echo $_W['setting']['copyright']['footerright'];?><?php  } ?> &nbsp; &nbsp; <?php  if(!empty($_W['setting']['copyright']['statcode'])) { ?><?php  echo $_W['setting']['copyright']['statcode'];?><?php  } ?>

		</div>

		<div class="text-center">

			<?php  if(empty($_W['setting']['copyright']['footerleft'])) { ?>Powered by <a href="#"><b>��˳</b></a> v<?php echo IMS_VERSION;?> &copy; 2016 <a href="http://#">www.yushunbox.com</a><?php  } else { ?><?php  echo $_W['setting']['copyright']['footerleft'];?><?php  } ?>

		</div>

	</div>

</div>

</body>

</html>