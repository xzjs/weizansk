<?php defined('IN_IA') or exit('Access Denied');?><html lang="zh-CN">
<head>
    <style type="text/css">@charset "UTF-8";
    [ng\:cloak], [ng-cloak], [data-ng-cloak], [x-ng-cloak], .ng-cloak, .x-ng-cloak, .ng-hide:not(.ng-hide-animate) {
        display: none !important;
    }
    ng\:form {
        display: block;
    }</style>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta content="telephone=no" name="format-detection">
    <title>商家详情</title>
    <link data-turbolinks-track="true" href="<?php echo RES;?>/mobile/<?php  echo $this->cur_tpl?>/assets/diandanbao/weixin.css" media="all" rel="stylesheet">
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
    <?php  echo register_jssdk(false);?>
</head>
<body>
<div ng-view="" style="height: 100%;" class="ng-scope">
    <div class="ddb-nav-header ng-scope" common-header="">
        <div class="nav-left-item" onclick="location.href='<?php  echo $this->createMobileUrl('waprestlist', array(), true)?>';"><i class="fa fa-angle-left"></i></div>
        <div class="header-title ng-binding"><?php  echo $title;?></div>
    </div>
    <!--footer-->
    <?php  include $this->template($this->cur_tpl.'/_menu');?>
    <div id="ddb-branch-show" class="main-view ng-scope">
        <div class="avatar-part">
            <div class="avatar">
                <img src="<?php  echo tomedia($item['logo']);?>">
            </div>
            <div class="branch-info">
                <div class="name ng-binding"><?php  echo $title;?></div>
                <div class="comments-level red">
                    <div ng-rating="branch.rating" class="ng-isolate-scope">
                        <?php  for($i=0;$i < $item['level']; $i++){ ?>
                        <i class="fa fa-star-o ng-scope"></i>
                        <?php  }?>
                    </div>
                </div>
                <div class="facilities ng-scope">
                    <?php  if($item['enable_wifi']==1) { ?>
                    <span class="facility ng-scope">
                        <i class="fa fa-wifi"></i> 支持Wifi
                    </span>
                    <?php  } ?>
                    <?php  if($item['enable_park']==1) { ?>
                    <span class="facility ng-scope">
                        <i class="fa fa-paypal"></i> 停车位
                    </span>
                    <?php  } ?>
                    <?php  if($item['enable_room']==1) { ?>
                    <span class="facility ng-scope">
                        <i class="fa fa-slideshare"></i> 包厢
                    </span>
                    <?php  } ?>
                    <?php  if($item['enable_card']==1) { ?>
                    <span class="facility ng-scope">
                        <i class="fa fa-credit-card"></i> 刷卡
                    </span>
                    <?php  } ?>
                </div>
                <div class="average-consume">
                    <?php  if(!empty($item['consume'])) { ?>
                    <span class="red ng-binding">￥<?php  echo $item['consume'];?></span>/人
                    <?php  } ?>
                    <span class="button collection <?php  if(empty($collection)) { ?>border-green<?php  } ?>">

                        <span class="ng-scope"><?php  if(empty($collection)) { ?>收藏<?php  } else { ?>已收藏<?php  } ?></span>
                    </span>
                </div>
            </div>
        </div>
        <div class="operation-navs ng-scope">
            <div class="operation-nav-item ng-scope <?php  if($item['is_reservation']!=1) { ?>inavailable<?php  } ?>">
                <a href="<?php  if($item['is_reservation']!=1) { ?>#<?php  } else { ?><?php  echo $this->createMobileUrl('reservationIndex', array('storeid' => $item['id'], 'mode' => 3), true)?><?php  } ?>">
                    <div class="icon red ng-scope"><i class="fa fa-dot-circle-o"></i></div>
                    <div class="text ng-binding"><?php  echo $item['btn_reservation'];?></div>
                </a>
            </div>
            <div class="operation-nav-item ng-scope <?php  if($item['is_meal']!=1) { ?>inavailable<?php  } ?>" onclick="$('#diaqrcode').removeClass('ng-hide');">
                <a href="#">
                    <div class="icon red ng-scope"><i class="fa fa-cutlery"></i>
                    </div>
                    <div class="text ng-binding"><?php  echo $item['btn_eat'];?></div>
                </a>
            </div>
            <div class="operation-nav-item ng-scope <?php  if($item['is_delivery']!=1) { ?>inavailable<?php  } ?>">
                <a href="<?php  if($item['is_delivery']!=1) { ?>#<?php  } else { ?><?php  echo $this->createMobileUrl('waplist', array('storeid' => $item['id'], 'mode' => 2), true)?><?php  } ?>">
                    <div class="icon red ng-scope"><i class="fa fa-truck"></i></div>
                    <div class="text ng-binding"><?php  echo $item['btn_delivery'];?></div>
                </a>
            </div>
            <div class="operation-nav-item ng-scope <?php  if($item['is_snack']!=1) { ?>inavailable<?php  } ?>">
                <a href="<?php  if($item['is_snack']!=1) { ?>#<?php  } else { ?><?php  echo $this->createMobileUrl('waplist', array('storeid' => $item['id'], 'mode' => 4), true)?><?php  } ?>">
                    <div class="icon red ng-scope"><i class="fa fa-delicious"></i></div>
                    <div class="text ng-binding"><?php  echo $item['btn_snack'];?></div>
                </a>
            </div>
            <div class="operation-nav-item ng-scope <?php  if($item['is_queue']!=1) { ?>inavailable<?php  } ?>">
                <a href="<?php  if($item['is_queue']!=1) { ?>#<?php  } else { ?><?php  echo $this->createMobileUrl('queue', array('storeid' => $item['id']), true)?><?php  } ?>">
                    <div class="icon red ng-scope"><i class="fa fa-weixin"></i></div>
                    <div class="text ng-binding"><?php  echo $item['btn_queue'];?></div>
                </a>
            </div>
            <div class="operation-nav-item ng-scope <?php  if($item['is_intelligent']!=1) { ?>inavailable<?php  } ?>">
                <a href="<?php  if($item['is_intelligent']!=1) { ?>#<?php  } else { ?><?php  echo $this->createMobileUrl('wapselect', array('storeid' => $item['id']), true)?><?php  } ?>">
                    <div class="icon red ng-scope"><i class="fa fa-bell"></i></div>
                    <div class="text ng-binding"><?php  echo $item['btn_intelligent'];?></div>
                </a>
            </div>
        </div>
        <?php  if(!empty($item['announce'])) { ?>
        <div class="notification-section">
            <div class="notice">
                <i class="fa fa-volume-up red"></i>
                <marquee behavior="alternate" scrollamount="1" scrolldelay="1" class="ng-binding"><?php  echo $item['announce'];?></marquee>
            </div>
        </div>
        <?php  } ?>
        <?php  if(!empty($item['coupon_title1']) || !empty($item['coupon_title2']) || !empty($item['coupon_title3'])) { ?>
        <div class="space-12"></div>
        <div class="space-12"></div>
        <div class="intro-section">
            <?php  if(!empty($item['coupon_title1'])) { ?>
            <a class="branch-item arrow-right" href="<?php  echo $item['coupon_link1'];?>">
                <div class="red icon">
                    <i class="fa fa-file-o red"></i>
                </div>
                <div class="label"><?php  echo $item['coupon_title1'];?></div>
            </a>
            <?php  } ?>
            <?php  if(!empty($item['coupon_title2'])) { ?>
            <a class="branch-item arrow-right" href="<?php  echo $item['coupon_link2'];?>">
                <div class="red icon">
                    <i class="fa fa-file-o red"></i>
                </div>
                <div class="label"><?php  echo $item['coupon_title2'];?></div>
            </a>
            <?php  } ?>
            <?php  if(!empty($item['coupon_title3'])) { ?>
            <a class="branch-item arrow-right" href="<?php  echo $item['coupon_link3'];?>">
                <div class="red icon">
                    <i class="fa fa-file-o red"></i>
                </div>
                <div class="label"><?php  echo $item['coupon_title3'];?></div>
            </a>
            <?php  } ?>
        </div>
        <?php  } ?>
        <div class="space-12"></div>
        <div class="space-12"></div>
        <div class="intro-section">
            <a class="branch-item arrow-right" href="<?php  echo $this->createMobileUrl('detailcontent', array('id' => $id), true)?>">
                <div class="gray icon">
                    <i class="fa fa-clock-o gray"></i>
                </div>
                <div class="label">门店详情</div>
            </a>
            <div class="location-label">
                <div class="gray icon">
                    <i class="fa fa-map-marker"></i>
                </div>
                <div class="address ng-binding"><?php  echo $item['address'];?></div>

                <div class="red phone">
                    <a href="tel:<?php  echo $item['tel'];?>"><i class="fa fa-phone"></i></a>
                </div>
                <div class="red location-address">
                    <a href="http://api.map.baidu.com/marker?location=<?php  echo $item['lat'];?>,<?php  echo $item['lng'];?>&title=<?php  echo $item['title'];?>&content=<?php  echo $item['address'];?>&output=html&src=wzj|wzj" style="color:#ef4437;"><i class="fa fa-map-marker"></i></a>
                </div>
            </div>
            <div class="branch-item">
                <div class="gray icon">
                    <i class="fa fa-clock-o gray"></i>
                </div>
                <span class="label">营业时间：</span><span class="ng-binding ng-scope"><?php  echo $item['begintime'];?>~<?php  echo $item['endtime'];?></span>
            </div>
            <?php  if(!empty($item['qq'])) { ?>
            <div class="branch-item ng-binding ng-scope">
                <div class="red icon">
                    <i class="fa fa-qq"></i>
                </div>
                <?php  echo $item['qq'];?>
            </div>
            <?php  } ?>
            <?php  if(!empty($item['weixin'])) { ?>
            <div class="branch-item ng-binding ng-scope" ng-if="branch.wechat_no">
                <div class="green icon">
                    <i class="fa fa-weixin"></i>
                </div>
                <?php  echo $item['weixin'];?>
            </div>
            <?php  } ?>
        </div>
        <div class="space-12"></div>

        <!--<div class="comments-section">-->
            <!--<a ng-href="#/branches/356/comments" href="#/branches/356/comments">-->
                <!--<div class="list-item arrow-right ng-binding">查看所有评论(0)</div>-->
            <!--</a>-->
            <!--<div ng-comments="branch.comments" class="ng-isolate-scope">&lt;!&ndash; ngRepeat: comment in comments &ndash;&gt;</div>-->
        <!--</div>-->
        <!--<div class="space-12"></div>-->
        <input type="hidden" id="curlat" name="curlat" value="0"/>
        <input type="hidden" id="curlng" name="curlng" value="0"/>
        <input type="hidden" id="isposition" name="isposition" value="<?php  echo $isposition;?>" />
        <input type="hidden" id="cururl" name="cururl" value="<?php  echo $this->createMobileurl('detail', array('id' => $id), true)?>" />
        <input type="hidden" id="fansurl" name="fansurl" value="<?php  echo $this->createMobileurl('UpdateFansPosition', array(), true)?>" />
    </div>
</div>

<div ng-alert-dialog="" ng-show="alert_content" class="ng-isolate-scope"><!-- ngIf: show --></div>
<div id="ddb-loading" style="display: none;"><i class="fa fa-spinner fa-spin"></i></div>

<div class="ng-isolate-scope">
    <div class="ddb-box ng-scope ng-hide" id="diaqrcode">
        <div class="box-mask"></div>
        <div class="ddb-alert">
            <div class="alert-title ng-binding">请确认已到店</div>
            <div class="alert-body" ng-transclude="">
                <p class="ng-scope"></p>
                <p class="ng-scope">如果您已经到店，请点击 '扫码下单' 并扫描桌子上的二维码进行店内下单。</p>
            </div>
            <div class="alert-footer">
                <div class="box-button ng-binding btn-eat-room" >扫码下单</div>
                <div class="box-button ng-binding" onclick="$('#diaqrcode').addClass('ng-hide');">待会下单</div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleFavorite() {
        alert('debug');
    }
</script>
<script src="<?php echo RES;?>/mobile/<?php  echo $this->cur_tpl?>/assets/diandanbao/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=5PESLgvMcSbSUbPjmDKgvGZ3"></script>
<script type="text/javascript" src="../addons/weisrc_dish/template/js/postion_shop.js?v=5"></script>
<script language="javascript">
    $('.collection').click(function () {
        var url = "<?php  echo $this->createMobileUrl('SetCollection', array('id' => $id), true);?>";
        $.ajax
        ({
            url: url,
            type:'POST',
            data: {},
            dataType:'json',
            error: function () {
                alert('网络通讯异常，请稍后再试！');
            },
            success: function (result) {
                if (result.status == 1) {
                    $(".collection").removeClass('border-green');
                    $(".collection > .ng-scope").html('已收藏');
                } else {
                    $(".collection").addClass('border-green');
                    $(".collection > .ng-scope").html('收藏');
                }
            }
        });
//        $(".collection").addClass('ng-hide').eq($('.ddb-tab-bar .ddb-tab-item').index(this)).removeClass('ng-hide');

    });

    $('.btn-eat-room').click(function () {
//        if (confirm('请对准桌子上的二维码进行扫描')) {
        alert('hello');
            wx.scanQRCode({
                needResult: 1, // 默认为0，扫描结果由微信处理，1则直接返回扫描结果，
                scanType: ["qrCode", "barCode"], // 可以指定扫二维码还是一维码，默认二者都有
                success: function (res) {
                    var result = res.resultStr; // 当needResult 为 1 时，扫码返回的结果
                    location.href= result;
                }
            });
//        }
    });


    $('.ddb-box').click(function () {
//        $(".filter-nav-menu > .ddb-nav-pane").addClass('ng-hide').eq($('.ddb-tab-bar .ddb-tab-item').index(this)).addClass('ng-hide');
//        $(".ddb-box").addClass('ng-hide');
    });
</script>
<script>
    wx.ready(function () {
        sharedata = {
            title: '<?php  echo $share_title;?>',
            desc: '<?php  echo $share_desc;?>',
            link: '<?php  echo $share_url;?>',
            imgUrl: '<?php  echo $share_image;?>',
            success: function(){
                //alert('感谢分享');
            },
            cancel: function(){
                //alert('cancel');
            }
        };
        wx.onMenuShareAppMessage(sharedata);
        wx.onMenuShareTimeline(sharedata);
    });
</script>
</body>
</html>