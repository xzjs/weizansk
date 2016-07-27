<?php defined('IN_IA') or exit('Access Denied');?><html ng-app="diandanbao" class="ng-scope">
<head>
    <style type="text/css">
        @charset "UTF-8";
        [ng\:cloak], [ng-cloak], [data-ng-cloak], [x-ng-cloak], .ng-cloak, .x-ng-cloak, .ng-hide:not(.ng-hide-animate) {
            display: none !important;
        }

        ng\:form {
            display: block;
        }</style>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta content="telephone=no" name="format-detection">
    <title>门店搜索</title>
    <link data-turbolinks-track="true" href="<?php echo RES;?>/mobile/<?php  echo $this->cur_tpl?>/assets/diandanbao/weixin.css?v=1" media="all" rel="stylesheet">
    <style type="text/css">@media screen {
        .smnoscreen {
            display: none
        }
    }
    @media print {
        .smnoprint {
            display: none
        }
    }</style>
</head>
<body>
<!-- ngView:  -->
<div ng-view="" style="height: 100%;" class="ng-scope">
    <?php  include $this->template($this->cur_tpl.'/_menu');?>
    <div id="search-word-page" class="ng-scope">
        <div class="ddb-nav-header search-header">
            <div class="search-input">
                <span class="fa fa-search green voice-input ng-scope" ng-click="startRecord()" ng-if="!is_recording"></span>
                <input type="text" placeholder="输入门店名称" name="word" id="word" class="query-word ng-pristine ng-untouched ng-valid">
            </div>
            <div class="operation-button green search-cancel" onclick="search()">搜索</div>
        </div>
        <div class="main-view">
            <?php  if(!empty($words)) { ?>
            <div class="keywords-section">
                <?php  $itemindex = 1;?>
                <?php  if(is_array($words)) { foreach($words as $item) { ?>
                <?php  if($itemindex%5 == 1) { ?>
                <?php  $labelcolor = 'label-green';?>
                <?php  } else if($itemindex%5 == 2) { ?>
                <?php  $labelcolor = 'label-red';?>
                <?php  } else if($itemindex%5 == 3) { ?>
                <?php  $labelcolor = 'label-orange';?>
                <?php  } else if($itemindex%5 == 4) { ?>
                <?php  $labelcolor = 'label-blue';?>
                <?php  } else if($itemindex%5 == 0) { ?>
                <?php  $labelcolor = 'label-pink';?>
                <?php  } ?>
                <div class="keyword ng-binding ng-scope <?php  echo $labelcolor;?>" onclick="searchword('<?php  echo $item;?>');">
                    <?php  echo $item;?>
                </div>
                <?php  $itemindex++;?>
                <?php  } } ?>
                <div class="space"></div>
            </div>
            <?php  } ?>
            <div class="search-result">
                <?php  if(is_array($list)) { foreach($list as $item) { ?>
                <a class="list-item ng-binding ng-scope" href="<?php  echo $this->createMobileUrl('detail', array('id' => $item['id']), true)?>">
                    <?php  echo $item['title'];?>
                </a>
                <?php  } } ?>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo RES;?>/mobile/<?php  echo $this->cur_tpl?>/assets/diandanbao/jquery-1.11.3.min.js"></script>
<script>
    function searchword(search) {
        window.location.href = "<?php  echo $this->createMobileUrl('search', array(), true)?>" + "&searchword=" + search;
    }

    function search() {
        var word = $('#word').val();
        if (word == '') {
            alert('请输入搜索关键字！');
            return false;
        }
        window.location.href = "<?php  echo $this->createMobileUrl('search', array(), true)?>" + "&searchword=" + word;
    }
</script>
</body>
</html>