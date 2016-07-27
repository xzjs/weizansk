<?php defined('IN_IA') or exit('Access Denied');?><div class="ddb-nav-footer ng-scope" common-footer="">
    <?php  if(empty($setting) || $setting['mode']==0) { ?>
    <a class="nav-item <?php  if($cur_nave=='home') { ?>active<?php  } ?>" href="<?php  echo $this->createMobileUrl('waprestlist', array(), true)?>"><i class="fa fa-home"></i>
        <div class="nav-text">首页</div>
    </a>
    <?php  } else { ?>
    <a class="nav-item <?php  if($cur_nave=='detail') { ?>active<?php  } ?>" href="<?php  echo $this->createMobileUrl('detail', array('id' => $setting['storeid']), true)?>"><i class="fa fa-home"></i>
        <div class="nav-text">门店</div>
    </a>
    <?php  } ?>

    <?php  if(empty($setting) || $setting['mode']==0) { ?>
    <a class="nav-item ng-scope <?php  if($cur_nave=='search') { ?>active<?php  } ?>"  href="<?php  echo $this->createMobileUrl('search', array(), true)?>"><i class="fa fa-search"></i>
        <div class="nav-text">搜索</div>
    </a>
    <a class="nav-item ng-scope <?php  if($cur_nave=='collection') { ?>active<?php  } ?>"  href="<?php  echo $this->createMobileUrl('collection', array(), true)?>" ng-if="!current_shop.is_single">
        <i class="fa fa-star"></i>
        <div class="nav-text">收藏</div>
    </a>
    <?php  } ?>
    <a class="nav-item <?php  if($cur_nave=='my') { ?>active<?php  } ?>" href="<?php  echo $this->createMobileUrl('order', array(), true)?>"><i class="fa fa-user"></i>
        <div class="nav-text">我的</div>
    </a>
</div>