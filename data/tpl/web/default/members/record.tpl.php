<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<style type="text/css">
    .panel-body > ul{list-style:none;margin: 0px;padding: 0px}
    .panel-body > ul li{display: inline-block}
</style>
<div class="main">
 <ul class="nav nav-tabs">
    <li><a href="<?php  echo url('members/member');?>">财务中心</a></li>
    <li><a href="<?php  echo url('members/buypackage');?>">套餐购买</a></li>
    <li><a href="<?php  echo url('members/buysms');?>">短信购买</a></li>
	<?php  if($_W['isfounder']) { ?>
    <li class="active"><a href="<?php  echo url('members/record');?>">会员消费</a></li>
	<li><a href="<?php  echo url('members/configs');?>">服务配置</a></li>
    <?php  } ?>
</ul>
    <div style="width: 100%" >
        <aside>
            <section>
                <?php  if($do == 'cz') { ?>
                <div class="panel panel-default">
                    <div class="panel-heading">充值记录</div>
                    <div class="panel-body">
                        <table class="table mytable table-striped b-t text-sm">
                            <thead>
                            <tr>
                                <th width="20"></th>
                                <th class="col-sm-1">货币</th>
                                <th class="col-sm-2">订单号</th>
                                <th class="col-sm-1">充值金额</th>
                                <th class="col-sm-2">充值时间</th>
                                <th class="col-sm-1">状态</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php  if(is_array($list)) { foreach($list as $item) { ?>
                            <tr>
                                <th width="20"></th>
                                <th class="col-sm-4"><?php  if($item['credittype']=='credit2') { ?>交易币<?php  } else { ?>积分<?php  } ?></th>
                                <th><?php  echo $item['orderid'];?></th>
                                <th class="col-sm-1"><?php  echo $item['money'];?></th>
                                <th><?php  echo date('Y-m-d H:i',$item['order_time'])?></th>
                                <th><?php  if($item['status']==1) { ?><span class="label label-success">已付款</span><?php  } else { ?><span class="label label-warning">待付款</span><?php  } ?></th>
                            </tr>
                            <?php  } } ?>
                            </tbody>
                        </table>
                        <?php  echo $pager;?>
                    </div>
                </div>
                <?php  } else { ?>
                <div class="panel panel-default">
                    <div class="panel-heading"><?php  if($_GPC["type"]=='credit2') { ?>充值<?php  } else { ?>消费<?php  } ?>记录</div>
                    <div class="panel-body">
                        <table class="table mytable table-striped b-t text-sm">
                            <thead>
                            <tr>
                                <th width="20"></th>
                                <th class="col-sm-1">货币</th>
                                <th class="col-sm-2">金额</th>
                                <th><?php  if($_GPC["type"]=='credit2') { ?>充值<?php  } else { ?>消费<?php  } ?>原因</th>
                                <th class="col-sm-2"><?php  if($_GPC["type"]=='credit2') { ?>充值<?php  } else { ?>消费<?php  } ?>时间</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php  if(is_array($list)) { foreach($list as $item) { ?>
                            <tr>
                                <th width="20"></th>
                                <th class="col-sm-4"><?php  if($item['credittype']=='credit2') { ?>交易币<?php  } else { ?>积分<?php  } ?></th>
                                <th><?php  echo $item['num'];?></th>
                                <th class="col-sm-1"><?php  echo htmlspecialchars_decode($item['remark'])?></th>
                                <th><?php  echo date('Y-m-d H:i',$item['createtime'])?></th>
                            </tr>
                            <?php  } } ?>
                            </tbody>
                        </table>
                        <?php  echo $pager;?>
                    </div>
                </div>
                <?php  } ?>
            </section>
        </aside>
    </div>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>