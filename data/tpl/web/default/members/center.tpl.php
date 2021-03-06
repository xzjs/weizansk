<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<style type="text/css">
    .panel-body > ul{list-style:none;margin: 0px;padding: 0px}
    .panel-body > ul li{display: inline-block}
</style>
<div class="main">
<ul class="nav nav-tabs">
    <li class="active"><a href="<?php  echo url('members/member');?>">财务中心</a></li>
    <li><a href="<?php  echo url('members/buypackage');?>">套餐购买</a></li>
    <li><a href="<?php  echo url('members/buysms');?>">短信购买</a></li>
	<?php  if($_W['isfounder']) { ?>
    <li><a href="<?php  echo url('members/record');?>">会员消费</a></li>
	<li><a href="<?php  echo url('members/configs');?>">服务配置</a></li>
    <?php  } ?>
</ul>
    <div>
        <?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('members/common/account', TEMPLATE_INCLUDEPATH)) : (include template('members/common/account', TEMPLATE_INCLUDEPATH));?>
        <aside style="width: 50%;float: left">
            <section>
                <div class="panel panel-default">
                    <div class="panel-heading">用户充值中心</div>
                    <div class="panel-body">
                        <form action="<?php  echo url('members/recharge')?>" class="form-horizontal form" method="post" enctype="multipart/form-data" target="_blank">
                            <div class="form-group">
                                <div class="col-sm-2"></div>
                                <div class="col-sm-8 text-center">
                                    <label class="radio-inline">
                                        <input type="radio" name="pay_type" value="baifubao" checked> 百付宝付款
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="pay_type" value="alipay"> 支付宝付款
                                    </label>
                                </div>
                                <div class="col-sm-2"></div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-2"></div>
                                <div class="col-sm-8">
                                    <div class="input-group" >
                                        <span class="input-group-addon">充值帐号</span>
                                        <input class="form-control" type="text" placeholder="充值帐号" value="<?php  echo $_W['user']['username'];?>"  disabled="disabled" >
                                        <input type="hidden" name="recharge_type" value="credit2">
                                    </div>
                                </div>
                                <div class="col-sm-2"></div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-2"></div>
                                <div class="col-sm-8">
                                    <div class="input-group" >
                                        <span class="input-group-addon">充值金额(<span style="color: red">最少充值10块</span>)</span>
                                        <input class="form-control" name="recharge_number" type="text" placeholder="充值金额" value="10"  >
                                        <span class="input-group-addon">元</span>
                                    </div>
                                </div>
                                <div class="col-sm-2"></div>
                            </div>
                        </form>
                    </div>
                    <div class="panel-footer" style="text-align: center">
                        <button type="button" class="btn btn-warning buy">确认充值</button>
                    </div>
                </div>
            </section>
        </aside>

        <aside style="width: 22%;float: left;margin-left: 5px">
            <section>
                <div class="list-group bg-white set_list_group">
                    <span class="list-group-item">
                        <h4 style="text-align: center;color: red">服务公告</h4>
                        <div style="font-size: 13px">
                            <?php  echo html_entity_decode($settings['service_gg']);?>
                        </div>
                        <h4 style="text-align: center;color: red;margin-top: 20px">在线客服</h4>
                        <div>
                            <ul style="list-style:none;margin: 0px;padding: 0px">
                                <?php  if(is_array($qqs)) { foreach($qqs as $service) { ?>
                                <li style="margin: 5px;display: inline-block">
                                    <i class="fa fa-qq"></i> <a target="_blank" style="color: blue" href="http://wpa.qq.com/msgrd?v=3&amp;uin=<?php  echo $service['qq'];?>&amp;site=qq&amp;menu=yes"><?php  echo $service['name'];?></a>
                                </li>
                                <?php  } } ?>
                            </ul>
                        </div>
                    </span>
                </div>
            </section>
        </aside>
    </div>
    <div style="float: left;width:100%;">
        <?php  $sett = uni_setting($_W['uniacid'], array('groupdata'));?>
        <?php  if(is_array($_W["user"]["packages"])) { foreach($_W["user"]["packages"] as $item) { ?>
        <?php  if(is_array($item)) { ?>
        <aside>
            <section>
                <div class="panel panel-<?php  if($_W['user']['account']['groupid'] == $item['id']) { ?>info<?php  } else { ?>default<?php  } ?>">
                    <div class="panel-heading">套餐：<?php  echo $item["name"];?> <?php  if($_W["user"]["account"]["groupid"] == $item['id']) { ?><span class="label label-success">正在使用</span><?php  } ?></div>
                    <div class="panel-body">
                        <ul class="list-group">
                            <?php  if(is_array($item['modules'])) { foreach($item['modules'] as $module) { ?>
                            <li class="list-group-item" style="margin: 5px"><a href="<?php  echo url('home/welcome/ext',array('m'=>$module['name']))?>"><?php  echo $module["title"];?></a> </li>
                            <?php  } } ?>
                        </ul>
                    </div>
                    <?php  if($_W["user"]["account"]["groupid"] <> $item['id']) { ?>
                    <?php  } else { ?>
                    <div class="panel-footer" style="text-align: right;font-weight: bold;<?php  if($_W['user']['packages']['isexpire'] <=0) { ?>color: blue;<?php  } else { ?>color: red;<?php  } ?>">
                        <input type="checkbox" id="is_auto" value="true" <?php  if($sett["groupdata"]["is_auto"] == 1) { ?> checked="checked" <?php  } ?>> 开启自动续费
                    </div>
                    <?php  } ?>
                </div>
            </section>
        </aside>
        <?php  } ?>
        <?php  } } ?>
    </div>
</div>
<div class="modal fade" id="myModal" data-backdrop="static" style="top: 25%">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">充值提醒</h4>
            </div>
            <div class="modal-body" style="line-height: 30px;text-indent: 2em;font-size: 16px;font-weight: bold">
                请在新弹出的第三方支付平台完成支付，即可自动充值到帐户，未完成支付前请不要关闭本窗口。<br/>
                <span style="font-weight: normal;font-size: 14px;color: red">若充值过程中网络中断或失败，请拨打我司电话.</span>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning done">完成支付</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script type="text/javascript">
    require(['bootstrap'],function($){
        $("button.ChangePackage").click(function() {
            var id = $(this).attr("id");
            if(parseInt(id) <= 0) return;
            $.post("<?php  echo url('members/buypackage');?>", {'groupid' : parseInt(id)}, function(data){
                if(data == 'illegal-uniacid') {
                    u.message('您没有操作该公众号的权限');
                } else if (data == 'illegal-group') {
                    u.message('您没有使用该服务套餐的权限');
                } else {
                    location.reload();
                }
            });
        });
        $("#is_auto").on("click",function (){
            var is_auto = 0;
            if($(this).is(':checked')) {
                console.log("c");
                is_auto = 1;
            }
            $.ajax({
                'url':"<?php  echo url('members/site')?>",
                'data':{is_auto:is_auto},
                'type':'POST',
                'async':'true',
                'dataType':'json',
                'success':function(data){
                    console.debug(data);
                    alert(data.message);
                }
            });
        });
        $("button.buy").on("click",function(){
            $("button.buy").removeAttr("disabled");
            $('#myModal').modal('show');
            $("form.form").action = "<?php  echo url('members/recharge')?>";
            $("form.form").submit();
        });
        $("button.done").on("click",function(){
            location.reload();
        });
    });
</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('members/common/footer', TEMPLATE_INCLUDEPATH)) : (include template('members/common/footer', TEMPLATE_INCLUDEPATH));?>