<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<input type="hidden" name="storeid" value="<?php  echo $storeid;?>" />
<?php  echo $this -> set_tabbar($action, $storeid);?>
<style>
    .checkbox-dish {
        /*background: #368ee0;*/
        background: #5ac5d4;
        border-radius: 3px;
        height: 40px;
        padding: 10px 5px 0px 5px;
        margin-bottom: 5px;
    }
</style>
<?php  if($operation == 'post') { ?>
<div class="main">
	<form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
	<input type="hidden" name="parentid" value="<?php  echo $parent['id'];?>" />
        <div class="panel panel-default">
            <div class="panel-heading">
                智能点餐编辑
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">适用人数</label>
                    <div class="col-sm-9">
                        <input type="text" name="catename" class="form-control" value="<?php  echo $intelligent['name'];?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">商品选择</label>
                    <div class="col-sm-9">
                        <?php  if(is_array($categorys)) { foreach($categorys as $category) { ?>
                        <b><?php  echo $category['name'];?></b><br/>
                        <?php  if(is_array($goods_arr[$category['id']])) { foreach($goods_arr[$category['id']] as $item) { ?>
                        <label class="checkbox-dish">
                            <input type="checkbox" name="goodsids[]" value="<?php  echo $item['id'];?>" <?php  if(!empty($intelligent['name'])) { ?><?php  if(in_array($item['id'], $goodsids)) { ?>checked<?php  } ?><?php  } ?>> <span class="label"><?php  echo $item['title'];?></span>
                        </label>
                        <?php  } } ?>
                        <br>
                        <?php  } } ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">显示顺序</label>
                    <div class="col-sm-9">
                        <input type="text" name="displayorder" class="form-control" value="<?php  echo $intelligent['displayorder'];?>" />
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group col-sm-12">
            <input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1" />
            <input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
        </div>
	</form>
</div>
<?php  } else if($operation == 'display') { ?>
<div class="main">
    <div class="panel panel-default">
        <div class="panel-body">
            <a class="btn btn-default" href="<?php  echo $this->createWebUrl('intelligent', array('op' => 'post', 'storeid' => $storeid))?>"><i class="fa fa-plus"></i> 添加套餐</a>
            <a class="btn btn-default" href="<?php  echo $this->createWebUrl('goods', array('op' => 'display', 'storeid' => $storeid))?>"><i class="fa fa-list"></i> 商品管理</a>
        </div>
    </div>
    <div class="panel panel-default">
        <form action="" method="post" class="form-horizontal form" >
        <div class="table-responsive panel-body">
        <table class="table table-hover">
            <thead class="navbar-inner">
            <tr>
                <th style="width:10%;">显示顺序</th>
                <th style="width:8%;">适用人数</th>
                <th style="width:72%;text-align: left;">商品</th>
                <th style="width:10%;text-align:right;">操作</th>
            </tr>
            </thead>
            <tbody id="level-list">
            <?php  if(is_array($intelligents)) { foreach($intelligents as $row) { ?>
            <tr>
                <td><input type="text" class="form-control" name="displayorder[<?php  echo $row['id'];?>]" value="<?php  echo $row['displayorder'];?>"></td>
                <td style="text-align: center;"><?php  echo $row['name'];?></td>
                <td style="white-space:normal;">
                    <?php  $goodsids = explode(',', $row['content']);?>
                    <?php  if(is_array($goodsids)) { foreach($goodsids as $goodsid) { ?>
                    <label class="checkbox-dish"><span class="label" ><?php  echo $goods_arr[$goodsid];?></span></label>
                    <?php  } } ?>
                </td>
                <td style="text-align:right;"><a class="btn btn-default btn-sm" href="<?php  echo $this->createWebUrl('intelligent', array('op' => 'post', 'id' => $row['id'], 'storeid' => $storeid))?>" title="编辑"><i class="fa fa-pencil"></i></a>&nbsp;&nbsp;<a class="btn btn-default btn-sm" href="<?php  echo $this->createWebUrl('intelligent', array('op' => 'delete', 'id' => $row['id'], 'storeid' => $storeid))?>" onclick="return confirm('确认删除此分类吗？');return false;" title="删除"><i class="fa fa-times"></i></a></td>
            </tr>
            <?php  } } ?>
            <tr>
                <td colspan="4">
                    <input name="submit" type="submit" class="btn btn-primary" value="批量更新排序">
                    <input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
                </td>
            </tr>
            </tbody>
        </table>
        </div>
        </form>
    </div>
    <?php  echo $pager;?>
</div>
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>