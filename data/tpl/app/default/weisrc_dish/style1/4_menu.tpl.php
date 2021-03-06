<?php defined('IN_IA') or exit('Access Denied');?><html lang="zh-CN"><head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<meta name="format-detection" content="telephone=no">
<title>我的商品</title>
<link rel="stylesheet" type="text/css" href="../addons/weisrc_dish/template/css/1/wei_canyin_v1.8.4.css?v=1.1.2" media="all">
<link rel="stylesheet" type="text/css" href="../addons/weisrc_dish/template/css/1/wei_dialog_v1.2.1.css?v=1.0" media="all">
<script type="text/javascript" src="../addons/weisrc_dish/template/js/1/wei_webapp_v2_common_v1.9.4.js"></script>
<script type="text/javascript" src="../addons/weisrc_dish/template/js/1/wei_dialog_v1.9.9.js?v=1"></script>
<script type="text/javascript" src="../addons/weisrc_dish/template/js/2/jQuery.js"></script>
<link rel="stylesheet" type="text/css" href="../addons/weisrc_dish/template/css/2/mobiscroll.custom-2.6.2.min.css" media="all">
<link data-turbolinks-track="true" href="<?php echo RES;?>/mobile/<?php  echo $this->cur_tpl?>/assets/diandanbao/font.css?v=1" media="all" rel="stylesheet">
<script type="text/javascript" src="../addons/weisrc_dish/template/js/2/mobiscroll.custom-2.6.2.min.js"></script>

<style>abbr,article,aside,audio,canvas,datalist,details,dialog,eventsource,fieldset,figure,figcaption,footer,header,hgroup,mark,menu,meter,nav,output,progress,section,small,time,video,legend{display:block;}</style>

<script type="text/javascript">
    $(function () {
        var dat = new Date();
        var curr = dat.getFullYear();
        var currM = dat.getMonth();
        var currD = dat.getDate();
        var opt = {}
        opt.date = { preset: 'date', minDate: new Date(curr, currM, currD, 9, 22), maxDate: new Date(curr, currM + 1, currD, 15, 44), stepMinute: 5 };
        opt.datetime = { preset: 'datetime', minDate: new Date(2012, 3, 10, 9, 22), maxDate: new Date(2014, 7, 30, 15, 44), stepMinute: 5 };
        opt.time = { preset: 'time' };
        $('#sdate').scroller('destroy').scroller($.extend(opt['date'], { theme: 'ios', mode: 'scroller', display: 'bottom', lang: 'zh', dateOrder: 'yymmdd', dateFormat: 'yy-mm-dd' }));
    });
</script>
<style>
    .btn_1{
        display:block;
        border:1px solid #d8d8d8;
        border-radius:3px;
        padding:10px;
        color:#666;
        background:-webkit-gradient(linear, 0 0, 0 100%, from(#fefefe), to(#efefef));
    }

    .btn_2{
        display:block;
        width:150px;
        margin:auto;
        line-height:35px;
        text-align:center;
        padding:0 5px;
        color:#fff;
        background:-webkit-gradient(linear, 0 0, 0 100%, from(#F03C03), to(#F03C03));
        border:1px solid #F03C03;
        border-radius:20px;
        font-size:15px;
    }

    .table_book{
        width:100%;
    }

    .table_book td{
        padding:5px 3px;
        white-space:nowrap;
    }

    .table_book input[type="text"], .table_book input[type="tel"], .table_book textarea{
        padding:8px;
        background:#fff;
        border-radius:5px;
        border:1px solid #dedede;
        outline:none;
        resize:none;
        width:100%;
        -webkit-box-sizing:border-box;
        box-shadow:none;

        /*width: 100%;*/
        /*-webkit-appearance: none;*/
        /*border-color: #dbdbdb;*/
        /*border-radius: 4px;*/
        /*padding: 8px 5px;*/
        /*font-size: 14px;*/
        /*line-height: 26px;*/
        /*border: 0*/
    }

    .table_book select{
        display:inline-block;
        /*-webkit-appearance:button;*/
        border:1px solid #dedede;
        border-radius:5px;
        padding:2px;
        outline:none;
        /*line-height: 28px;*/
        height: 28px;
        font-size: 16px;
        letter-spacing: normal;
        word-spacing: normal;
        width: 100%;

        font-family: Helvetica;
        align-items: center;
        white-space: pre;
        -webkit-rtl-ordering: logical;
        cursor: default;
    }

    .group_radio{
        border:1px solid #dedede;
        border-radius:18px;
        display:inline-block;
        overflow:hidden;
    }

    .group_radio input[type="radio"]{
        -webkit-appearance:button;
        display:inline-block;
        width:50px;
        height:35px;
        border-radius:0;
        border:0;
        background:-webkit-gradient(linear, 0 0, 0 100%, from(#fff), to(#f2f2f2));
    }
    .group_radio span{
        position:relative;
        display:inline-block;
        height:35px;
        width:50px;
        float:left;
        border-right:1px solid #ccc;
    }
    .group_radio span:last-of-type{
        border:0;
    }
    .group_radio span:first-child input{
        border-radius:16px 0 0 16px;
    }
    .group_radio span:last-child input{
        border-radius:0 15px 15px 0;
    }
    .group_radio span label{
        content:attr(data-title);
        position:absolute;
        top:0;
        left:0;
        z-index:10;
        color:red;
        display:inline-block;
        width:50px;
        line-height:35px;
        text-align:center;
        overflow:hidden;
        color:#666666;
        pointer-events:none;
    }
    .group_radio input[type="radio"]:checked{
        background:-webkit-gradient(linear, 0 0, 0 100%, from(#F03C03), to(#F03C03));
    }
    .group_radio input[type="radio"]:checked+label{
        color:#fff;
    }
    .group_checkbox{
        overflow:hidden;
        border-radius:16px;
        position: relative;
    }
    .group_checkbox input[type="checkbox"]{
        -webkit-appearance:button;
        padding:8px;
        width:70px;
        border-radius:16px;
        border:1px solid #dedede;
        display:inline-block;
        height:35px;
        background:url(../addons/weisrc_dish/template/images/btn1.png#1), -webkit-gradient(linear, 0 0, 0 100%, from(#F03C03), to(#F03C03));
        background-size:auto 32px, auto auto;
        background-position:right center;
        background-repeat: no-repeat;
    }
    .group_checkbox input[type="checkbox"]:checked{
        background-position:0px center;
    }
    .btn_myorder {
        padding: 5px 8px; border: 0; border-radius: 2px; cursor: pointer; background-color: #F03C03; color: #fff;
    }
</style>
    <style>
        .ddb-nav-header {
            opacity: 0.95;
            position: fixed;
            top: 0;
            width: 100%;
            height: 45px;
            line-height: 44px;
            z-index: 1999;
            border-bottom: 1px solid #eeeeee;
            background-color: white;
            color: #555555
        }

        .ddb-nav-header a {
            color: #555555
        }

        .ddb-nav-header .header-title {
            width: 50%;
            white-space: nowrap;
            text-overflow: ellipsis;
            text-align: center;
            overflow: hidden;
            font-size: 17px;
            font-weight: bold;
            display: inline-block;
            margin-left: 25%
        }

        .ddb-nav-header .nav-left-item, .ddb-nav-header .nav-right-item {
            display: inline-block;
            height: 100%
        }

        .ddb-nav-header .nav-left-item i, .ddb-nav-header .nav-right-item i {
            width: 20px;
            font-size: 18px;
            line-height: inherit;
        }

        .ddb-nav-header .nav-left-item {
            width: 25%;
            float: left;
            padding-left: 10px
        }

        .ddb-nav-header .nav-right-item {
            width: 10%;
            float: right;
            text-align: right
        }

        .ddb-nav-header .nav-right-item .operation-button {
            font-size: 16px;
            text-align: right;
            color: #555555;
            padding-right: 10px;
            font-weight: bold
        }

        .ddb-nav-header .nav-left-item ~ .header-title {
            margin-left: 0
        }

        .ddb-nav-header.label-red {
            border-bottom: 0
        }
        .notification-section {
            /*background-color: white;*/
            background-color: #fff9df;
            padding: 0 10px;
            border-bottom: 0px solid #dbdbdb
        }

        .notification-section .notice {
            border-top: 1px dotted #eeeeee;
            padding: 8px 0;
            padding-left: 30px;
            line-height: 20px
        }

        .notification-section .notice i {
            display: block;
            float: left;
            margin-left: -30px;
            text-align: center;
            width: 30px
        }

        #page_intelOrder header, #page_intelOrder section {
            margin: 0px;
        }
    </style>
</head>
<body id="page_intelOrder" class="myOrderCon">
<div class="ddb-nav-header ng-scope">
    <a class="nav-left-item" href="javascript:void(0)" onclick="history.go(-1)"><i class="fa fa-angle-left"></i></a>
    <div class="header-title ng-binding">购物车</div>
    <a class="nav-right-item" href="#"></a>
</div>
<div class="center">
    <!--<header>-->
        <!--<span class="pCount">-->
            <?php  if($store['is_delivery'] == 1) { ?>
            <?php  if(!empty($store['sendingprice'])) { ?>
            <!--本店外卖<font color="red"><?php  echo $store['sendingprice'];?></font>元起送-->
            <?php  } else { ?>
            <!--美味即将送到-->
            <?php  } ?>
            <?php  } else { ?>
            <!--请叫服务员下单-->
            <?php  } ?>
        <!--</span>-->
        <!--<label><i>商品共计：</i><b class="duiqi" id="total">0</b><b class="duiqi">元</b></label>-->
    <!--</header>-->
    <section style="margin-bottom: 10px;margin-top: 40px;">
        <article style="overflow: hidden;">
            <a href="<?php  echo $this->createMobileurl('order', array(), true)?>" class="btn_add btn_myorder" style="color:#fff;">我的订单</a>
            <span style="float: right;"><?php  echo $my_order_total;?></span>
        </article>
    </section>
    <?php  if($flag != true) { ?>
    <section style="margin-bottom: 10px;">
        <article>
            <h2>商品列表
                <button class="btn_add emptyIt" id="clearBtn">清空</button>
                <button class="btn_add" onclick="location.href='<?php  echo $this->createMobileUrl('waplist', array('storeid' => $storeid, 'mode' => $mode, 'tablesid' => $tablesid), true)?>'">+增加</button>
            </h2>
            <ul class="myorder" id="myorder">
                <?php  if(is_array($cart)) { foreach($cart as $item) { ?>
                <li>
                    <span class="dishName"><?php  echo $item['title'];?></span>
                    <?php  if($item['isspecial']==1) { ?>
                    <i><?php  echo $item['productprice'];?>元/<?php  echo $item['unitname'];?></i>
                    <?php  } else { ?>
                    <i><?php  echo $item['marketprice'];?>元/<?php  echo $item['unitname'];?><span class="yuanjia"><?php  echo $item['productprice'];?>元/<?php  echo $item['unitname'];?></span></i>
                    <?php  } ?>
                    <section class="bbox" dishid="<?php  echo $item['id'];?>" dishname="<?php  echo $item['title'];?>"> <input class="btn-reduce" type="button" value="-"><input class="numBox" name="numBox" type="text" value="<?php  echo $item['total'];?>" price="<?php  if($item['isspecial']==1) { ?><?php  echo $item['productprice'];?><?php  } else { ?><?php  echo $item['marketprice'];?><?php  } ?>" disabled="disabled"><input type="button" class="btn-plus" value="+"></section>
                </li>
                <?php  } } ?>
            </ul>
        </article>
    </section>
    <?php  } ?>
    <?php  if($flag == true) { ?>
    <section style="margin-bottom: 20px;">
        <article>
            <form id="form2" target="hide">
                <input type="hidden" name="id" value="">
                <table class="table_book">
                    <tbody>
                    <tr>
                        <td width="80px" style="width: 80px;">手机号码：</td>
                        <td colspan="2">
                            <input type="tel" name="entry_mobile" id="entry_mobile" placeholder="请输入手机号码" maxlength="15" class="input" style="width: 200px;"/>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 80px;">验证码：</td>
                        <td colspan="2">
                            <input type="number" placeholder="验证码：" name="entry_checkcode" id="entry_checkcode" maxlength="10" class="input" style="width: 100px;"/>
                            <input type="button" onclick="getReceiveMemberCardVCode(this, event,'form2', 'entry_mobile',2);" class="button vcode" value="获取验证码" style="width: 100px;height: 40px"/>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <footer>
                    <input type="button" value="提交" class="btn_2" id="submit_form2">
                </footer>
            </form>
        </article>
    </section>
    <style>
        .input{
            display: inline-block;
            padding:11px 5px;
            border:1px solid #dddddd;
            border-radius:3px;
            width:100%;
            background:#f3f3f3;
            -webkit-box-sizing:border-box;
            color:#606366;
        }
        .button{
            display: inline-block;
            line-height: 40px;
            font-size:12px;
            text-align: center;
            color:#ffffff;
            border:1px solid #c3cfd0;
            border-radius: 1px;
            background:#56c6d6;
            border: 0;
        }
        .button.vcode{
            width:80px;
        }
        .button.vcode[disabled]{
            background:#cccccc;
            font-size: 12px;
        }
    </style>
    <?php  } else { ?>
    <input type="hidden" id="mode" value="<?php  echo $mode;?>" name="mode">
    <input type="hidden" id="tables" value="<?php  echo $tablesid;?>" name="tables">
    <section style="margin-bottom: 20px;">
        <article>
            <?php  if($mode == 1) { ?>
            <h2 style="color:#f00;"><?php  echo $table_title;?></h2>
            <?php  } ?>
            <div id="form_dish" target="hide" <?php  if($mode == 1) { ?>style="border-top: 1px #e7eaeb solid;margin-top: 10px;"<?php  } ?>>
                <table class="table_book">
                    <tbody>
                    <?php  if($mode == 2) { ?>
                    <?php  if($store['delivery_within_days'] != 0) { ?>
                    <tr>
                        <td style="width: 80px;">配送日期：</td>
                        <td colspan="2">
                            <select name="meal_date" id="meal_date" onclick="changeMealDate(this)">
                                <?php  echo $select_mealdate;?>
                            </select>
                        </td>
                    </tr>
                    <?php  } else { ?>
                    <input type="hidden" id="meal_date" value="" name="meal_date">
                    <?php  } ?>
                    <tr>
                        <td style="width: 80px;">配送时间：</td>
                        <td colspan="2">
                            <select name="meal_time" id="meal_time">
                                <?php  echo $select_mealtime;?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 80px;">配送费用：</td>
                        <td colspan="2">
                            <?php  echo $store['dispatchprice'];?> <?php  if($store['freeprice']!=0.00) { ?>(<font color="#f00">消费满<?php  echo $store['freeprice'];?>免配送费用</font>)<?php  } ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 80px;">配送范围：</td>
                        <td colspan="2">
                            <?php  if($delivery_radius>0) { ?><?php  echo $delivery_radius;?>公里<?php  } ?>
                        </td>
                    </tr>
                    <?php  } ?>
                    <tr>
                        <td width="80px" style="width: 80px;">手机号码：</td>
                        <td colspan="2">
                            <input type="tel" id="tel" name="tel" value="<?php  if(!empty($user['mobile'])) { ?><?php  echo $user['mobile'];?><?php  } ?>" maxlength="13" style="width: 200px;" placeholder="请输入手机号码"></td>
                    </tr>
                    <tr>
                        <td style="width: 80px;">用户姓名：</td>
                        <td style="width: 50%;">
                            <input type="text" name="guest_name" id="name" value="<?php  if(!empty($user['username'])) { ?><?php  echo $user['username'];?><?php  } ?>" maxlength="10" style="width: 100px;" placeholder="请输入您的姓名">
                        </td>
                        <td>
                            <div class="group_checkbox" style="width: 70px; display: inline;">
                                <input type="checkbox" value="1" name="sex" checked="checked">
                            </div>
                        </td>
                    </tr>
                    <?php  if($mode == 2) { ?>
                    <tr>
                        <td width="80px" style="width: 80px;">地址：</td>
                        <td colspan="2">
                            <input type="text" id="address" name="address" value="<?php  if(!empty($order['address'])) { ?><?php  echo $order['address'];?><?php  } ?>" style="width: 200px;" placeholder="请输入您的联系地址！">
                        </td>
                    </tr>
                    <?php  } ?>
                    <?php  if($mode == 1) { ?>
                    <tr>
                        <td style="width: 80px;">用餐人数：</td>
                        <td colspan="2">
                            <input type="tel" id="counts" name="counts" value="" maxlength="3" style="width: 100px;" placeholder="请填写用餐人数">
                        </td>
                    </tr>
                    <?php  } ?>
                    <tr>
                        <td style="width: 80px; vertical-align: top; line-height: 25px;">备注说明：</td>
                        <td colspan="2">
                            <textarea name="remark" id="remark" style="height: 60px;" maxlength="100" placeholder="请输入备注(可不填)"></textarea>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </article>
    </section>
    <style>
        .header { padding: 5px 0; display: block; position: fixed; width: 100%; z-index: 3; bottom: 0px; color: #F03C03; background-color:#fff; line-height: 32px; font-size: 14px;border-top: 1px solid #E2E2E2; }
        .header .left { float: left;margin-left: 10px }
        .header .right { float: right;margin-right: 10px }
        .footermenu {
            position: relative;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 900;
            -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
        }
        .footermenu ul {
            position: fixed;
            z-index: 900;
            bottom: 0;
            left: 0;
            right: 0;
            margin: auto;
            display: block;
            width: 100%;
            height: 48px;
            display: -webkit-box;
            display: box;
            -webkit-box-orient: horizontal;
            background-color: #282f35;
            background: -webkit-linear-gradient(top,#282f35,#282f35);

            background:#5ac5d4;
            border-top: 1px solid #5ac5d4;
            /*border-top: 1px solid #404a54;*/
        }
        .footermenu ul li {
            width: auto!important;
            height: 100%;
            position: static!important;
            margin: 0;
            border-radius: 0!important;
            -webkit-box-sizing: border-box;
            box-sizing: border-box;
            -webkit-box-flex: 1;
            box-flex: 1;
            -webkit-box-sizing: border-box;
            box-shadow: none!important;
            background: none;
        }
        .footermenu ul li img {
            vertical-align: bottom;
        }
        .footermenu ul li a {
            color: #fff;
            text-align: center;
            display: block;
            text-decoration: none;
            padding-top: 2px;
            font-size: 12px;
            position: relative;
            height: 46px;
            text-shadow: 0 1px rgba(0, 0, 0, 0.2);
            line-height: 20px;
        }
        .footermenu ul li a.active {
            /*background: -webkit-linear-gradient(top,#404a54,#252c34);*/
            background: -webkit-linear-gradient(top,#5ac5d4,#5ac5d4);
            box-shadow: 0 -10px 25px 0px rgba(0,0,0,0.3) inset;
        }
        .xhlbtn {
            display: block;
            text-align: center;
            background-color: #F03C03;
            /*background-color: #fdb338;*/
            /*background-color: #5ac5d4;*/
            padding: 0 15px;
            border-radius: 20px;
            color: #fff;
            font-weight: bold;
        }
        img {
            width: 24px;
            height: 24px;
            vertical-align: middle;
        }
        .right .disable{
            background-color:  #dbdbdb;
        }
    </style>
    <div class="header">
        <input type="hidden" id="totalprice" value="<?php  echo $totalprice;?>" name="totalprice">
        <input type="hidden" id="totalcount" value="<?php  echo $totalcount;?>" name="totalcount">
        <input type="hidden" id="limitprice" value="<?php  echo $limitprice;?>" name="limitprice">
        <input type="hidden" id="over_radius" value="<?php  echo $over_radius;?>" name="over_radius">

        <input type="hidden" id="btnstatus" value="0" name="btnstatus">
        <div class="left">总计：￥<span id="totalpriceshow"><?php  echo $totalprice;?></span></span>元</div>
        <div class="right"><a id="btnselect" class="xhlbtn disable" href="javascript:void(0)">立即下单</a></div>
    </div>
    <?php  } ?>
    <div style="padding-top: 20px;"></div>
</div>
<?php  if($flag != true) { ?>
<script>
    function changeMealDate(obj) {
        var time1 = '<?php  echo $select_mealtime;?>';
        var time2 = '<?php  echo $select_mealtime2;?>';
        var curdate = "<?php  echo $cur_date;?>";
        if (obj.value == curdate) {
            $("#meal_time").html(time1);
        } else {
            $("#meal_time").html(time2);
        }
    }

    function changeBtnSelect() {
        var limitprice = parseFloat(_q("#limitprice").value);
        var totalprice = parseFloat(_q("#totalprice").value);
        if (limitprice > 0) {
            if (totalprice > limitprice) {
                _removeClass(_q("#btnselect"),'disable');
                _q("#btnstatus").value = '1';
            } else {
                _addClass(_q("#btnselect"),'disable');
                _q("#btnstatus").value = '0';
            }
        } else {
            if (totalprice > 0) {
                _q("#btnstatus").value = '1';
                _removeClass(_q("#btnselect"),'disable');
            } else {
                _q("#btnstatus").value = '0';
                _addClass(_q("#btnselect"),'disable');
            }
        }
    }
</script>
<?php  } ?>
<script>var pageName = 'menuFilled';</script>
<script type="text/javascript">
var reduce = _qAll('.btn-reduce');
var plus = _qAll('.btn-plus');
var share = _qAll('.shareBtn');
//金额累加操作
"<?php  if($flag != true) { ?>"
function tototal(){
    var total = 0;
    var nums = _qAll('.numBox');
    for( var j = 0; j < nums.length; j++){
        total = total + nums[j].value * nums[j].getAttribute('price');
    }
    endTotal = parseFloat(total).toFixed(2) * 100/100;
    // endTotal = endTotal == parseInt(endTotal) ? parseInt(endTotal) : endTotal;
    _q('#totalprice').value = endTotal;
    _q("#totalpriceshow").innerHTML = endTotal;

    changeBtnSelect();
    return endTotal;
}
tototal();//初始化金额

function doSelectBtn(){
    var btn = _qAll("article ul li .bbox");
        var btnIndex = 0,btnLength = btn.length;
    for(btnIndex;btnIndex<btnLength;btnIndex++){
        var countNumText=parseInt(btn[btnIndex].children[1].value),
            btnAdd=btn[btnIndex].children[2],
            btnMin=btn[btnIndex].children[0];
        var iTimeout,iInterval,originalNum,
            beforeRemoveDish = false;
        btnAdd.addEventListener(_movestartEvt,function(){
            var _self = this;
            originalNum = parseInt(_self.parentNode.children[1].value, 10);
            countNumText =  originalNum +1;
            _self.parentNode.children[1].value = countNumText;
            iTimeout = setTimeout(function(){
                iInterval = setInterval(function(){
                    countNumText++;
                    _self.parentNode.children[1].value = countNumText;
                },100)
            },1000)
        })
        btnAdd.addEventListener(_moveendEvt,function(){
            //alert(countNumText)
            //_doAjax()...
            clearTimeout(iTimeout);
            clearInterval(iInterval);
            tototal();
            var _self = this;
            var countNumText =  parseInt(_self.parentNode.children[1].value, 10);
            var dishid = _self.parentNode.getAttribute('dishid');
            ajaxDishReset(dishid, countNumText, function(){}, function() {
                _self.parentNode.children[1].value = originalNum;
                tototal();
            });
            // countNumText = 0;
        })

        btnMin.addEventListener(_movestartEvt,function(){
            var _self = this;
            originalNum = parseInt(_self.parentNode.children[1].value, 10);
            countNumText =  originalNum -1;
            if(countNumText <= 0){
                beforeRemoveDish = true;
            }else{
                _self.parentNode.children[1].value = countNumText;
                iTimeout = setTimeout(function(){
                    iInterval = setInterval(function(){
                        if(countNumText<=0){
                            beforeRemoveDish = true;
                            _self.parentNode.children[1].value = originalNum;
                            clearInterval(iInterval);
                            return;
                        }
                        countNumText--;
                        _self.parentNode.children[1].value = countNumText;
                    },100)
                },1000)
            }
        })

        btnMin.addEventListener(_moveendEvt,function(){
            clearTimeout(iTimeout);
            clearInterval(iInterval);
            _self = this;

            var dishid = _self.parentNode.getAttribute('dishid');
            var dishName = _self.parentNode.getAttribute('dishName');
            var countNumText =  parseInt(_self.parentNode.children[1].value, 10);

            if(beforeRemoveDish){
                setTimeout(function(){
                    MDialog.confirm(
                        '', '是否删除' + dishName +'？', null,
                        '确定', function(){
                            ajaxDishRemove(dishid, function(){
                                var li = _self.parentNode.parentNode;
                                li.parentNode.removeChild(li);
                                var total = tototal();
                                // 没有数据后刷新页面
                                if (total == 0) {
                                    location.reload();
                                }
                            }, function() {
                                _self.parentNode.children[1].value = originalNum;
                                tototal();
                            });
                        }, null,
                        '取消', null, null,null, true, true
                    );
                },500)
                beforeRemoveDish = false;
            } else {
                tototal();
                ajaxDishReset(dishid, countNumText, function(){}, function() {
                     _self.parentNode.children[1].value = originalNum;
                    tototal();
                });
            }
        })
    } // for

    //更新分类菜品数量
    function ajaxDishReset(dishid, o2uNum, successCallback, errorCallback) {
        var params = {
            'dishid' : dishid,
            'o2uNum' : o2uNum
        };
        var url = "<?php  echo $this->createMobileUrl('UpdateDishNumOfCategory', array('storeid' => $storeid, 'from_user' => $from_user), true)?>";
        _doAjax(url, 'POST', params, function(ret) {
            if (ret['message']['code'] != 0) {
                errorCallback();
                alert(ret['message']['msg']);
                return;
            } else {
                successCallback();
            }
            successCallback();
        });
    } // ajaxDishReset

    function ajaxDishRemove(dishid, successCallback, errorCallback) {
        var params = {
            'dishid' : dishid,
            'action' : 'remove'
        };

        var url = "<?php  echo $this->createMobileUrl('RemoveDishNumOfCategory', array('storeid' => $storeid, 'from_user' => $from_user), true)?>";

        _doAjax(url, 'POST', params, function(ret) {
            if (ret['message']['code']!= 0) {
                errorCallback();
                alert(ret['message']['msg']);
                return;
            } else {
                successCallback();
            }
        });
    } // ajaxDishRemove
} // doSelectBtn
"<?php  } ?>"
_onPageLoaded(function(){
    var reduce = _qAll('.btn-reduce');
    var plus = _qAll('.btn-plus');
    "<?php  if($flag != true) { ?>"
    tototal();//初始化金额
    doSelectBtn();
    "<?php  } ?>"

    var url = "<?php  echo $this->createMobileUrl('clearmenu', array('storeid' => $storeid, 'from_user' => $from_user), true)?>";
    "<?php  if($flag != true) { ?>"
    _q('#clearBtn').onclick = function() {
        MDialog.confirm(
            '', '是否清空菜单？', null,
            '确定', function(){
                window.location.href = url;
            }, null,
            '取消', null, null,
            null, true, true
        );
    };
    "<?php  } ?>"

    $("#btnselect").click(function () {
        var bool = checkItem();
        if (bool) {
            MDialog.confirm(
                '', '确认提交吗？', null,
                '确定', function(){
                    postmain();
                }, null, '取消', null, null,null, true, true
            );
        }
    });

    $("#submit_form2").click(function () {
        var mobile = $("#entry_mobile").val();
        var checkcode = $("#entry_checkcode").val();
        if (mobile == "") {
            alert('请输入手机号码!');
            return false;
        }
        if (checkcode == "") {
            alert('请输入验证码!');
            return false;
        }
        MDialog.confirm(
            '', '确认提交吗？', null,
            '确定', function(){
                postmain2();
            }, null, '取消', null, null,null, true, true
        );
    });
});

function checkItem() {
    var ordertype = $("#mode").val();
    var meal_time = $("#meal_time").val();

    if ($("#tel").val() == "" || $("#tel").val() == "(必填*)请输入您的手机号码") { alert("请输入您的电话号码！"); return false; }
//    if (!/^1[3|4|5|8][0-9]\d{8}$/.test($("#tel").val())) { alert("请输入正确的电话号码！"); return false; }
    if ($("#name").val() == "" || $("#name").val() == "(必填*)请输入您的真实姓名") { alert("请输入您的真实姓名！"); return false; }
    totalprice = parseFloat(_q("#totalprice").value);

    if (totalprice <= 0) { alert("金额为0，请选择菜品！"); return false; }

    if (ordertype == 1) { //店内
        if ($("#counts").val() == "") { alert("请输入用餐人数！"); return false; }
        if (!new RegExp("^[0-9]*$").test($("#counts").val())) { alert("用餐人数只能为数字！"); return false; }
        if ($("#tables").val() == "") { alert("请输入桌号！"); return false; }
    } else if (ordertype == 2){//外卖
        if ($("#address").val() == "" || $("#address").val() == "(必填*)请输入您的联系地址！") { alert("请输入您的联系地址！"); return false; }
        if (meal_time == '休息中') {
            alert('未在配送时间！');
            return false;
        }
        var over_radius = $("#over_radius").val();
        if (over_radius == 1) {
            alert('超出配送范围，不允许下单！');
            return false;
        }
    } else if (ordertype == 4){//快餐

    }
    return true;
}

function postmain() {
    var status = _q("#btnstatus").value;
    var ordertype = $("#mode").val();
    var mealtime = $("#meal_date").val() + ' ' + $("#meal_time").val();
    if ($("#meal_date").val() == undefined) {
        if ($("#meal_time").val() == undefined) {
            mealtime = '';
        } else {
            mealtime = $("#meal_time").val();
        }
    }

    if (status == 0) {
        return false;
    }

    $("#btnselect").hide();
    if (true) {
        var data = "[";
        var myfoodlist = $("#myorder li");
        if (myfoodlist.length == 0) { return; }
        for (var i = 0; i < myfoodlist.length; i++) {
            data += "{"; data += "\"sid\":\"" + myfoodlist.eq(i).attr("name") + "\",";
            data += "\"counts\":\"" + myfoodlist.eq(i).find(".numBox").val() + "\""; data += "},";
        }
        data = data.substring(0, data.length - 1); data += "]";
        var url = "<?php  echo $this->createMobileUrl('addtoorder', array('storeid' => $storeid, 'from_user' => $from_user), true)?>";

        var address = $("#address").val();
        var totalprice = parseFloat(_q("#totalprice").value);

        $.ajax({
            url: url, type: "post", dataType: "json", timeout: "10000",
            data: {
                "type": "add", "total": totalprice, "ordertype":ordertype,"tables": $("#tables").val(),
                "guest_name": $("#name").val(), "tel": $("#tel").val(), "sex": $("input:checkbox[name='sex']").attr("checked") == "checked" ? "1" : "0",
                "meal_time": mealtime,
                "counts": $("#counts").val(),
                "seat_type": $("input:radio[name='seat_type']:checked").val(), "remark": $("#remark").val(), "data": data, "carports": $("#carports").val(), "address":address
            },
            success: function (data) {
                if (data.message['code'] != 0) {
                    var url = "<?php  echo $this->createMobileUrl('pay', array(), true)?>"+"&orderid="+data.message['orderid'];
                    location.href = url;
                } else {
                    alert(data.message['msg']);
                    //'网络不稳定，请重新尝试!'
                }
                $("#btnselect").show();
            },error: function () {
                alert("订单提交失败！");
            }
        });
    } else {
        $("#btnselect").show();
    }
}

function postmain2() {
    $("#submit_form2").hide();
    if (true) {
        var url = "<?php  echo $this->createMobileUrl('validatecheckcode', array('from_user' => $from_user), true)?>";

        var mobile = $("#entry_mobile").val();
        var checkcode = $("#entry_checkcode").val();
        if (mobile == "") {
            alert('请输入手机号码!');
            return false;
        }
        if (checkcode == "") {
            alert('请输入验证码!');
            return false;
        }

        $.ajax({
            url: url, type: "post", dataType: "json", timeout: "10000",
            data: {
                "mobile": mobile,
                "checkcode": checkcode
            },
            success: function (data) {
                if (data.status == 1) {
                    window.location.reload();
                } else {
                    alert(data.msg);
                }
                $("#submit_form2").show();
            },error: function () {
                alert("网络不稳定，请重新尝试!");
            }
        });
    } else {
        $("#submit_form2").show();
    }
}
</script>
<script type="text/javascript">
    var intervalId,buttonObj;
    //发送下一条短信需要间隔的秒数
    var seconds = 60;
    function getReceiveMemberCardVCode(clickObj, evt, formId, teleName, ch_type){
        var form = document.getElementById(formId);
        var url = "<?php  echo $this->createMobileurl('GetCheckCode', array('from_user' => $from_user), true);?>";
        var storeid = "<?php  echo $storeid;?>";
        var mobile = $.trim(form[teleName].value);
        var req = {
            mobile: mobile,
            storeid: storeid
        }
        if(!req.mobile){
            alert("请输入手机号");return;
        }
        clickObj.setAttribute("disabled", "disabled");
        clickObj.value = "正在发送，请稍候...";

        $.ajax({
            url: url,
            type:"post",
            data:req,
            dataType:"JSON",
            success: function(data) {
                if (data.status == 1) {
                    clickObj.value = '发送成功';
                    buttonObj = clickObj;
                    intervalId = setInterval("ticker()",1000);
                } else {
                    clickObj.value = '发送失败';
                    buttonObj = clickObj;
                    intervalId = setInterval("ticker()",1000);
                    alert('调试:' + data.msg);
                    return false;
                }
            },
            error: function(data){
                alert('网络异常哦!');
                return false;
            }
        });
    }

    function ticker(){
        seconds --;
        if(seconds > 55){
            //提示消息显示5秒钟
        }else if(seconds>0){
            buttonObj.value = seconds+"秒后可重新获取";
        }else{
            clearInterval(intervalId);
            buttonObj.removeAttribute("disabled");
            buttonObj.value = "获取验证码";
            seconds = 60;
            buttonObj = null;
        }
    }
</script>
</body>
</html>