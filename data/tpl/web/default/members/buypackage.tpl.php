<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<?php  $packages = get_AllPackage()?>
<style type="text/css">
    .panel-body > ul{list-style:none;margin: 0px;padding: 0px}
    .panel-body > ul li{display: inline-block}
</style>
<div class="main">
<ul class="nav nav-tabs">
    <li><a href="<?php  echo url('members/member');?>">财务中心</a></li>
    <li class="active"><a href="<?php  echo url('members/buypackage');?>">套餐购买</a></li>
    <li><a href="<?php  echo url('members/buysms');?>">短信购买</a></li>
		<?php  if($_W['isfounder']) { ?>
    <li><a href="<?php  echo url('members/record');?>">会员消费</a></li>
	<li><a href="<?php  echo url('members/configs');?>">服务配置</a></li>
    <?php  } ?>
</ul>
    <div>
        <?php  $sett = get_settings();?>
        <?php  if(is_array($list)) { foreach($list as $item) { ?>
        <?php  if($sett["over_group"] <> $item["id"]) { ?>
        <?php  if($item["hide"] == 0) { ?>
        <?php  list($price,$discount) = check_price($item['price'])?>
        <aside style="margin: 10px">
            <section>
                <div class="panel panel-<?php  if($_W['user']['account']['groupid'] == $item['id']) { ?>info<?php  } else { ?>default<?php  } ?>">
                    <div class="panel-heading">
                        套餐：<?php  echo $item["name"];?>
                        <?php  if($_W["user"]["account"]["groupid"] == $item['id']) { ?>
                            <span class="label label-success">正在使用</span>
                        <?php  } ?>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <span>套餐价格: <?php  echo $item['price'];?> / <?php  echo $sett['package_day'];?> 天</span>
                        <?php  if($discount>0) { ?>&nbsp;&nbsp;&nbsp;&nbsp;
                        可享受折扣：<span style="color: red;font-weight: bold"><?php  echo $discount;?></span> 折,优惠价格:<span style="color: red;font-weight: bold"> <?php  echo $price;?> </span>/ <?php  echo $sett['package_day'];?>天
                        <?php  } ?>
                    </div>
                    <div class="panel-body">
                        <ul class="list-group">
                            <?php  if(is_array($item['modules'])) { foreach($item['modules'] as $module) { ?>
                            <li class="list-group-item" style="margin: 5px"><a href="<?php  echo url('home/welcome/ext',array('m'=>$module['name']))?>"><?php  echo $module["title"];?></a> </li>
                            <?php  } } ?>
                        </ul>
                    </div>
                    <?php  if($_W["user"]["account"]["groupid"] != -1) { ?>
                        <?php  if($_W["user"]["account"]["groupid"] <> $item['id']) { ?>
                            <?php  if(count($item['modules']) > $curr_count) { ?>
                                <div class="panel-footer" style="text-align: right">
                                    <button type="button" class="btn btn-warning ChangePackage" data-name = "<?php  echo $item['name'];?>" data-day = "<?php  echo $sett['package_day'];?>" data-price = "<?php  echo $price;?>" data-pid="<?php  echo $item['id'];?>" data-toggle="modal" data-target="#myModal">升级套餐</button>
                                </div>
                            <?php  } else { ?>
                                <div class="panel-footer" style="text-align: right">
                                    <button type="button" class="btn btn-warning ChangePackage" data-name = "<?php  echo $item['name'];?>" data-day = "<?php  echo $sett['package_day'];?>" data-price = "<?php  echo $price;?>" data-pid="<?php  echo $item['id'];?>" data-toggle="modal" data-target="#myModal">降级套餐</button>
                                </div>
                            <?php  } ?>
                        <?php  } else { ?>
                            <div class="panel-footer" style="text-align: right;font-weight: bold;<?php  if($_W['user']['packages']['isexpire'] <=0) { ?>color: blue;<?php  } else { ?>color: red;<?php  } ?>;padding: 8px 12px;">
                                到期时间: <?php  echo $_W["user"]["packages"]['endtime'];?>&nbsp;&nbsp;
                                <button type="button" class="btn btn-warning ChangePackage" data-name = "<?php  echo $item['name'];?>" data-day = "<?php  echo $sett['package_day'];?>" data-price = "<?php  echo $price;?>" data-pid="<?php  echo $item['id'];?>" data-toggle="modal" data-target="#myModal">续费套餐</button>
                                
                            </div>
                        <?php  } ?>
                    <?php  } ?>

                </div>
            </section>
        </aside>
        <?php  } ?>
        <?php  } ?>
        <?php  } } ?>
    </div>
</div>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="top: 25%">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">套餐操作</h4>
            </div>
            <div class="modal-body">
                <div class="panel-body" style="margin: 15px">
                    <form action="" class="form-horizontal form" id="form_1" method="post" enctype="multipart/form-data">
                        <!--<div class="form-group">-->
                            <!--<label class="col-sm-2"></label>-->
                            <!--<div class="col-sm-8">-->
                                <!--<div class="input-group" >-->
                                    <!--<span class="input-group-addon">份数</span>-->
                                    <!--<input data-reg="^[1-9][0-9]{0,2}$" class="total form-control" name="total" type="text" placeholder="份数必须大于0" value="" >-->
                                <!--</div>-->
                            <!--</div>-->
                            <!--<label class="col-sm-2"></label>-->
                            <!---->
                        <!--</div>-->
                        <input type="hidden" name="pid"/>
                        <input type="hidden" name="total" value="1"/>
                        <div class="form-group">
                            <label class="col-sm-1"></label>
                            <div class="col-sm-10" style="text-align: center;font-size: 18px">
                                购买 <span class="name" style="font-weight: bold;color: red;font-size: 25px"></span> 套餐 <span class="day" style="font-weight: bold;color: red;font-size: 25px"></span> 天，需要消费
                                <span class="money" style="font-weight: bold;color: red;font-size: 25px"></span> 元
                            </div>
                            <label class="col-sm-1"></label>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning btn-submit">提交</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script language='javascript'>
    require(['jquery', 'util'], function($, u){
        $('#myModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var package_id = button.data('pid'); // Extract info from data-* attributes
            // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
            // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
            var price = button.data('price');
            var name = button.data('name');
            var day = button.data('day');
            var modal = $(this);
            modal.find('input[name=pid]').val(package_id);
            $('span.money').html((Number(price)).toFixed(2));
            $('span.name').html(name);
            $('span.day').html(day);
            modal.find('input[name=total]').on('blur', function(){
                if(!submit_check()){
                    return;
                }
                //计算价格
                var package_price = (Number(price * $(this).val())).toFixed(2);//总价
                $('span.money').html(package_price);
                $('span.number').html($(this).val());
            });
        }).on('hidden.bs.modal', function (event) {
            //$(this).find('input[name=total]').unbind().val('');
        });
        $('#myModal').find('button.btn-submit').on("click",function(){
            $(this).attr("disabled","true");
            $.ajax({
                'url':"<?php  echo url('members/buypackage')?>",
                'data':$("form").serialize(),
                'type':'POST',
                'async':'true',
                'dataType':'json',
                'complete':function(XMLHttpRequest, textStatus){
                    $("button.btn-submit").removeAttr("disabled");
                },
                'success':function(data){
                    console.debug(data);
                    alert(data.message);
                    if(data.code>0){
                        location.reload();
                    }
                }
            });
        });
        function submit_check(){
            var reg = new RegExp($("input.total").attr("data-reg"));
            $("input.total").parent().parent().parent().removeClass("has-error");
            if(!reg.test($("input.total").val())) {
                $("input.total").parent().parent().parent().addClass("has-error");
                return false;
            }
            return true;
        }
    });
</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('members/common/footer', TEMPLATE_INCLUDEPATH)) : (include template('members/common/footer', TEMPLATE_INCLUDEPATH));?>