<?php
/**
 * 域顺微点餐
 *
 */
defined('IN_IA') or exit('Access Denied');
include "model.php";
include "plugin/feyin/HttpClient.class.php";
include "templateMessage.php";
define(EARTH_RADIUS, 6371); //地球半径，平均半径为6371km
define('RES', '../addons/weisrc_dish/template/');
define('CUR_MOBILE_DIR', 'dish/');
define('FEYIN_HOST', 'my.feyin.net');
define('FEYIN_PORT', 80);

class weisrc_dishModuleSite extends WeModuleSite
{
    //模块标识
    public $modulename = 'weisrc_dish';
    public $cur_tpl = 'style1';

    public $member_code = '';
    public $feyin_key = '';
    public $device_no = '';

    public $msg_status_success = 1;
    public $msg_status_bad = 0;
    public $_debug = '1'; //default:0
    public $_weixin = '1'; //default:1

    public $_appid = '';
    public $_appsecret = '';
    public $_accountlevel = '';
    public $_account = '';

    public $_weid = '';
    public $_fromuser = '';
    public $_nickname = '';
    public $_headimgurl = '';

    public $_auth2_openid = '';
    public $_auth2_nickname = '';
    public $_auth2_headimgurl = '';
    public $_lat = '';
    public $_lng = '';
    public $table_area = 'weisrc_dish_area';
    public $table_blacklist = 'weisrc_dish_blacklist';
    public $table_cart = 'weisrc_dish_cart';
    public $table_category = 'weisrc_dish_category';
    public $table_email_setting = 'weisrc_dish_email_setting';
    public $table_goods = 'weisrc_dish_goods';
    public $table_intelligent = 'weisrc_dish_intelligent';
    public $table_nave = 'weisrc_dish_nave';
    public $table_order = 'weisrc_dish_order';
    public $table_order_goods = 'weisrc_dish_order_goods';
    public $table_print_order = 'weisrc_dish_print_order';
    public $table_print_setting = 'weisrc_dish_print_setting';
    public $table_reply = 'weisrc_dish_reply';
    public $table_setting = 'weisrc_dish_setting';
    public $table_sms_checkcode = 'weisrc_dish_sms_checkcode';
    public $table_sms_setting = 'weisrc_dish_sms_setting';
    public $table_store_setting = 'weisrc_dish_store_setting';
    public $table_mealtime = 'weisrc_dish_mealtime';
    public $table_stores = 'weisrc_dish_stores';
    public $table_collection = 'weisrc_dish_collection';
    public $table_type = 'weisrc_dish_type';
    public $table_ad = 'weisrc_dish_ad';
    public $table_template = "weisrc_dish_template";
    public $table_account = "weisrc_dish_account";
    public $table_queue_setting = "weisrc_dish_queue_setting";
    public $table_queue_order = "weisrc_dish_queue_order";
    public $table_tablezones = "weisrc_dish_tablezones";
    public $table_tables = "weisrc_dish_tables";
    public $table_tables_order = "weisrc_dish_tables_order";
    public $table_reservation = "weisrc_dish_reservation";
    public $table_fans = "weisrc_dish_fans";

    function __construct()
    {
        global $_W, $_GPC;

        $this->_fromuser = $_W['fans']['from_user']; //debug
        $this->_weid = $_W['uniacid'];
        $account = $_W['account'];

        $this->_auth2_openid = 'auth2_openid_' . $_W['uniacid'];
        $this->_auth2_nickname = 'auth2_nickname_' . $_W['uniacid'];
        $this->_auth2_headimgurl = 'auth2_headimgurl_' . $_W['uniacid'];

        $this->_lat = 'lat_' . $this->_weid;
        $this->_lng = 'lng_' . $this->_weid;

        $this->_appid = '';
        $this->_appsecret = '';
        $this->_accountlevel = $account['level']; //是否为高级号

        if (isset($_COOKIE[$this->_auth2_openid])) {
            $this->_fromuser = $_COOKIE[$this->_auth2_openid];
        }

        if ($this->_accountlevel < 4) {
            $setting = uni_setting($this->_weid);
            $oauth = $setting['oauth'];
            if (!empty($oauth) && !empty($oauth['account'])) {
                $this->_account = account_fetch($oauth['account']);
                $this->_appid = $this->_account['key'];
                $this->_appsecret = $this->_account['secret'];
            }
        } else {
            $this->_appid = $_W['account']['key'];
            $this->_appsecret = $_W['account']['secret'];
        }

        $template = pdo_fetch("SELECT * FROM " . tablename($this->table_template) . " WHERE weid = :weid", array(':weid' => $this->_weid));
        if (!empty($template)) {
            $this->cur_tpl = $template['template_name'];
        }
        $this->resetn();
    }

    //首页
    public function doMobileWapIndex()
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        $from_user = $this->_fromuser;

        $method = 'wapindex'; //method
        $authurl = $_W['siteroot'] . 'app/' . $this->createMobileUrl($method, array(), true) . '&authkey=1';
        $url = $_W['siteroot'] . 'app/' . $this->createMobileUrl($method, array(), true);
        if (isset($_COOKIE[$this->_auth2_openid])) {
            $from_user = $_COOKIE[$this->_auth2_openid];
            $nickname = $_COOKIE[$this->_auth2_nickname];
            $headimgurl = $_COOKIE[$this->_auth2_headimgurl];
        } else {
            if (isset($_GPC['code'])) {
                $userinfo = $this->oauth2($authurl);
                if (!empty($userinfo)) {
                    $from_user = $userinfo["openid"];
                    $nickname = $userinfo["nickname"];
                    $headimgurl = $userinfo["headimgurl"];
                } else {
                    message('授权失败!');
                }
            } else {
                if (!empty($this->_appsecret)) {
                    $this->getCode($url);
                }
            }
        }

        //幻灯片
        $slide = pdo_fetchall("SELECT * FROM " . tablename($this->table_ad) . " WHERE uniacid = :uniacid AND position=1 AND status=1 AND :time > starttime AND :time < endtime  ORDER BY displayorder DESC,id DESC LIMIT 6", array(':uniacid' => $this->_weid, ':time' => TIMESTAMP));

        if (empty($slide)) {
            $jump_url = $this->createMobileUrl('waprestlist', array(), true);
            header("location:$jump_url");
        }

        $setting = $this->getSetting();
        $title = empty($setting) ? "微餐厅" : $setting['title'];
        if ($setting['mode'] == 1) {
            $jump_url = $this->createMobileUrl('detail', array('id' => $setting['storeid']), true);
        } else {
            $jump_url = $this->createMobileUrl('waprestlist', array(), true);
        }

        include $this->template($this->cur_tpl . '/index');
    }

    public function getSetting()
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        $setting = pdo_fetch("SELECT * FROM " . tablename($this->table_setting) . " where weid = :weid LIMIT 1", array(':weid' => $weid));
        return $setting;
    }

    //商品列表
    public function doMobileWapList()
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        $from_user = $this->_fromuser;
        $tablesid = intval($_GPC['tablesid']);

        $title = '全部商品';
        $mode = intval($_GPC['mode']);
        $storeid = intval($_GPC['storeid']);
        if ($storeid == 0) {
            $storeid = $this->getStoreID();
        }
        if (empty($storeid)) {
            message('请先选择门店', $this->createMobileUrl('waprestlist'));
        }

        $method = 'waplist'; //method

        if ($mode == 1) {
            $authurl = $_W['siteroot'] . 'app/' . $this->createMobileUrl($method, array('storeid' => $storeid, 'mode' => $mode, 'tablesid' => $tablesid), true) . '&authkey=1';
            $url = $_W['siteroot'] . 'app/' . $this->createMobileUrl($method, array('storeid' => $storeid, 'mode' => $mode, 'tablesid' => $tablesid), true);
        } else {
            $authurl = $_W['siteroot'] . 'app/' . $this->createMobileUrl($method, array('storeid' => $storeid, 'mode' => $mode), true) . '&authkey=1';
            $url = $_W['siteroot'] . 'app/' . $this->createMobileUrl($method, array('storeid' => $storeid, 'mode' => $mode), true);
        }
        if (isset($_COOKIE[$this->_auth2_openid])) {
            $from_user = $_COOKIE[$this->_auth2_openid];
            $nickname = $_COOKIE[$this->_auth2_nickname];
            $headimgurl = $_COOKIE[$this->_auth2_headimgurl];
        } else {
            if (isset($_GPC['code'])) {
                $userinfo = $this->oauth2($authurl);
                if (!empty($userinfo)) {
                    $from_user = $userinfo["openid"];
                    $nickname = $userinfo["nickname"];
                    $headimgurl = $userinfo["headimgurl"];
                } else {
                    message('授权失败!');
                }
            } else {
                if (!empty($this->_appsecret)) {
                    $this->getCode($url);
                }
            }
        }

        $fans = pdo_fetch("SELECT * FROM " . tablename($this->table_fans) . " WHERE from_user=:from_user AND weid=:weid LIMIT 1", array(':from_user' => $from_user, ':weid' => $weid));
        if ($this->_accountlevel == 4) {
            if (empty($fans) && !empty($nickname)) {
                $insert = array(
                    'weid' => $weid,
                    'from_user' => $from_user,
                    'nickname' => $nickname,
                    'headimgurl' => $headimgurl,
                    'dateline' => TIMESTAMP
                );
                pdo_insert($this->table_fans, $insert);
            }
        } else {
            if (empty($fans) && !empty($from_user)) {
                $insert = array(
                    'weid' => $weid,
                    'from_user' => $from_user,
                    'dateline' => TIMESTAMP
                );
                pdo_insert($this->table_fans, $insert);
            }
        }

        if ($mode == 1) {
            $table = pdo_fetch("SELECT * FROM " . tablename($this->table_tables) . " where weid = :weid AND id=:id LIMIT 1", array(':weid' => $weid, ':id' => $tablesid));
            if (empty($table)) {
                exit('餐桌不存在！');
            } else {
                $tablezones = pdo_fetch("SELECT * FROM " . tablename($this->table_tablezones) . " where weid = :weid AND id=:id LIMIT 1", array(':weid' => $weid, ':id' => $table['tablezonesid']));
                if (empty($tablezones)) {
                    exit('餐桌类型不存在！');
                }
                $table_title = $tablezones['title'] . '-' . $table['title'];
                pdo_update($this->table_tables, array('status' => 1), array('id' => $tablesid));

                pdo_insert($this->table_tables_order, array('from_user' => $from_user, 'weid' => $weid, 'tablesid' => $tablesid, 'storeid' => $storeid, 'dateline' => TIMESTAMP));
            }
        }

        if (empty($from_user)) {
           message('会话已过期/或未设置好公众号','http://dwz.cn/wzkj001');
        }

        $store = pdo_fetch("SELECT * FROM " . tablename($this->table_stores) . "  WHERE weid=:weid AND id=:id ORDER BY id DESC LIMIT 1", array(':weid' => $weid, ':id' => $storeid));

        if ($this->check_hourtime($store['begintime'], $store['endtime']) == 0) {
            message("营业时间" . $store['begintime'] . "~" . $store['endtime']);
        }

        $pindex = max(1, intval($_GPC['page']));
        $psize = 20;
        $condition = '';

        if (!empty($_GPC['ccate'])) {
            $cid = intval($_GPC['ccate']);
            $condition .= " AND ccate = '{$cid}'";
        } elseif (!empty($_GPC['pcate'])) {
            $cid = intval($_GPC['pcate']);
            $condition .= " AND pcate = '{$cid}'";
        }

        $children = array();
        $category = pdo_fetchall("SELECT * FROM " . tablename($this->table_category) . " WHERE weid = :weid AND storeid=:storeid ORDER BY  displayorder DESC,id DESC", array(':weid' => $weid, ':storeid' => $storeid));

        $cid = intval($category[0]['id']);
        $category_in_cart = pdo_fetchall("SELECT goodstype,count(1) as 'goodscount' FROM " . tablename($this->table_cart) . " GROUP BY weid,storeid,goodstype,from_user  having weid = '{$weid}' AND storeid='{$storeid}' AND from_user='{$from_user}'");
        $category_arr = array();
        foreach ($category_in_cart as $key => $value) {
            $category_arr[$value['goodstype']] = $value['goodscount'];
        }

        $list = pdo_fetchall("SELECT * FROM " . tablename($this->table_goods) . " WHERE weid = '{$weid}' AND storeid={$storeid} AND status = '1' AND pcate={$cid} ORDER BY displayorder DESC, subcount DESC, id DESC ");

        $dish_arr = $this->getDishCountInCart($storeid);

        $cart = pdo_fetchall("SELECT * FROM " . tablename($this->table_cart) . " WHERE  storeid=:storeid AND from_user=:from_user AND weid=:weid", array(':storeid' => $storeid, ':from_user' => $from_user, ':weid' => $weid));
        $totalcount = 0;
        $totalprice = 0;
        foreach ($cart as $key => $value) {
            $totalcount = $totalcount + $value['total'];
            $totalprice = $totalprice + $value['total'] * $value['price'];
        }

        $jump_url = $this->createMobileurl('wapmenu', array('from_user' => $from_user, 'storeid' => $storeid, 'mode' => $mode), true);
        $limitprice = 0;
        if ($mode == 1) {
            $limitprice = floatval($tablezones['limit_price']);
            $jump_url = $this->createMobileurl('wapmenu', array('from_user' => $from_user, 'storeid' => $storeid, 'mode' => $mode, 'tablesid' => $tablesid), true);
        } elseif ($mode == 2) {
            $limitprice = floatval($store['sendingprice']);
        } elseif ($mode == 3) {
            $rtype = 2;
            $timeid = intval($_GPC['timeid']);
            $select_date = trim($_GPC['selectdate']);
            $time = pdo_fetch("SELECT * FROM " . tablename($this->table_reservation) . " WHERE weid = :weid AND storeid =:storeid AND id=:id ORDER BY id LIMIT 1", array(':weid' => $this->_weid, ':storeid' => $storeid, ':id' => $timeid));
            if (!empty($time)) {
                $tablezones = pdo_fetch("SELECT * FROM " . tablename($this->table_tablezones) . " WHERE weid = :weid AND storeid =:storeid AND id=:id ORDER BY id LIMIT 1", array(':weid' => $this->_weid, ':storeid' => $storeid, ':id' => $time['tablezonesid']));
                $limitprice = floatval($tablezones['limit_price']);
            }
            $jump_url = $this->createMobileUrl('reservationdetail', array('storeid' => $storeid, 'mode' => 3, 'selectdate' => $select_date, 'timeid' => $timeid, 'rtype' => 2), true);
        } elseif ($mode == 5) {//排队
            $jump_url = $this->createMobileurl('queue', array('from_user' => $from_user, 'storeid' => $storeid), true);
        }

        include $this->template($this->cur_tpl . '/list');
    }

    //我的菜单
    public function doMobileWapMenu()
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        $from_user = $this->_fromuser;
        $title = '我的菜单';
        $do = 'menu';
        $storeid = intval($_GPC['storeid']);
        $mode = intval($_GPC['mode']);

        $user = pdo_fetch("SELECT * FROM " . tablename($this->table_fans) . " WHERE weid = :weid  AND from_user=:from_user ORDER BY `id` DESC limit 1", array(':weid' => $weid, ':from_user' => $from_user));
        if ($user['status'] == 0) {
            message('你被禁止下单,不能进行相关操作...');
        }

        if (empty($storeid)) {
            message('请先选择门店', $this->createMobileUrl('waprestlist'));
        }

        $method = 'wapmenu'; //method
        $authurl = $_W['siteroot'] . 'app/' . $this->createMobileUrl($method, array('storeid' => $storeid, 'mode' => $mode), true) . '&authkey=1';
        $url = $_W['siteroot'] . 'app/' . $this->createMobileUrl($method, array('storeid' => $storeid, 'mode' => $mode), true);
        if (isset($_COOKIE[$this->_auth2_openid])) {
            $from_user = $_COOKIE[$this->_auth2_openid];
            $nickname = $_COOKIE[$this->_auth2_nickname];
            $headimgurl = $_COOKIE[$this->_auth2_headimgurl];
        } else {
            if (isset($_GPC['code'])) {
                $userinfo = $this->oauth2($authurl);
                if (!empty($userinfo)) {
                    $from_user = $userinfo["openid"];
                    $nickname = $userinfo["nickname"];
                    $headimgurl = $userinfo["headimgurl"];
                } else {
                    message('授权失败!');
                }
            } else {
                if (!empty($this->_appsecret)) {
                    $this->getCode($url);
                }
            }
        }
        if (empty($from_user)) {
            message('会话已过期/或未设置好公众号','http://dwz.cn/wzkj001');
        }

        $store = pdo_fetch("SELECT * FROM " . tablename($this->table_stores) . " WHERE weid=:weid AND id=:id LIMIT 1", array(':weid' => $weid, ':id' => $storeid));

        if ($this->check_hourtime($store['begintime'], $store['endtime']) == 0) {
            message("营业时间" . $store['begintime'] . "~" . $store['endtime']);
        }


        $over_radius = 0;
        if ($mode == 2) {
            //距离
            $delivery_radius = floatval($store['delivery_radius']);
            $distance = $this->getDistance($user['lat'], $user['lng'], $store['lat'], $store['lng']);
            $distance = floatval($distance);
            if ($store['not_in_delivery_radius'] == 0) { //只能在距离范围内
                if ($distance > $delivery_radius) {
                    $over_radius = 1;
                }
            }
        }

        $mealtimes = pdo_fetchall("SELECT * FROM " . tablename($this->table_mealtime) . " WHERE weid=:weid AND storeid=:storeid ORDER BY id ASC", array(':weid' => $weid, ':storeid' => $storeid));
        $select_mealtime = '';
        $select_mealtime2 = '';
        $cur_date = date("Y-m-d", TIMESTAMP);
        foreach ($mealtimes as $key => $value) {
            $begintime = intval(strtotime(date('Y-m-d ') . $value['begintime']));
            $endtime = intval(strtotime(date('Y-m-d ') . $value['endtime']));
            if (TIMESTAMP < $endtime) {//debug
                $select_mealtime .= '<option value="' . $value['begintime'] . '~' . $value['endtime'] . '">' . $value['begintime'] . '~' . $value['endtime'] . '</option>';
            }
            $select_mealtime2 .= '<option value="' . $value['begintime'] . '~' . $value['endtime'] . '">' . $value['begintime'] . '~' . $value['endtime'] . '</option>';
        }
        if (empty($select_mealtime)) {
            $select_mealtime = '<option value="休息中">休息中</option>';
        }
        $select_mealdate = '';
        if (!empty($store['delivery_within_days'])) {
            for ($i = 0; $i < $store['delivery_within_days']; $i++) {
                $date_title = '';
                if ($i == 0) {
                    $date_value = date("Y-m-d", TIMESTAMP);
                    $date_title = '今日';
                } elseif ($i == 1) {
                    $date_value = date("Y-m-d", strtotime("+{$i} day"));
                    $date_title = '明日';
                } else {
                    $date_value = date("Y-m-d", strtotime("+{$i} day"));
                    $date_title = date("Y-m-d", strtotime("+{$i} day"));
                }

                $select_mealdate .= "<option value='{$date_value}'>{$date_title}</option>";
            }
        }

        $flag = false;
        $issms = intval($store['is_sms']);
        $checkcode = pdo_fetch("SELECT * FROM " . tablename('weisrc_dish_sms_checkcode') . " WHERE weid = :weid  AND from_user=:from_user AND status=1 ORDER BY `id` DESC limit 1", array(':weid' => $weid, ':from_user' => $from_user));
        if ($issms == 1 && empty($checkcode)) {
            $flag = true;
        }

        $setting = pdo_fetch("SELECT * FROM " . tablename($this->table_setting) . " WHERE weid=:weid LIMIT 1", array(':weid' => $weid));

        $cart = pdo_fetchall("SELECT * FROM " . tablename($this->table_cart) . " a LEFT JOIN " . tablename('weisrc_dish_goods') . " b ON a.goodsid=b.id WHERE a.weid=:weid AND a.from_user=:from_user AND a.storeid=:storeid", array(':weid' => $weid, ':from_user' => $from_user, ':storeid' => $storeid));
        $totalcount = 0;
        $totalprice = 0;
        foreach ($cart as $key => $value) {
            $totalcount = $totalcount + $value['total'];
            $totalprice = $totalprice + $value['total'] * $value['price'];
        }

        $jump_url = $this->createMobileurl('wapmenu', array('from_user' => $from_user, 'storeid' => $storeid), true);
        $limitprice = 0;
        if ($mode == 1) {
            $tablesid = intval($_GPC['tablesid']);
            $table = pdo_fetch("SELECT * FROM " . tablename($this->table_tables) . " where weid = :weid AND id=:id LIMIT 1", array(':weid' => $weid, ':id' => $tablesid));
            if (empty($table)) {
                exit('餐桌不存在！');
            } else {
                $tablezones = pdo_fetch("SELECT * FROM " . tablename($this->table_tablezones) . " where weid = :weid AND id=:id LIMIT 1", array(':weid' => $weid, ':id' => $table['tablezonesid']));
                if (empty($tablezones)) {
                    exit('餐桌类型不存在！');
                }
                $table_title = $tablezones['title'] . '-' . $table['title'];
            }
            $limitprice = floatval($tablezones['limit_price']);
        } elseif ($mode == 2) {
            $limitprice = floatval($store['sendingprice']);
            $jump_url = $this->createMobileurl('wapmenu', array('from_user' => $from_user, 'storeid' => $storeid, 'mode' => 2), true);
        } elseif ($mode == 5) {//排队
            $jump_url = $this->createMobileurl('queue', array('from_user' => $from_user, 'storeid' => $storeid), true);
        }

        if (!empty($from_user) && !(empty($weid))) {
            $order = pdo_fetch("SELECT * FROM " . tablename($this->table_order) . " WHERE weid=:weid AND from_user=:from_user ORDER BY id DESC LIMIT 1", array(':from_user' => $from_user, ':weid' => $weid));
        }

        $my_order_total = pdo_fetchcolumn("SELECT COUNT(1) FROM " . tablename($this->table_order) . " WHERE storeid=:storeid AND from_user=:from_user ", array(':from_user' => $from_user, ':storeid' => $storeid));
        $my_order_total = intval($my_order_total);

        include $this->template($this->cur_tpl . '/menu');
    }

    public function check_hourtime($begintime, $endtime)
    {
        global $_W, $_GPC;

        $nowtime = intval(date("Hi"));
        $begintime = intval(str_replace(':', '', $begintime));
        $endtime = intval(str_replace(':', '', $endtime));

        if ($nowtime >= $begintime && $nowtime <= $endtime) {
            return 1;
        }
        return 0;
    }

    public function check_mealtime()
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        $from_user = $this->_fromuser;

        $timelist = pdo_fetchall("SELECT * FROM " . tablename($this->table_mealtime) . " WHERE weid=:weid AND storeid=0 ", array(':weid' => $weid));

        $nowtime = intval(date("Hi"));

        foreach ($timelist as $key => $value) {
            $begintime = intval(str_replace(':', '', $value['begintime']));
            $endtime = intval(str_replace(':', '', $value['endtime']));

            if ($nowtime >= $begintime && $nowtime <= $endtime) {
                return 1;
            }
        }
        return 0;
    }

    public function testSendFormatedMessage()
    {
        $msgNo = time() + 1;
        /*
         格式化的打印内容
        */
        $msgInfo = array(
            'memberCode' => $this->member_code,
            'charge' => '3000',
            'customerName' => '刘小姐',
            'customerPhone' => '13321332245',
            'customerAddress' => '五山华南理工',
            'customerMemo' => '请快点送货',
            'msgDetail' => '番茄炒粉@1000@2||客家咸香鸡@2000@1',
            'deviceNo' => $this->device_no,
            'msgNo' => $msgNo,
        );

        echo $this->sendFormatedMessage($msgInfo);
        return $msgNo;
    }

    public function _365SendFreeMessage($orderid = 0, $print_type = 0)
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        load()->func('communication');

        if ($orderid == 0) {
            return -2;
        }

        $order = pdo_fetch("SELECT * FROM " . tablename($this->table_order) . " WHERE  id =:id AND weid=:weid ORDER BY id DESC limit 1", array(':id' => $orderid, ':weid' => $weid));

        if (empty($order)) {
            return -3;
        }

        $storeid = $order['storeid'];
        //打印机配置信息
        $settings = pdo_fetchall("SELECT * FROM " . tablename($this->table_print_setting) . " WHERE storeid = :storeid AND print_status=1 AND type='365' ", array(':storeid' => $storeid));

        if ($settings == false) {
            return -4;
        }

        $paytype = array('0' => '线下付款', '1' => '余额支付', '2' => 在线支付, '3' => '货到付款');
        //商品id数组
        $goodsid = pdo_fetchall("SELECT goodsid, total FROM " . tablename($this->table_order_goods) . " WHERE orderid = :orderid", array(':orderid' => $orderid), 'goodsid');
        $ordertype = array(
            '1' => '堂点',
            '2' => '外卖',
            '3' => '预定',
            '4' => '快餐'
        );
        $store = pdo_fetch("SELECT * FROM " . tablename($this->table_stores) . " WHERE  id =:id AND weid=:weid ORDER BY id DESC limit 1", array(':id' => $storeid, ':weid' => $weid));
        $paystatus = $order['ispay'] == 0 ? '未支付' : '已支付';
        $content = '订单编号:' . $order['ordersn'] . "\n";
        $content .= '订单类型:' . $ordertype[$order['dining_mode']] . "\n";
        $content .= '所属门店:' . $store['title'] . "\n";
        $content .= '支付方式:' . $paytype[$order['paytype']] . "(" . $paystatus . ")\n";
        $content .= '下单日期:' . date('Y-m-d H:i:s', $order['dateline']) . "\n";
        if ($order['dining_mode']==3) {
            $tablezones = pdo_fetch("SELECT * FROM " . tablename($this->table_tablezones) . " where weid = :weid AND id=:id LIMIT 1", array(':weid' => $weid, ':id' => $order['tablezonesid']));
            $content .= "桌台类型：{$tablezones['title']}\n";
        }
        if (!empty($order['tables'])) {
            $content .= '桌台信息:' . $this->getTableName($order['tables']) . "\n";
        }

        if (!empty($order['remark'])) {
            $content .= '备注:' . $order['remark'] . "\n";
        }
        $content .= '门店地址:' . $store['address'] . "\n";
        $content .= '门店电话:' . $store['tel'] . "\n";
        $content .= "\n菜单列表\n";
        $content .= "-------------------------\n";

        $content2 = "-------------------------\n";
        $content2 .= "总数量:" . $order['totalnum'] . "   总价:" . number_format($order['totalprice'], 2) . "元\n";
        if ($order['dining_mode'] != 4 && !empty($order['meal_time'])) {
            $content2 .= '预定时间:' . $order['meal_time'] . "\n";
        }
        if (!empty($order['username'])) {
            $content2 .= '姓名:' . $order['username'] . "\n";
        }
        if (!empty($order['tel'])) {
            $content2 .= '手机:' . $order['tel'] . "\n";
        }
        if (!empty($order['address'])) {
            $content2 .= '地址:' . $order['address'] . "\n";
        }

        if (!empty($setting['print_bottom'])) {
            $content2 .= "-------------------------\n";
            $content2 .= "" . $setting['print_bottom'] . "";
        }

        foreach ($settings as $item => $value) {
            if ($value['print_type'] == 1) {
                if ($order['ispay'] == 0) {
                    continue;
                }
            }

            if (!empty($value['print_top'])) {
                $print_top = "" . $value['print_top'] . "\n";
            }
            if (!empty($value['print_bottom'])) {
                $print_bottom = "\n" . $value['print_bottom'] . "";
            }

            //商品
            if ($value['print_goodstype'] == '0') {
                $goods = pdo_fetchall("SELECT * FROM " . tablename($this->table_goods) . "  WHERE id IN ('" . implode("','", array_keys($goodsid)) . "')");
            } else {
                $goods = pdo_fetchall("SELECT * FROM " . tablename($this->table_goods) . "  WHERE id IN ('" . implode("','", array_keys($goodsid)) . "') AND pcate IN ('" . $value['print_goodstype'] . "')");
            }
            $order['goods'] = $goods;
            $content1 = '';
            if ($value['is_print_all'] == 1) {
                if ($value['type'] == '365') {
                    $print_order_data = array(
                        'weid' => $weid,
                        'orderid' => $orderid,
                        'print_usr' => $value['print_usr'],
                        'print_status' => -1,
                        'dateline' => TIMESTAMP
                    );
                    pdo_insert($this->table_print_order, $print_order_data);
                    $oid = pdo_insertid();
                }
                foreach ($order['goods'] as $v) {
                    $money = $v['marketprice'];
                    $content1 .= $v['title'] . ' ' . $goodsid[$v['id']]['total'] . $v['unitname'] . ' ' . number_format($money, 2) . "元\n";
                }

                $deviceNo = $value['print_usr'];
                $key = $value['feyin_key'];
                $times = $value['print_nums'];
                $printContent = $print_top . $content . $content1 . $content2 . $print_bottom;

                $target = "http://open.printcenter.cn:8080/addOrder";
                $post_data = "deviceNo=" . $deviceNo . "&key=" . $key . "&printContent=" . $printContent . "&times=" . $times;
                $result = ihttp_request($target, $post_data);
                $_365status = $result['responseCode'];
                pdo_update('weisrc_dish_print_order', array('print_status' => $_365status), array('id' => $oid));
            } else {
                $content = '订单编号:' . $order['ordersn'] . "\n";
                $content .= '订单类型:' . $ordertype[$order['dining_mode']] . "\n";
                $content .= '所属门店:' . $store['title'] . "\n";
                $content .= '支付方式:' . $paytype[$order['paytype']] . "(" . $paystatus . ")\n";
                $content .= '下单日期:' . date('Y-m-d H:i:s', $order['dateline']) . "\n";
                if ($order['dining_mode'] != 4 && !empty($order['meal_time'])) {
                    $content .= '预定时间:' . $order['meal_time'] . "\n";
                }
                if (!empty($order['tables'])) {
                    $content .= '桌台信息:' . $this->getTableName($order['tables']) . "\n";
                }
                if (!empty($order['remark'])) {
                    $content .= '备注:' . $order['remark'] . "\n";
                }
                if (!empty($order['username'])) {
                    $content2 = '姓名:' . $order['username'] . "\n";
                }
                if (!empty($order['tel'])) {
                    $content2 .= '手机:' . $order['tel'] . "\n";
                }
                if (!empty($order['address'])) {
                    $content2 .= '地址:' . $order['address'] . "\n";
                }
                foreach ($order['goods'] as $v) {
                    if ($value['type'] == 'feiyin') { //飞印
                        $print_order_data = array(
                            'weid' => $weid,
                            'orderid' => $orderid,
                            'print_usr' => $value['print_usr'],
                            'print_status' => -1,
                            'dateline' => TIMESTAMP
                        );
                        pdo_insert($this->table_print_order, $print_order_data);
                        $oid = pdo_insertid();
                    }
                    $content1 = '';
                    $content1 .= "-------------------------\n";
                    $content1 .= '名称:' . $v['title'] . "\n";
                    $content1 .= '数量:' . $goodsid[$v['id']]['total'] . $v['unitname'] . "\n";
                    $content1 .= "-------------------------\n";

                    $deviceNo = $value['print_usr'];
                    $key = $value['feyin_key'];
                    $times = $value['print_nums'];
                    $printContent = $print_top . $content . $content1 . $content2 . $print_bottom;

                    $target = "http://open.printcenter.cn:8080/addOrder";
                    $post_data = "deviceNo=" . $deviceNo . "&key=" . $key . "&printContent=" . $printContent . "&times=" . $times;
                    $result = ihttp_request($target, $post_data);
                    $_365status = $result['responseCode'];
                    pdo_update('weisrc_dish_print_order', array('print_status' => $_365status), array('id' => $oid));
                }
            }
        }
    }

    function feiyinSendFreeMessage($orderid = 0, $print_type = 0)
    {
        global $_W, $_GPC;
        $weid = $this->_weid;

        if ($orderid == 0) {
            return -2;
        }

        $order = pdo_fetch("SELECT * FROM " . tablename($this->table_order) . " WHERE  id =:id AND weid=:weid ORDER BY id DESC limit 1", array(':id' => $orderid, ':weid' => $weid));

        if (empty($order)) {
            return -3;
        }

        $storeid = $order['storeid'];
        //打印机配置信息
        $settings = pdo_fetchall("SELECT * FROM " . tablename($this->table_print_setting) . " WHERE storeid = :storeid AND print_status=1 AND type='feiyin' ", array(':storeid' => $storeid));

        if ($settings == false) {
            return -4;
        }

        $paytype = array('0' => '线下付款', '1' => '余额支付', '2' => 在线支付, '3' => '货到付款');
        //商品id数组
        $goodsid = pdo_fetchall("SELECT goodsid, total FROM " . tablename($this->table_order_goods) . " WHERE orderid = :orderid", array(':orderid' => $orderid), 'goodsid');

        $store = pdo_fetch("SELECT * FROM " . tablename($this->table_stores) . " WHERE  id =:id AND weid=:weid ORDER BY id DESC limit 1", array(':id' => $storeid, ':weid' => $weid));
        $ordertype = array(
            '1' => '堂点',
            '2' => '外卖',
            '3' => '预定',
            '4' => '快餐'
        );

        $paystatus = $order['ispay'] == 0 ? '未支付' : '已支付';
        $content = '订单编号:' . $order['ordersn'] . "\n";
        $content .= '订单类型:' . $ordertype[$order['dining_mode']] . "\n";
        $content .= '所属门店:' . $store['title'] . "\n";
        $content .= '支付方式:' . $paytype[$order['paytype']] . "(" . $paystatus . ")\n";
        $content .= '下单日期:' . date('Y-m-d H:i:s', $order['dateline']) . "\n";
        if ($order['dining_mode']==3) {
            $tablezones = pdo_fetch("SELECT * FROM " . tablename($this->table_tablezones) . " where weid = :weid AND id=:id LIMIT 1", array(':weid' => $weid, ':id' => $order['tablezonesid']));
            $content .= "桌台类型：{$tablezones['title']}\n";
        }
        if (!empty($order['tables'])) {
            $content .= '桌台信息:' . $this->getTableName($order['tables']) . "\n";
        }

        if (!empty($order['remark'])) {
            $content .= '备注:' . $order['remark'] . "\n";
        }
        $content .= '门店地址:' . $store['address'] . "\n";
        $content .= '门店电话:' . $store['tel'] . "\n";
        $content .= "菜单列表\n";
        $content .= "-------------------------\n";

        $content2 = "-------------------------\n";
        $content2 .= "总数量:" . $order['totalnum'] . "   总价:" . number_format($order['totalprice'], 2) . "元\n";
        if ($order['dining_mode'] != 4 && !empty($order['meal_time'])) {
            $content2 .= '预定时间:' . $order['meal_time'] . "\n";
        }
        if (!empty($order['username'])) {
            $content2 .= '姓名:' . $order['username'] . "\n";
        }
        if (!empty($order['tel'])) {
            $content2 .= '手机:' . $order['tel'] . "\n";
        }
        if (!empty($order['address'])) {
            $content2 .= '地址:' . $order['address'] . "\n";
        }
        $content2 .= "-------------------------\n";


        if (!empty($setting['print_bottom'])) {
            $content2 .= "" . $setting['print_bottom'] . "";
        }

        foreach ($settings as $item => $value) {
            if ($value['print_type'] == 1) {
                if ($order['ispay'] == 0) {
                    continue;
                }
            }

            if (!empty($value['print_top'])) {
                $print_top = "" . $value['print_top'] . "\n";
            }
            if (!empty($value['print_bottom'])) {
                $print_bottom = "\n" . $value['print_bottom'] . "";
            }

            $this->member_code = $value['member_code'];
            $this->device_no = $value['print_usr'];
            $this->feyin_key = $value['feyin_key'];

            //商品
            if ($value['print_goodstype'] == '0') {
                $goods = pdo_fetchall("SELECT * FROM " . tablename($this->table_goods) . "  WHERE id IN ('" . implode("','", array_keys($goodsid)) . "')");
            } else {
                $goods = pdo_fetchall("SELECT * FROM " . tablename($this->table_goods) . "  WHERE id IN ('" . implode("','", array_keys($goodsid)) . "') AND pcate IN ('" . $value['print_goodstype'] . "')");
            }
            $order['goods'] = $goods;
            $content1 = '';
            if ($value['is_print_all'] == 1) {
                if ($value['type'] == 'feiyin') { //飞印
                    $print_order_data = array(
                        'weid' => $weid,
                        'orderid' => $orderid,
                        'print_usr' => $value['print_usr'],
                        'print_status' => -1,
                        'dateline' => TIMESTAMP
                    );
                    pdo_insert($this->table_print_order, $print_order_data);
                    $oid = pdo_insertid();
                }
                foreach ($order['goods'] as $v) {
                    $money = $v['marketprice'];
                    $content1 .= $v['title'] . ' ' . $goodsid[$v['id']]['total'] . $v['unitname'] . ' ' . number_format($money, 2) . "元\n";
                }
                $msgDetail = $print_top . $content . $content1 . $content2 . $print_bottom;
                $msgNo = time() + 1;
                $freeMessage = array(
                    'memberCode' => $this->member_code,
                    'msgDetail' => $msgDetail,
                    'deviceNo' => $this->device_no,
                    'msgNo' => $oid,
                );
                $feiyinstatus = $this->sendFreeMessage($freeMessage);
                pdo_update('weisrc_dish_print_order', array('print_status' => $feiyinstatus), array('id' => $oid));
            } else {
                $content = '订单编号:' . $order['ordersn'] . "\n";
                $content .= '订单类型:' . $ordertype[$order['dining_mode']] . "\n";
                $content .= '所属门店:' . $store['title'] . "\n";
                $content .= '支付方式:' . $paytype[$order['paytype']] . "(" . $paystatus . ")\n";
                $content .= '下单日期:' . date('Y-m-d H:i:s', $order['dateline']) . "\n";
                if ($order['dining_mode'] != 4 && !empty($order['meal_time'])) {
                    $content .= '预定时间:' . $order['meal_time'] . "\n";
                }
                if (!empty($order['tables'])) {
                    $content .= '桌台信息:' . $this->getTableName($order['tables']) . "\n";
                }
                if (!empty($order['remark'])) {
                    $content .= '备注:' . $order['remark'] . "\n";
                }
                if (!empty($order['username'])) {
                    $content2 = '姓名:' . $order['username'] . "\n";
                }
                if (!empty($order['tel'])) {
                    $content2 .= '手机:' . $order['tel'] . "\n";
                }
                if (!empty($order['address'])) {
                    $content2 .= '地址:' . $order['address'] . "\n";
                }
                foreach ($order['goods'] as $v) {
                    if ($value['type'] == 'feiyin') { //飞印
                        $print_order_data = array(
                            'weid' => $weid,
                            'orderid' => $orderid,
                            'print_usr' => $value['print_usr'],
                            'print_status' => -1,
                            'dateline' => TIMESTAMP
                        );
                        pdo_insert($this->table_print_order, $print_order_data);
                        $oid = pdo_insertid();
                    }
                    $content1 = '';
                    $content1 .= "-------------------------\n";
                    $content1 .= '名称:' . $v['title'] . "\n";
                    $content1 .= '数量:' . $goodsid[$v['id']]['total'] . $v['unitname'] . "\n";
                    $content1 .= "-------------------------\n";

                    $msgDetail = $print_top . $content . $content1 . $content2 . $print_bottom;
                    $msgNo = time() + 1;
                    $freeMessage = array(
                        'memberCode' => $this->member_code,
                        'msgDetail' => $msgDetail,
                        'deviceNo' => $this->device_no,
                        'msgNo' => $oid,
                    );
                    $feiyinstatus = $this->sendFreeMessage($freeMessage);
                    pdo_update('weisrc_dish_print_order', array('print_status' => $feiyinstatus), array('id' => $oid));
                }
            }
        }
        return $msgNo;
    }

    //用户打印机处理订单
    private function feiyinformat($string, $length = 0, $isleft = true)
    {
        $substr = '';
        if ($length == 0 || $string == '') {
            return $string;
        }
        if ($this->print_strlen($string) > $length) {
            for ($i = 0; $i < $length; $i++) {
                $substr = $substr . "  ";
            }
            $string = $string . $substr;
        } else {
            for ($i = $this->print_strlen($string); $i < $length; $i++) {
                $substr = $substr . " ";
            }
            $string = $isleft ? ($string . $substr) : ($substr . $string);
        }
        return $string;
    }

    /**
     * @param string $l
     * @param string $r
     * @return string
     */
    function formatstr($l = '', $r = '')
    {
        $nbsp = '                              ';
        $llen = $this->print_strlen($l);
        $rlen = $this->print_strlen($r);
        if ($l && $r) {
            $lr = $llen + $rlen;
            $nl = $this->print_strlen($nbsp);
            if ($lr >= $nl) {
                $strtxt = $l . "\r\n" . $this->formatstr(null, $r);
            } else {
                $strtxt = $l . substr($nbsp, $lr) . $r;
            }
        } elseif ($r) {
            $strtxt = substr($nbsp, $rlen) . $r;
        } else {
            $strtxt = $l;
        }
        return $strtxt;
    }

    /**
     * PHP获取字符串中英文混合长度
     * @param $str        字符串
     * @param string $charset 编码
     * @return int 返回长度，1中文=2位(utf-8为3位)，1英文=1位
     */
    private function print_strlen($str, $charset = '')
    {
        global $_W;
        if (empty($charset)) {
            $charset = $_W['charset'];
        }
        if (strtolower($charset) == 'gbk') {
            $charset = 'gbk';
            $ci = 2;
        } else {
            $charset = 'utf-8';
            $ci = 3;
        }
        if (strtolower($charset) == 'utf-8') $str = iconv('utf-8', 'GBK//IGNORE', $str);
        $num = strlen($str);
        $cnNum = 0;
        for ($i = 0; $i < $num; $i++) {
            if (ord(substr($str, $i + 1, 1)) > 127) {
                $cnNum++;
                $i++;
            }
        }
        $enNum = $num - ($cnNum * $ci);
        $number = $enNum + $cnNum * $ci;
        return ceil($number);
    }

    //门店列表
    public function doMobileWapRestList()
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        $from_user = $this->_fromuser;
        $setting = $this->getSetting();
        $cur_nave = 'home';

        if ($setting['mode'] == 1) {
            $jump_url = $this->createMobileUrl('detail', array('id' => $setting['storeid']), true);
            header("location:$jump_url");
        }

        $areaid = intval($_GPC['areaid']);
        $typeid = intval($_GPC['typeid']);
        $sortid = intval($_GPC['sortid']);

        $lat = trim($_GPC['lat']);
        $lng = trim($_GPC['lng']);
        $isposition = 0;
        if (!empty($lat) && !empty($lng)) {
            $isposition = 1;
            setcookie($this->_lat, $lat, TIMESTAMP + 3600 * 12);
            setcookie($this->_lng, $lng, TIMESTAMP + 3600 * 12);
        } else {
            if (isset($_COOKIE[$this->_lat])) {
                $isposition = 1;//0的时候才跳转
                $lat = $_COOKIE[$this->_lat];
                $lng = $_COOKIE[$this->_lng];
            }
        }

        $method = 'waprestlist'; //method
        $authurl = $_W['siteroot'] . 'app/' . $this->createMobileUrl($method, array(), true) . '&authkey=1';
        $url = $_W['siteroot'] . 'app/' . $this->createMobileUrl($method, array(), true);
        if (isset($_COOKIE[$this->_auth2_openid])) {
            $from_user = $_COOKIE[$this->_auth2_openid];
            $nickname = $_COOKIE[$this->_auth2_nickname];
            $headimgurl = $_COOKIE[$this->_auth2_headimgurl];
        } else {
            if (isset($_GPC['code'])) {
                $userinfo = $this->oauth2($authurl);
                if (!empty($userinfo)) {
                    $from_user = $userinfo["openid"];
                    $nickname = $userinfo["nickname"];
                    $headimgurl = $userinfo["headimgurl"];
                } else {
                    message('授权失败!');
                }
            } else {
                if (!empty($this->_appsecret)) {
                    $this->getCode($url);
                }
            }
        }

        if (empty($from_user)) {
            message('会话已过期/或未设置好公众号','http://dwz.cn/wzkj001');
        }

        if ($areaid != 0) {
            $strwhere = " AND areaid={$areaid} ";
        }

        if ($typeid != 0) {
            $strwhere .= " AND typeid={$typeid} ";
        }

        //所属区域
        $area = pdo_fetchall("SELECT * FROM " . tablename($this->table_area) . " where weid = :weid ORDER BY displayorder DESC", array(':weid' => $weid), 'id');
        $curarea = "全城";
        if (!empty($area[$areaid]['name'])) {
            $curarea = $area[$areaid]['name'];
        }
        //门店类型
        $shoptype = pdo_fetchall("SELECT * FROM " . tablename($this->table_type) . " where weid = :weid ORDER BY displayorder DESC", array(':weid' => $weid), 'id');
        $curtype = "门店类型";
        if (!empty($shoptype[$typeid]['name'])) {
            $curtype = $shoptype[$typeid]['name'];
        }
        $cursort = "综合排序";
        if ($sortid == 1) {
            $cursort = "正在营业";
        } else if ($sortid == 2) {
            $cursort = "距离优先";
        }

        pdo_update($this->table_stores, array('is_rest' => 0));
        pdo_query("UPDATE " . tablename($this->table_stores) . " SET is_rest=1 WHERE date_format(now(),'%H:%i') between begintime and endtime");

        if ($sortid == 1) {
            $restlist = pdo_fetchall("SELECT *,(lat-:lat) * (lat-:lat) + (lng-:lng) * (lng-:lng) as dist FROM " . tablename($this->table_stores) . " where weid = :weid and is_show=1 {$strwhere} ORDER BY is_rest DESC,displayorder DESC, id DESC", array(':weid' => $weid, ':lat' => $lat, ':lng' => $lng));
        } else if ($sortid == 2) {
            if (empty($lat)) {
                message('没定位无法距离排序！');
            }
            $restlist = pdo_fetchall("SELECT *,(lat-:lat) * (lat-:lat) + (lng-:lng) * (lng-:lng) as dist FROM " . tablename($this->table_stores) . " WHERE weid = :weid and is_show=1 {$strwhere} ORDER BY dist, displayorder DESC,id DESC", array(':weid' => $weid, ':lat' => $lat, ':lng' => $lng));
        } else {
            $restlist = pdo_fetchall("SELECT *,(lat-:lat) * (lat-:lat) + (lng-:lng) * (lng-:lng) as dist FROM " . tablename($this->table_stores) . " where weid = :weid and is_show=1 {$strwhere} ORDER BY is_rest DESC,displayorder DESC, id DESC", array(':weid' => $weid, ':lat' => $lat, ':lng' => $lng));
        }

        include $this->template($this->cur_tpl . '/restlist');
    }

    //门店列表
    public function doMobileSearch()
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        $from_user = $this->_fromuser;
        $setting = $this->getSetting();
        $cur_nave = 'search';

        $word = $setting['searchword'];
        if ($word) {
            $words = explode(' ', $word);
        }

        $searchword = trim($_GPC['searchword']);
        if ($searchword) {
            $strwhere = " AND title like '%" . $searchword . "%' ";
            $list = pdo_fetchall("SELECT * FROM " . tablename($this->table_stores) . " where weid = :weid {$strwhere} ORDER BY displayorder DESC,id DESC", array(':weid' => $weid));
        } else {
            $list = pdo_fetchall("SELECT * FROM " . tablename($this->table_stores) . " where weid = :weid AND is_hot=1 ORDER BY displayorder DESC,id DESC", array(':weid' => $weid));
        }

        include $this->template($this->cur_tpl . '/search');
    }

    public function doMobileReservationIndex()
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        $from_user = $this->_fromuser;
        $storeid = intval($_GPC['storeid']);

        $cur_date = date("Y-m-d", TIMESTAMP);
        $cur_time = date("H:i", TIMESTAMP);
        $select_date = empty($_GPC['selectdate']) ? $cur_date : $_GPC['selectdate'];

        $tablezones = pdo_fetchall("SELECT * FROM " . tablename($this->table_tablezones) . " WHERE weid = :weid AND storeid =:storeid ORDER BY displayorder DESC, id DESC", array(':weid' => $this->_weid, ':storeid' => $storeid));

        $list = pdo_fetchall("SELECT * FROM " . tablename($this->table_reservation) . " WHERE weid = :weid AND storeid =:storeid ORDER BY id ", array(':weid' => $this->_weid, ':storeid' => $storeid));

        $dates = array();
        for ($i = 0; $i < 7; $i++) {
            if ($i == 0) {
                $dates[] = date("Y-m-d", TIMESTAMP);
            } else {
                $dates[] = date("Y-m-d", strtotime("+{$i} day"));
            }
        }

        include $this->template($this->cur_tpl . '/reservation_index');
    }

    public function doMobileReservationDetail()
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        $from_user = $this->_fromuser;
        $storeid = intval($_GPC['storeid']);
        $rtype = !isset($_GPC['rtype']) ? 1 : intval($_GPC['rtype']);
        $timeid = intval($_GPC['timeid']);
        $select_date = trim($_GPC['selectdate']);

        $store = pdo_fetch("SELECT * FROM " . tablename($this->table_stores) . " WHERE weid = :weid AND id=:id ORDER BY id LIMIT 1", array(':weid' => $this->_weid, ':id' => $storeid));

        $user = pdo_fetch("SELECT * FROM " . tablename($this->table_fans) . " WHERE weid = :weid  AND from_user=:from_user ORDER BY `id` DESC limit 1", array(':weid' => $weid, ':from_user' => $from_user));
        if ($user['status'] == 0) {
            message('你被禁止下单,不能进行相关操作...');
        }

        $time = pdo_fetch("SELECT * FROM " . tablename($this->table_reservation) . " WHERE weid = :weid AND storeid =:storeid AND id=:id ORDER BY id LIMIT 1", array(':weid' => $this->_weid, ':storeid' => $storeid, ':id' => $timeid));
        if (!empty($time)) {
            $tablezones = pdo_fetch("SELECT * FROM " . tablename($this->table_tablezones) . " WHERE weid = :weid AND storeid =:storeid AND id=:id ORDER BY id LIMIT 1", array(':weid' => $this->_weid, ':storeid' => $storeid, ':id' => $time['tablezonesid']));
        }

        $cart = pdo_fetchall("SELECT * FROM " . tablename($this->table_cart) . " a LEFT JOIN " . tablename('weisrc_dish_goods') . " b ON a.goodsid=b.id WHERE a.weid=:weid AND a.from_user=:from_user AND a.storeid=:storeid", array(':weid' => $weid, ':from_user' => $from_user, ':storeid' => $storeid));
        $totalcount = 0;
        $totalprice = 0;
        foreach ($cart as $key => $value) {
            $totalcount = $totalcount + $value['total'];
            $totalprice = $totalprice + $value['total'] * $value['price'];
        }

        $url1 = $this->createMobileUrl('reservationdetail', array('storeid' => $storeid, 'mode' => 3, 'selectdate' => $select_date, 'timeid' => $timeid, 'rtype' => 1), true);
        $url2 = $this->createMobileUrl('waplist', array('storeid' => $storeid, 'mode' => 3, 'selectdate' => $select_date, 'timeid' => $timeid, 'rtype' => 2), true);

        include $this->template($this->cur_tpl . '/reservation_detail');
    }

    public function doMobileQueue()
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        $from_user = $this->_fromuser;
        $storeid = intval($_GPC['storeid']);

        $config = $this->module['config']['weisrc_dish'];

        $user_queue = pdo_fetch("SELECT * FROM " . tablename($this->table_queue_order) . " where weid = :weid AND from_user=:from_user AND status=1 AND storeid=:storeid ORDER BY id DESC LIMIT 1 ", array(':weid' => $weid, ':from_user' => $from_user, ':storeid' => $storeid));
        if (!empty($user_queue)) {
            $queue_setting = pdo_fetch("SELECT * FROM " . tablename($this->table_queue_setting) . " where id = :id ORDER BY id DESC LIMIT 1 ", array(':id' => $user_queue['queueid']));
            $cur_queue = pdo_fetch("SELECT * FROM " . tablename($this->table_queue_order) . " where weid = :weid AND storeid=:storeid AND status=1 AND queueid=:queueid ORDER BY id ASC LIMIT 1 ", array(':weid' => $weid, ':storeid' => $storeid, ':queueid' => $user_queue['queueid']));
            $wait_count = pdo_fetchcolumn("SELECT COUNT(1) FROM " . tablename($this->table_queue_order) . " WHERE status=1 AND storeid=:storeid AND id<:id AND queueid=:queueid ORDER BY id DESC", array(':id' => $user_queue['id'], ':storeid' => $storeid, ':queueid' => $user_queue['queueid']));
        }
        $list = pdo_fetchall("SELECT * FROM " . tablename($this->table_queue_setting) . " WHERE weid = :weid AND storeid =:storeid AND :time>starttime AND :time<endtime ORDER BY limit_num ASC", array(':weid' => $this->_weid, ':storeid' => $storeid, ':time' => date('H:i')));
        $queue_count = pdo_fetchall("SELECT queueid,COUNT(1) as count FROM " . tablename($this->table_queue_order) . " where storeid=:storeid AND status=1 AND  weid = :weid GROUP BY queueid", array(':weid' => $this->_weid, ':storeid' => $storeid), 'queueid');

        include $this->template($this->cur_tpl . '/queue');
    }

    public function doMobileQueueform()
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        $from_user = $this->_fromuser;
        $storeid = intval($_GPC['storeid']);
        $queueid = intval($_GPC['queueid']);

        $config = $this->module['config']['weisrc_dish'];
        if ($config['queuemode'] == 2) {
            $list = pdo_fetchall("SELECT * FROM " . tablename($this->table_queue_setting) . " WHERE weid = :weid AND storeid =:storeid AND :time>starttime AND :time<endtime  ORDER BY id ASC", array(':weid' => $this->_weid, ':storeid' => $storeid, ':time' => date('H:i')));
        }

        include $this->template($this->cur_tpl . '/queue_form');
    }

    public function doMobilesetqueue()
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        $from_user = $this->_fromuser;
        $storeid = intval($_GPC['storeid']);
        $queueid = intval($_GPC['queueid']);
        $usermobile = trim($_GPC['usermobile']);
        $usercount = trim($_GPC['usercount']);

        if (empty($from_user)) {
            $this->showMsg('请重新发送关键字进入系统!');
        }
        if (empty($storeid)) {
            $this->showMsg('请先选择门店!');
        }
        if (empty($usermobile)) {
            $this->showMsg('请输入手机号码!');
        }
        if (empty($usercount)) {
            $this->showMsg('请输入用户数量!');
        }
        $num = 'C001';
        $config = $this->module['config']['weisrc_dish'];
        if ($queueid == 0) { //未选队列
            if ($config['queuemode'] == 2) {
                $this->showMsg('请先选择队列!');
            }

            $queueSetting = pdo_fetch("SELECT * FROM " . tablename($this->table_queue_setting) . " WHERE weid = :weid AND storeid =:storeid AND :time>starttime AND :time<endtime AND :usercount<=limit_num ORDER BY limit_num ASC LIMIT 1", array(':weid' => $this->_weid, ':storeid' => $storeid, ':time' => date('H:i'), ':usercount' => $usercount));

            if (empty($queueSetting)) {
                $this->showMsg('没有符合您人数的队列!');
            }

            $exists_queue = pdo_fetch("SELECT * FROM " . tablename($this->table_queue_order) . " WHERE queueid=:queueid AND from_user=:from_user AND status=1 ORDER BY id DESC LIMIT 1", array(':queueid' => $queueSetting['id'], ':from_user' => $from_user));
            if (!empty($exists_queue)) {
                $this->showMsg('您已经在排队中！');
            }

            $queueOrder = pdo_fetch("SELECT * FROM " . tablename($this->table_queue_order) . " WHERE weid = :weid AND storeid =:storeid AND queueid=:queueid ORDER BY id DESC LIMIT 1", array(':weid' => $this->_weid, ':storeid' => $storeid, ':queueid' => $queueSetting['id']));

            if (empty($queueOrder)) {
                $num = $queueSetting['prefix'] . '001';
            } else {
                $num = intval(findNum($queueOrder['num']));
                $num++;
                $num = $queueSetting['prefix'] . str_pad($num, 3, "0", STR_PAD_LEFT);
            }
            $queueid = $queueSetting['id'];
        } else { //已选队列
            if (empty($config['queuemode']) || $config['queuemode'] == 1) {
                $this->showMsg('请先选择队列!');
            }
            $queueSetting = pdo_fetch("SELECT * FROM " . tablename($this->table_queue_setting) . " WHERE weid = :weid AND storeid =:storeid AND :time>starttime AND :time<endtime AND id=:id AND :usercount<=limit_num LIMIT 1", array(':weid' => $this->_weid, ':storeid' => $storeid, ':time' => date('H:i'), ':id' => $queueid, ':usercount' => $usercount));

            if (empty($queueSetting)) {
                $this->showMsg('没有符合您人数的队列!');
            }

            $exists_queue = pdo_fetch("SELECT * FROM " . tablename($this->table_queue_order) . " WHERE queueid=:queueid AND from_user=:from_user AND status=1 ORDER BY id DESC LIMIT 1", array(':queueid' => $queueSetting['id'], ':from_user' => $from_user));
            if (!empty($exists_queue)) {
                $this->showMsg('您已经在排队中！');
            }

            $queueOrder = pdo_fetch("SELECT * FROM " . tablename($this->table_queue_order) . " WHERE weid = :weid AND storeid =:storeid AND queueid=:queueid ORDER BY id DESC LIMIT 1", array(':weid' => $this->_weid, ':storeid' => $storeid, ':queueid' => $queueSetting['id']));

            if (empty($queueOrder)) {
                $num = $queueSetting['prefix'] . '001';
            } else {
                $num = intval(findNum($queueOrder['num']));
                $num++;
                $num = $queueSetting['prefix'] . str_pad($num, 3, "0", STR_PAD_LEFT);
            }
        }

        $data = array(
            'weid' => $weid,
            'from_user' => $from_user,
            'storeid' => $storeid,
            'queueid' => $queueid,
            'num' => $num,
            'mobile' => $usermobile,
            'usercount' => $usercount,
            'isnotify' => 0,
            'status' => 1, //状态
            'dateline' => TIMESTAMP
        );

        pdo_insert($this->table_queue_order, $data);
        $oid = pdo_insertid();
        if ($oid > 0) {
            if ($this->_accountlevel == 4) {
                $this->sendQueueNotice($oid);
                $setting = pdo_fetch("select * from " . tablename($this->table_setting) . " WHERE weid =:weid LIMIT 1", array(':weid' => $weid));
                if (!empty($setting)) {
                    if (!empty($setting['tpluser'])) {
                        $tousers = explode(',', $setting['tpluser']);
                        foreach ($tousers as $key => $value) {
                            $this->sendAdminQueueNotice($oid, $value, $setting);
                        }
                    }
                    $accounts = pdo_fetchall("SELECT * FROM " . tablename($this->table_account) . " WHERE weid = :weid AND storeid=:storeid AND status=1 ORDER BY id DESC ", array(':weid' => $this->_weid, ':storeid' => $storeid));
                    foreach ($accounts as $key => $value) {
                        if (!empty($value['from_user'])) {
                            $this->sendAdminQueueNotice($oid, $value['from_user'], $setting);
                        }
                    }
                }
            }
        }
        $this->showMsg('操作成功!', 1);
    }

    public function doMobileCancelOrder()
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        $from_user = $this->_fromuser;
        $id = intval($_GPC['id']);

        if (empty($from_user)) {
            $this->showMsg('请重新发送关键字进入系统!');
        }

        if ($id == 0) { //未选队列
            $this->showMsg('请先选择订单!');
        } else { //已选队列
            $order = pdo_fetch("SELECT * FROM " . tablename($this->table_order) . " WHERE id=:id AND from_user=:from_user AND status=0 ORDER BY id DESC LIMIT 1", array(':id' => $id, ':from_user' => $from_user));
            if (empty($order)) {
                $this->showMsg('订单不存在！');
            }

            pdo_update($this->table_order, array('status' => -1), array('id' => $id));
        }
        $this->showMsg('操作成功!', 1);
    }

    public function sendQueueNotice($oid, $status = 1)
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        $from_user = $this->_fromuser;

        $setting = pdo_fetch("select * from " . tablename($this->table_setting) . " WHERE weid =:weid LIMIT 1", array(':weid' => $weid));
        $order = pdo_fetch("select * from " . tablename($this->table_queue_order) . " WHERE id =:id LIMIT 1", array(':id' => $oid));
        $store = pdo_fetch("select * from " . tablename($this->table_stores) . " WHERE id =:id LIMIT 1", array(':id' => $order['storeid']));
        $keyword1 = $order['num'];
        $keyword2 = date("Y-m-d H:i", $order['dateline']);
        $url = $_W['siteroot'].'app'.str_replace('./', '/', $this->createMobileUrl('queue', array('storeid' => $order['storeid']), true));
        $wait_count = pdo_fetchcolumn("SELECT COUNT(1) FROM " . tablename($this->table_queue_order) . " WHERE status=1 AND storeid=:storeid AND id<:id AND queueid=:queueid ORDER BY id DESC", array(':id' => $oid, ':storeid' => $order['storeid'], ':queueid' => $order['queueid']));
        $queueStatus = array(
            '1' => '排队中',
            '2' => '已接受',
            '-1' => '已取消',
            '3' => '已过号'
        );

        if (!empty($setting['tplnewqueue']) && $setting['istplnotice'] == 1) {
            $templateid = $setting['tplnewqueue'];

            if ($setting['tpltype'] == 1) {
                if ($status == 1) { //排队中
                    $first = "排号提醒：编号{$keyword1}已成功领号，您可以点击本消息提前点菜，节约等待时间哦";
                    $remark = "门店名称：{$store['title']}";
                    $remark .= "\n排队号码：" . $this->getQueueName($order['queueid']). " " . $order['num'];
                    $remark .= "\n前面等待：" . intval($wait_count);
                    $remark .= "\n排队状态：排队中";
                } else if ($status == 2) { //排队提醒
                    $first = "排号提醒：还需等待{$wait_count}桌";
                    $remark = "门店名称：{$store['title']}";
                    $remark .= "\n排队号码：" . $this->getQueueName($order['queueid']). " " . $order['num'];
                    $remark .= "\n前面等待：" . intval($wait_count) . "桌";
                    $remark .= "\n排队状态：" . $queueStatus[$order['status']];
                } else if ($status == 3) { //取消提醒
                    $first = "排号取消提醒：编号".$order['num']."已取消";
                    $remark = "您在{$store['title']}的排队状态更新为已取消，如有疑问，请联系我们工作人员";
                    $remark .= "\n排队号码：" . $this->getQueueName($order['queueid']). " " . $order['num'];
                    $remark .= "\n排队状态：已取消";
                }
                $template = array(
                    'touser' => $order['from_user'],
                    'template_id' => $templateid,
                    'url' => $url,
                    'topcolor' => "#a6a6a9",
                    'data' => array(
                        'first' => array(
                            'value' => urlencode($first),
                            'color' => '#a6a6a9'
                        ),
                        'keyword1' => array(
                            'value' => urlencode($keyword1),
                            'color' => '#a6a6a9'
                        ),
                        'keyword2' => array(
                            'value' => urlencode($keyword2),
                            'color' => '#a6a6a9'
                        ),
                        'remark' => array(
                            'value' => $remark,
                            'color' => '#a6a6a9'
                        ),
                    )
                );
            } else {
                $keyword3 = intval($wait_count);
                if ($status == 1) { //排队中

                    $first = "排号提醒：编号{$keyword1}已成功领号，您可以点击本消息提前点菜，节约等待时间哦";
                    $remark = "门店名称：{$store['title']}";
                    $remark .= "\n排队号码：" . $this->getQueueName($order['queueid']). " " . $order['num'];
                    $remark .= "\n排队状态：排队中";
                } else if ($status == 2) { //排队提醒
                    $first = "排号提醒：还需等待{$wait_count}桌";
                    $remark = "门店名称：{$store['title']}";
                    $remark .= "\n排队号码：" . $this->getQueueName($order['queueid']). " " . $order['num'];
                    $remark .= "\n排队状态：" . $queueStatus[$order['status']];
                } else if ($status == 3) { //取消提醒
                    $first = "排号取消提醒：编号".$order['num']."已取消";
                    $remark = "您在{$store['title']}的排队状态更新为已取消，如有疑问，请联系我们工作人员";
                    $remark .= "\n排队号码：" . $this->getQueueName($order['queueid']). " " . $order['num'];
                    $remark .= "\n排队状态：已取消";
                }
                $template = array(
                    'touser' => $order['from_user'],
                    'template_id' => $templateid,
                    'url' => $url,
                    'topcolor' => "#a6a6a9",
                    'data' => array(
                        'first' => array(
                            'value' => urlencode($first),
                            'color' => '#a6a6a9'
                        ),
                        'keyword1' => array(
                            'value' => urlencode($keyword1),
                            'color' => '#a6a6a9'
                        ),
                        'keyword2' => array(
                            'value' => urlencode($keyword2),
                            'color' => '#a6a6a9'
                        ),
                        'keyword3' => array(
                            'value' => urlencode($keyword3),
                            'color' => '#a6a6a9'
                        ),
                        'remark' => array(
                            'value' => $remark,
                            'color' => '#a6a6a9'
                        ),
                    )
                );
            }

            pdo_update($this->table_queue_order, array('isnotify' => 1), array('id' => $oid));
            $templateMessage = new class_templateMessage($this->_appid, $this->_appsecret);
            $access_token = WeAccount::token();
            $result = $templateMessage->send_template_message($template, $access_token);
        } else {
            if ($status == 1) { //排队中
                $content = '排号提醒：编号'.$keyword1.'已成功领号，您可以<a href=\"'.$url.'\">点击本消息</a>提前点菜，节约等待时间哦';
                $content .= "\n当前排号：{$keyword1}";
                $content .= "\n取号时间：{$keyword2}";
                $content .= "\n门店名称：{$store['title']}";
                $content .= "\n排队号码：" . $this->getQueueName($order['queueid']). " " . $order['num'];
                $content .= "\n前面等待：" . intval($wait_count);
                $content .= "\n排队状态：排队中";
            } else if ($status == 2) { //排队提醒
                $content = "排号提醒：还需等待{$wait_count}桌";
                $content .= "\n当前排号：{$keyword1}";
                $content .= "\n取号时间：{$keyword2}";
                $content .= "\n门店名称：{$store['title']}";
                $content .= "\n排队号码：" . $this->getQueueName($order['queueid']). " " . $order['num'];
                $content .= "\n前面等待：" . intval($wait_count) . "桌";
                $content .= "\n排队状态：" . $queueStatus[$order['status']];
            } else if ($status == 3) { //取消提醒
                $content = "排号取消提醒：编号".$order['num']."已取消";
                $content .= "\n当前排号：{$keyword1}";
                $content .= "\n取号时间：{$keyword2}";
                $content .= "\n您在{$store['title']}的排队状态更新为已取消，如果疑问，请联系我们工作人员";
                $content .= "\n排队号码：" . $this->getQueueName($order['queueid']). " " . $order['num'];
                $content .= "\n排队状态：已取消";
            }
            $this->sendText($order['from_user'], $content);
        }
    }

    public function sendAdminQueueNotice($oid, $from_user, $setting)
    {
        global $_W, $_GPC;
        $weid = $this->_weid;

        $order = pdo_fetch("select * from " . tablename($this->table_queue_order) . " WHERE id =:id LIMIT 1", array(':id' => $oid));
        $store = pdo_fetch("select * from " . tablename($this->table_stores) . " WHERE id =:id LIMIT 1", array(':id' => $order['storeid']));
        $keyword1 = $order['num'];
        $keyword2 = date("Y-m-d H:i", $order['dateline']);
        $url = '';
        $wait_count = pdo_fetchcolumn("SELECT COUNT(1) FROM " . tablename($this->table_queue_order) . " WHERE status=1 AND storeid=:storeid AND queueid=:queueid ORDER BY id DESC", array(':storeid' => $order['storeid'], ':queueid' => $order['queueid']));

        if (!empty($setting['tplnewqueue']) && $setting['istplnotice'] == 1 && $setting['is_notice'] == 1 && !empty($from_user)) {
            $templateid = $setting['tplnewqueue'];

            $first = '排号提醒：有新的排号，编号' . $keyword1;

            if ($setting['tpltype'] == 1) {
                $remark = "门店名称：{$store['title']}";
                $remark .= "\n排队号码：" . $this->getQueueName($order['queueid']) . " " . $order['num'];
                $remark .= "\n排队等待：" . intval($wait_count) . '队';
                $template = array(
                    'touser' => $from_user,
                    'template_id' => $templateid,
                    'url' => $url,
                    'topcolor' => "#a6a6a9",
                    'data' => array(
                        'first' => array(
                            'value' => urlencode($first),
                            'color' => '#a6a6a9'
                        ),
                        'keyword1' => array(
                            'value' => urlencode($keyword1),
                            'color' => '#a6a6a9'
                        ),
                        'keyword2' => array(
                            'value' => urlencode($keyword2),
                            'color' => '#a6a6a9'
                        ),
                        'remark' => array(
                            'value' => $remark,
                            'color' => '#a6a6a9'
                        ),
                    )
                );
            } else {
                $remark = "门店名称：{$store['title']}";
                $remark .= "\n排队号码：" . $this->getQueueName($order['queueid']) . " " . $order['num'];

                $keyword3 = intval($wait_count);
                $template = array(
                    'touser' => $from_user,
                    'template_id' => $templateid,
                    'url' => $url,
                    'topcolor' => "#a6a6a9",
                    'data' => array(
                        'first' => array(
                            'value' => urlencode($first),
                            'color' => '#a6a6a9'
                        ),
                        'keyword1' => array(
                            'value' => urlencode($keyword1),
                            'color' => '#a6a6a9'
                        ),
                        'keyword2' => array(
                            'value' => urlencode($keyword2),
                            'color' => '#a6a6a9'
                        ),
                        'keyword3' => array(
                            'value' => urlencode($keyword3),
                            'color' => '#a6a6a9'
                        ),
                        'remark' => array(
                            'value' => $remark,
                            'color' => '#a6a6a9'
                        ),
                    )
                );
            }

            pdo_update($this->table_queue_order, array('isnotify' => 1), array('id' => $oid));
            $templateMessage = new class_templateMessage($this->_appid, $this->_appsecret);
            $access_token = WeAccount::token();
            $result = $templateMessage->send_template_message($template, $access_token);
        } else {
            if (!empty($setting['tpluser'])) { //排队中
                $content = '排号提醒：有新的排号，编号'.$keyword1;
                $content .= "\n当前排号：{$keyword1}";
                $content .= "\n取号时间：{$keyword2}";
                $content .= "\n门店名称：{$store['title']}";
                $content .= "\n排队号码：" . $this->getQueueName($order['queueid']). " " . $order['num'];
                $content .= "\n排队等待：" . intval($wait_count) . '队';
            }
            $this->sendText($from_user, $content);
        }
    }

    public function sendOrderSms($order)
    {
        global $_W, $_GPC;
        $weid = $order['weid'];

        //发送短信提醒
        $smsSetting = pdo_fetch("SELECT * FROM " . tablename($this->table_setting) . " WHERE weid=:weid LIMIT 1", array(':weid' => $weid));
        $sendInfo = array();
        if (!empty($smsSetting)) {
            if ($smsSetting['sms_enable'] == 1 && !empty($order['tel'])) {
                //模板
                $smsSetting['sms_business_tpl'] = '您的订单：[sn]，收货人：[name] 电话：[tel]，已经成功提交。感谢您的购买！';
                //订单号
                $smsSetting['sms_business_tpl'] = str_replace('[sn]', $order['ordersn'], $smsSetting['sms_business_tpl']);
                $smsSetting['sms_business_tpl'] = str_replace('[name]', $order['username'], $smsSetting['sms_business_tpl']);
                $smsSetting['sms_business_tpl'] = str_replace('[tel]', $order['tel'], $smsSetting['sms_business_tpl']);

                $sendInfo['username'] = $smsSetting['sms_username'];
                $sendInfo['pwd'] = $smsSetting['sms_pwd'];
                $sendInfo['mobile'] = $order['tel'];
                $sendInfo['content'] = $smsSetting['sms_business_tpl'];
                //debug

                $return_result_code = $this->_sendSms($sendInfo);
                $smsStatus = $this->sms_status[$return_result_code];
            }
        }
    }

    public function sendAdminOrderSms($mobile, $order)
    {
        global $_W, $_GPC;
        $weid = $order['weid'];

        //发送短信提醒
        $smsSetting = pdo_fetch("SELECT * FROM " . tablename($this->table_setting) . " WHERE weid=:weid LIMIT 1", array(':weid' => $weid));
        $sendInfo = array();
        if (!empty($smsSetting)) {
            if ($smsSetting['sms_enable'] == 1 && !empty($mobile)) {
                $smsSetting['sms_business_tpl'] = '您有新的订单：[sn]，收货人：[name]，电话：[tel]，请及时确认订单！';
                $smsSetting['sms_business_tpl'] = str_replace('[sn]', $order['ordersn'], $smsSetting['sms_business_tpl']);
                $smsSetting['sms_business_tpl'] = str_replace('[name]', $order['username'], $smsSetting['sms_business_tpl']);
                $smsSetting['sms_business_tpl'] = str_replace('[tel]', $order['tel'], $smsSetting['sms_business_tpl']);

                $sendInfo['username'] = $smsSetting['sms_username'];
                $sendInfo['pwd'] = $smsSetting['sms_pwd'];
                $sendInfo['mobile'] = $mobile;
                $sendInfo['content'] = $smsSetting['sms_business_tpl'];
                //debug

                $return_result_code = $this->_sendSms($sendInfo);
                $smsStatus = $this->sms_status[$return_result_code];
            }
            return $smsStatus;
        }
    }


    public function sendAdminOrderEmail($toemail, $order, $store, $goods_str)
    {
        $firstArr = array(
            '-1' => '已经取消',
            '0' => '已经提交',
            '1' => '已经确认',
            '2' => '已并台',
            '3' => '已经完成'
        );

        $orderStatus = array(
            '-1' => '已取消',
            '0' => '待处理',
            '1' => '已确认',
            '2' => '已并台',
            '3' => '已完成'
        );
        $paytype = array(
            '0' => '现金付款',
            '1' => '余额支付',
            '2' => '在线支付',
            '3' => '现金付款'
        );
        $paystatus = array(
            '0' => '未支付',
            '1' => '已支付'
        );

        //发送邮件提醒
        $emailSetting = pdo_fetch("SELECT * FROM " . tablename($this->table_setting) . " WHERE weid=:weid LIMIT 1", array(':weid' => $order['weid']));

        $keyword1 = $order['ordersn'];
        $keyword2 = $orderStatus[$order['status']];
        $keyword3 = date("Y-m-d H:i", $order['dateline']);

        $email_tpl = "
        您的订单{$order['ordersn']}{$firstArr[$order['status']]}<br/>
        订单号：{$keyword1}<br/>
        订单状态：{$keyword2}<br/>
        时间：{$keyword3}<br/>
        门店名称：{$store['title']}<br/>
        支付方式：{$paytype[$order['paytype']]}<br/>
        支付状态：{$paystatus[$order['ispay']]}<br/>
        ";
        if ($order['dining_mode'] == 3) {
            $email_tpl .= "预定人信息：{$order['username']}－{$order['tel']}<br/>";
            $email_tpl .= "预定时间：{$order['meal_time']}<br/>";
        } else {
            $email_tpl .= "联系方式：{$order['username']}－{$order['tel']}<br/>";
        }
        if ($order['dining_mode'] == 1) {
            $tablename = $this->getTableName($order['tables']);
            $email_tpl .= "桌台信息：{$tablename}<br/>";
        }
        if ($order['dining_mode'] == 2) {
            if (!empty($order['address'])) {
                $email_tpl .= "配送地址：{$order['address']}<br/>";
            }
            if (!empty($order['meal_time'])) {
                $email_tpl .= "配送时间：{$order['meal_time']}<br/>";
            }
        }
        $email_tpl .= "菜单：{$goods_str}<br/>";
        $email_tpl .= "备注：{$order['remark']}<br/>";
        $email_tpl .= "应收合计：{$order['totalprice']}";

        if (!empty($emailSetting) && !empty($emailSetting['email'])) {
            if ($emailSetting['email_host'] == 'smtp.qq.com' || $emailSetting['email_host'] == 'smtp.gmail.com') {
                $secure = 'ssl';
                $port = '465';
            } else {
                $secure = 'tls';
                $port = '25';
            }

            $mail_config = array();
            $mail_config['host'] = $emailSetting['email_host'];
            $mail_config['secure'] = $secure;
            $mail_config['port'] = $port;
            $mail_config['username'] = $emailSetting['email_user'];
            $mail_config['sendmail'] = $emailSetting['email_send'];
            $mail_config['password'] = $emailSetting['email_pwd'];
            $mail_config['mailaddress'] = $toemail;
            $mail_config['subject'] = '订单提醒';
            $mail_config['body'] = $email_tpl;
            $result = $this->sendmail($mail_config);
        }
    }

    public function sendOrderNotice($order ,$store, $setting)
    {
        global $_W, $_GPC;
        $weid = $this->_weid;

        $firstArr = array(
            '-1' => '已经取消',
            '0' => '已经提交',
            '1' => '已经确认',
            '2' => '已并台',
            '3' => '已经完成'
        );

        $orderStatus = array(
            '-1' => '已取消',
            '0' => '待处理',
            '1' => '已确认',
            '2' => '已并台',
            '3' => '已完成'
        );
        $paytype = array(
            '0' => '现金付款',
            '1' => '余额支付',
            '2' => '在线支付',
            '3' => '现金付款'
        );
        $paystatus = array(
            '0' => '未支付',
            '1' => '已支付'
        );

        $url = $_W['siteroot'].'app'.str_replace('./', '/', $this->createMobileUrl('orderdetail', array('orderid' => $order['id']), true));
        $keyword1 = $order['ordersn'];
        $keyword2 = $orderStatus[$order['status']];
        $keyword3 = date("Y-m-d H:i", $order['dateline']);

        if (!empty($setting['tplneworder']) && $setting['istplnotice'] == 1) {
            $templateid = $setting['tplneworder'];
            $first = "您的订单{$order['ordersn']}{$firstArr[$order['status']]}";
            $remark = "门店名称：{$store['title']}";
            $remark .= "\n支付方式：{$paytype[$order['paytype']]}";
            $remark .= "\n支付状态：{$paystatus[$order['ispay']]}";
            if ($order['dining_mode'] == 3) {
                if ($order['dining_mode']==3) {
                    $tablezones = pdo_fetch("SELECT * FROM " . tablename($this->table_tablezones) . " where weid = :weid AND id=:id LIMIT 1", array(':weid' => $weid, ':id' => $order['tablezonesid']));
                }
                $remark .= "\n预定人信息：{$order['username']}－{$order['tel']}";
                $remark .= "\n桌台类型：{$tablezones['title']}";
                $remark .= "\n预定时间：{$order['meal_time']}";
            } else {
                $remark .= "\n联系方式：{$order['username']}－{$order['tel']}";
            }
            if ($order['dining_mode'] == 1) {
                $tablename = $this->getTableName($order['tables']);
                $remark .= "\n桌台信息：{$tablename}";
            }
            if ($order['dining_mode'] == 2) {
                if (!empty($order['address'])) {
                    $remark .= "\n配送地址：{$order['address']}";
                }
                if (!empty($order['meal_time'])) {
                    $remark .= "\n配送时间：{$order['meal_time']}";
                }
            }
            if (!empty($order['remark'])) {
                $remark .= "\n备注：{$order['remark']}";
            }
            if (!empty($order['reply'])) {
                $remark .= "\n商家回复：{$order['reply']}";
            }

            $remark .= "\n应收合计：{$order['totalprice']}元";
            if ($order['credit'] > 0) {
                $remark .= "\n奖励积分：{$order['totalprice']}";
            }
            if ($setting['tpltype'] == 1) {
                $template = array(
                    'touser' => $order['from_user'],
                    'template_id' => $templateid,
                    'url' => $url,
                    'topcolor' => "#a6a6a9",
                    'data' => array(
                        'first' => array(
                            'value' => urlencode($first),
                            'color' => '#a6a6a9'
                        ),
                        'keyword1' => array(
                            'value' => urlencode($keyword1),
                            'color' => '#a6a6a9'
                        ),
                        'keyword2' => array(
                            'value' => urlencode($keyword2),
                            'color' => '#a6a6a9'
                        ),
                        'keyword3' => array(
                            'value' => urlencode($keyword3),
                            'color' => '#a6a6a9'
                        ),
                        'remark' => array(
                            'value' => $remark,
                            'color' => '#a6a6a9'
                        ),
                    )
                );
            } else {
                $remark = "订单状态：" . $keyword2 . "\n" . $remark;
                $template = array(
                    'touser' => $order['from_user'],
                    'template_id' => $templateid,
                    'url' => $url,
                    'topcolor' => "#a6a6a9",
                    'data' => array(
                        'first' => array(
                            'value' => urlencode($first),
                            'color' => '#a6a6a9'
                        ),
                        'keyword1' => array(
                            'value' => urlencode($keyword1),
                            'color' => '#a6a6a9'
                        ),
                        'keyword2' => array(
                            'value' => urlencode($keyword3),
                            'color' => '#a6a6a9'
                        ),
                        'remark' => array(
                            'value' => $remark,
                            'color' => '#a6a6a9'
                        ),
                    )
                );
            }

            $templateMessage = new class_templateMessage($this->_appid, $this->_appsecret);
            $access_token = WeAccount::token();
            $result = $templateMessage->send_template_message($template, $access_token);
        } else {
            $content = "您的订单{$order['ordersn']}{$firstArr[$order['status']]}";
            $content .= "\n订单号：{$keyword1}";
            $content .= "\n订单状态：{$keyword2}";
            $content .= "\n时间：{$keyword3}";
            $content .= "\n门店名称：{$store['title']}";
            $content .= "\n支付方式：{$paytype[$order['paytype']]}";
            $content .= "\n支付状态：{$paystatus[$order['ispay']]}";


            if ($order['dining_mode'] == 3) {
                if ($order['dining_mode']==3) {
                    $tablezones = pdo_fetch("SELECT * FROM " . tablename($this->table_tablezones) . " where weid = :weid AND id=:id LIMIT 1", array(':weid' => $weid, ':id' => $order['tablezonesid']));
                }
                $content .= "\n预定人信息：{$order['username']}－{$order['tel']}";
                $content .= "\n桌台类型：{$tablezones['title']}";
                $content .= "\n预定时间：{$order['meal_time']}";
            } else {
                $content .= "\n联系方式：{$order['username']}－{$order['tel']}";
            }
            if ($order['dining_mode'] == 1) {
                $tablename = $this->getTableName($order['tables']);
                $content .= "\n桌台信息：{$tablename}";
            }
            if ($order['dining_mode'] == 2) {
                if (!empty($order['address'])) {
                    $content .= "\n配送地址：{$order['address']}";
                }
                if (!empty($order['meal_time'])) {
                    $content .= "\n配送时间：{$order['meal_time']}";
                }
            }
            if (!empty($order['remark'])) {
                $content .= "\n备注：{$order['remark']}";
            }
            if (!empty($order['reply'])) {
                $content .= "\n商家回复：{$order['reply']}";
            }

            $content .= "\n应收合计：{$order['totalprice']}元";
            if ($order['credit'] > 0) {
                $content .= "\n奖励积分：{$order['totalprice']}";
            }
            $this->sendText($order['from_user'], $content);
        }
    }

    public function sendAdminOrderNotice($oid, $from_user, $setting)
    {
        global $_W, $_GPC;
        $weid = $this->_weid;

        $orderStatus = array(
            '-1' => '已取消',
            '0' => '待处理',
            '1' => '已确认',
            '2' => '已并台',
            '3' => '已完成'
        );
        $paytype = array(
            '0' => '现金付款',
            '1' => '余额支付',
            '2' => '在线支付',
            '3' => '现金付款'
        );
        $ordertype = array(
            '1' => '堂点',
            '2' => '外卖',
            '3' => '预定',
            '4' => '快餐'
        );
        $paystatus = array(
            '0' => '未支付',
            '1' => '已支付'
        );

        $order = pdo_fetch("select * from " . tablename($this->table_order) . " WHERE id =:id LIMIT 1", array(':id' => $oid));
        $store = pdo_fetch("select * from " . tablename($this->table_stores) . " WHERE id =:id LIMIT 1", array(':id' => $order['storeid']));
        $url = '';
        $keyword1 = $order['ordersn'];
        $keyword2 = $orderStatus[$order['status']];
        $keyword3 = date("Y-m-d H:i", $order['dateline']);

        if (!empty($setting['tplneworder']) && $setting['istplnotice'] == 1 && $setting['is_notice'] == 1 && !empty($from_user)) {
            $templateid = $setting['tplneworder'];
            $first = "您有新的订单";
            $remark = "门店名称：{$store['title']}";
            $remark .= "\n订单类型：{$ordertype[$order['dining_mode']]}";
            $remark .= "\n支付方式：{$paytype[$order['paytype']]}";
            $remark .= "\n支付状态：{$paystatus[$order['ispay']]}";

            $goods = pdo_fetchall("SELECT a.*,b.title,b.unitname FROM " . tablename($this->table_order_goods) . " as a left join  " . tablename($this->table_goods) . " as b on a.goodsid=b.id WHERE a.weid = :weid and a.orderid=:orderid", array(':weid' => $weid, ':orderid' => $oid));
            if (!empty($goods)) {
                $remark .= "\n商品名称   单价 数量";
                $remark .= "\n－－－－－－－－－－－－－－－－";
                foreach ($goods as $key => $value) {
                    $remark .= "\n{$value['title']} {$value['price']}元 {$value['total']}{$value['unitname']}";
                }
            }

            if ($order['dining_mode'] == 3) {
                if ($order['dining_mode']==3) {
                    $tablezones = pdo_fetch("SELECT * FROM " . tablename($this->table_tablezones) . " where weid = :weid AND id=:id LIMIT 1", array(':weid' => $weid, ':id' => $order['tablezonesid']));
                }
                $remark .= "\n预定人信息：{$order['username']}－{$order['tel']}";
                $remark .= "\n桌台类型：{$tablezones['title']}";
                $remark .= "\n预定时间：{$order['meal_time']}";
            } else {
                $remark .= "\n联系方式：{$order['username']}－{$order['tel']}";
            }
            if ($order['dining_mode'] == 1) {
                $tablename = $this->getTableName($order['tables']);
                $remark .= "\n桌台信息：{$tablename}";
            }
            if ($order['dining_mode'] == 2) {
                if (!empty($order['address'])) {
                    $remark .= "\n配送地址：{$order['address']}";
                }
                if (!empty($order['meal_time'])) {
                    $remark .= "\n配送时间：{$order['meal_time']}";
                }
            }
            $remark .= "\n备注：{$order['remark']}";
            $remark .= "\n应收合计：{$order['totalprice']}元";

            if ($setting['tpltype'] == 1) {
                $template = array(
                    'touser' => $from_user,
                    'template_id' => $templateid,
                    'url' => $url,
                    'topcolor' => "#a6a6a9",
                    'data' => array(
                        'first' => array(
                            'value' => urlencode($first),
                            'color' => '#a6a6a9'
                        ),
                        'keyword1' => array(
                            'value' => urlencode($keyword1),
                            'color' => '#a6a6a9'
                        ),
                        'keyword2' => array(
                            'value' => urlencode($keyword2),
                            'color' => '#a6a6a9'
                        ),
                        'keyword3' => array(
                            'value' => urlencode($keyword3),
                            'color' => '#a6a6a9'
                        ),
                        'remark' => array(
                            'value' => $remark,
                            'color' => '#a6a6a9'
                        ),
                    )
                );
            } else {
                $remark = "订单状态：" . $keyword2 . "\n" . $remark;
                $template = array(
                    'touser' => $from_user,
                    'template_id' => $templateid,
                    'url' => $url,
                    'topcolor' => "#a6a6a9",
                    'data' => array(
                        'first' => array(
                            'value' => urlencode($first),
                            'color' => '#a6a6a9'
                        ),
                        'keyword1' => array(
                            'value' => urlencode($keyword1),
                            'color' => '#a6a6a9'
                        ),
                        'keyword2' => array(
                            'value' => urlencode($keyword3),
                            'color' => '#a6a6a9'
                        ),
                        'remark' => array(
                            'value' => $remark,
                            'color' => '#a6a6a9'
                        ),
                    )
                );
            }

            $templateMessage = new class_templateMessage($this->_appid, $this->_appsecret);
            $access_token = WeAccount::token();
            $result = $templateMessage->send_template_message($template, $access_token);
        } else {
            $content = "您有新的订单";
            $content .= "\n订单类型：{$ordertype[$order['dining_mode']]}";
            $content .= "\n订单号：{$keyword1}";
            $content .= "\n订单状态：{$keyword2}";
            $content .= "\n时间：{$keyword3}";
            $content .= "\n门店名称：{$store['title']}";
            $content .= "\n支付方式：{$paytype[$order['paytype']]}";
            $content .= "\n支付状态：{$paystatus[$order['ispay']]}";
            $goods = pdo_fetchall("SELECT a.*,b.title,b.unitname FROM " . tablename($this->table_order_goods) . " as a left join  " . tablename($this->table_goods) . " as b on a.goodsid=b.id WHERE a.weid = :weid and a.orderid=:orderid", array(':weid' => $weid, ':orderid' => $oid));
            if (!empty($goods)) {
                $content .= "\n商品名称   单价 数量";
                $content .= "\n－－－－－－－－－－－－－－－－";
                foreach ($goods as $key => $value) {
                    $content .= "\n{$value['title']} {$value['price']} {$value['total']}{$value['unitname']}";
                }
            }
            if ($order['dining_mode'] == 3) {
                if ($order['dining_mode']==3) {
                    $tablezones = pdo_fetch("SELECT * FROM " . tablename($this->table_tablezones) . " where weid = :weid AND id=:id LIMIT 1", array(':weid' => $weid, ':id' => $order['tablezonesid']));
                }
                $content .= "\n预定人信息：{$order['username']}－{$order['tel']}";
                $content .= "\n桌台类型：{$tablezones['title']}";
                $content .= "\n预定时间：{$order['meal_time']}";
            } else {
                $content .= "\n联系方式：{$order['username']}－{$order['tel']}";
            }
            if ($order['dining_mode'] == 1) {
                $tablename = $this->getTableName($order['tables']);
                $content .= "\n桌台信息：{$tablename}";
            }
            if ($order['dining_mode'] == 2) {
                if (!empty($order['address'])) {
                    $content .= "\n配送地址：{$order['address']}";
                }
                if (!empty($order['meal_time'])) {
                    $content .= "\n配送时间：{$order['meal_time']}";
                }
            }
            $content .= "\n备注：{$order['remark']}";
            $content .= "\n应收合计：{$order['totalprice']}元";
            if (!empty($from_user)) {
                $this->sendText($from_user, $content);
            }
        }
    }

    public function doMobileScreen()
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        $from_user = $this->_fromuser;
        $storeid = intval($_GPC['storeid']);

        $config = $this->module['config']['weisrc_dish'];


        include $this->template('queue_screen');
    }

    public function doMobileRefreshScreen()
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        $from_user = $this->_fromuser;
        $storeid = intval($_GPC['storeid']);

        $list = pdo_fetchall("SELECT * FROM " . tablename($this->table_queue_setting) . " WHERE weid = :weid AND storeid =:storeid AND :time>starttime AND :time<endtime ORDER BY limit_num ASC ", array(':weid' => $this->_weid, ':storeid' => $storeid, ':time' => date('H:i')));
        $queue_count = pdo_fetchall("SELECT queueid,COUNT(1) as count FROM " . tablename($this->table_queue_order) . " where storeid=:storeid AND status=1 AND  weid = :weid GROUP BY queueid", array(':weid' => $this->_weid, ':storeid' => $storeid), 'queueid');

        $result = array(
            'list' => array()
        );

        $index = 0;
        foreach ($list AS $key => $value) {
            $current = 0;
            if (!empty($queue_count[$value['id']]['count'])) {
                $current = $queue_count[$value['id']]['count'];
            }

            $user_queue = pdo_fetch("SELECT * FROM " . tablename($this->table_queue_order) . " where weid = :weid AND status=1 AND storeid=:storeid AND queueid=:queueid ORDER BY id ASC LIMIT 1 ", array(':weid' => $weid, ':storeid' => $storeid, ':queueid' => $value['id']));

            $result['list'][] = array(
                'before' => 0,
                'type' => $value['title'],
                'current' => empty($user_queue['num']) ? 0 : $user_queue['num'],
                'type_name' => $value['title']
            );
            $index++;
        }
        echo json_encode($result);
    }

    public function doMobileCancelQueue()
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        $from_user = $this->_fromuser;
        $storeid = intval($_GPC['storeid']);

        $user_queue = pdo_fetch("SELECT * FROM " . tablename($this->table_queue_order) . " where weid = :weid AND from_user=:from_user AND status=1 AND storeid=:storeid ORDER BY id DESC LIMIT 1 ", array(':weid' => $weid, ':from_user' => $from_user, ':storeid' => $storeid));

        $resultid = pdo_update($this->table_queue_order, array('status' => -1), array('id' => $user_queue['id']));
        if ($resultid > 0) {
            if ($this->_accountlevel == 4) {
                $this->sendQueueNotice($user_queue['id'], 3);
            }
        }

        message('取消排号成功.', $this->createMobileurl('queue', array('storeid' => $storeid), true));
    }

    public function doMobileDetail()
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        $from_user = $this->_fromuser;
        $setting = $this->getSetting();
        $cur_nave = 'detail';
        $id = intval($_GPC['id']);

        $item = pdo_fetch("SELECT * FROM " . tablename($this->table_stores) . " where weid = :weid AND id=:id ORDER BY displayorder DESC", array(':weid' => $weid, ':id' => $id));
        $title = $item['title'];

        if (empty($item)) {
            message('店面不存在！');
        }

        $do = 'detail';
        $method = 'detail'; //method
        $authurl = $_W['siteroot'] . 'app/' . $this->createMobileUrl($method, array('id' => $id), true) . '&authkey=1';
        $url = $_W['siteroot'] . 'app/' . $this->createMobileUrl($method, array('id' => $id), true);
        if (isset($_COOKIE[$this->_auth2_openid])) {
            $from_user = $_COOKIE[$this->_auth2_openid];
            $nickname = $_COOKIE[$this->_auth2_nickname];
            $headimgurl = $_COOKIE[$this->_auth2_headimgurl];
        } else {
            if (isset($_GPC['code'])) {
                $userinfo = $this->oauth2($authurl);
                if (!empty($userinfo)) {
                    $from_user = $userinfo["openid"];
                    $nickname = $userinfo["nickname"];
                    $headimgurl = $userinfo["headimgurl"];
                    $sex = $userinfo["sex"];
                } else {
                    message('授权失败!');
                }
            } else {
                if (!empty($this->_appsecret)) {
                    $this->getCode($url);
                }
            }
        }

        $fans = pdo_fetch("SELECT * FROM " . tablename($this->table_fans) . " WHERE from_user=:from_user AND weid=:weid LIMIT 1", array(':from_user' => $from_user, ':weid' => $weid));
        if ($this->_accountlevel == 4) {
            if (empty($fans) && !empty($nickname)) {
                $insert = array(
                    'weid' => $weid,
                    'from_user' => $from_user,
                    'nickname' => $nickname,
                    'sex' => $sex,
                    'headimgurl' => $headimgurl,
                    'dateline' => TIMESTAMP
                );
                pdo_insert($this->table_fans, $insert);
            }
        } else {
            if (empty($fans) && !empty($from_user)) {
                $insert = array(
                    'weid' => $weid,
                    'from_user' => $from_user,
                    'dateline' => TIMESTAMP
                );
                pdo_insert($this->table_fans, $insert);
            }
        }
        $fans = pdo_fetch("SELECT * FROM " . tablename($this->table_fans) . " WHERE from_user=:from_user AND weid=:weid LIMIT 1", array(':from_user' => $from_user, ':weid' => $weid));

        $lat = trim($_GPC['lat']);
        $lng = trim($_GPC['lng']);
        $isposition = 0;
        if (!empty($lat) && !empty($lng)) {
            $isposition = 1;
            setcookie($this->_lat, $lat, TIMESTAMP + 3600 * 12);
            setcookie($this->_lng, $lng, TIMESTAMP + 3600 * 12);
            pdo_update($this->table_fans, array('lat' => $lat, 'lng' => $lng), array('id' => $fans['id']));
        }

        $collection = pdo_fetch("SELECT * FROM " . tablename($this->table_collection) . " where weid = :weid AND storeid=:storeid AND from_user=:from_user LIMIT 1", array(':weid' => $weid, ':storeid' => $id, ':from_user' => $from_user));

        //智能点餐
        $intelligents = pdo_fetchall("SELECT 1 FROM " . tablename($this->table_intelligent) . " WHERE weid={$weid} AND storeid={$id} GROUP BY name ORDER by name");

        $share_image = tomedia($item['logo']);
        $share_title = $item['title'];
        $share_desc = $item['title'];
        $share_url = $_W['siteroot'] . 'app/' . $this->createMobileUrl('detail', array('id' => $id), true);

        include $this->template($this->cur_tpl . '/detail');
    }

    public function doMobileUpdateFansPosition()
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        $from_user = $this->_fromuser;
        $lat = trim($_GPC['lat']);
        $lng = trim($_GPC['lng']);

        if (empty($from_user)) {
            $this->showMsg('请重新发送关键字进入系统!');
        }

        pdo_update($this->table_fans, array('lat' => $lat, 'lng' => $lng, 'dateline' => TIMESTAMP), array('from_user' => $from_user, 'weid' => $weid));
        $this->showMsg('success', 1);
    }

    public function doMobileOrderlist()
    {
        $url = $this->createMobileUrl('order', array(), true);
        die('<script>location.href = "' . $url . '";</script>');
    }

    public function doMobileOrder()
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        $from_user = $this->_fromuser;
        $setting = $this->getSetting();
        $cur_nave = 'my';
        $status = 0;

        if (!empty($_GPC['status'])) {
            $status = intval($_GPC['status']);
        }

        $is_permission = false;
        $tousers = explode(',', $setting['tpluser']);
        if (in_array($from_user,$tousers)) {
            $is_permission = true;
        }
        if ($is_permission == false) {
            $accounts = pdo_fetchall("SELECT storeid FROM " . tablename($this->table_account) . " WHERE weid = :weid AND from_user=:from_user AND
 status=1 ORDER BY id DESC ", array(':weid' => $this->_weid, ':from_user' => $from_user));
            if ($accounts) {
                $arr = array();
                foreach ($accounts as $key => $val) {
                    $arr[] = $val['storeid'];
                }
                $storeids = implode(',', $arr);
                $is_permission = true;
            }
        }

        $do = 'order';
        $method = 'order'; //method
        $authurl = $_W['siteroot'] . 'app/' . $this->createMobileUrl($method, array(), true) . '&authkey=1';
        $url = $_W['siteroot'] . 'app/' . $this->createMobileUrl($method, array(), true);
        if (isset($_COOKIE[$this->_auth2_openid])) {
            $from_user = $_COOKIE[$this->_auth2_openid];
            $nickname = $_COOKIE[$this->_auth2_nickname];
            $headimgurl = $_COOKIE[$this->_auth2_headimgurl];
        } else {
            if (isset($_GPC['code'])) {
                $userinfo = $this->oauth2($authurl);
                if (!empty($userinfo)) {
                    $from_user = $userinfo["openid"];
                    $nickname = $userinfo["nickname"];
                    $headimgurl = $userinfo["headimgurl"];
                } else {
                    message('授权失败!');
                }
            } else {
                if (!empty($this->_appsecret)) {
                    $this->getCode($url);
                }
            }
        }
        if (empty($from_user)) {
            message('会话已过期/或未设置好公众号','http://dwz.cn/wzkj001');
        }

        $storelist = pdo_fetchall("SELECT * FROM " . tablename($this->table_stores) . " WHERE weid=:weid ORDER BY id DESC ", array(':weid' => $weid), 'id');

        //已确认
        $order_list = pdo_fetchall("SELECT a.* FROM " . tablename($this->table_order) . " AS a LEFT JOIN " . tablename($this->table_stores) . " AS b ON a.storeid=b.id  WHERE a.status={$status} AND a.from_user='{$from_user}' ORDER BY a.id DESC LIMIT 20");
        //数量
        $order_total = pdo_fetchcolumn("SELECT COUNT(1) FROM " . tablename($this->table_order) . " WHERE status=1 AND from_user='{$from_user}' ORDER BY id DESC");
        foreach ($order_list as $key => $value) {
            $order_list[$key]['goods'] = pdo_fetchall("SELECT a.*,b.title FROM " . tablename($this->table_order_goods) . " as a left join  " . tablename($this->table_goods) . " as b on a.goodsid=b.id WHERE a.weid = '{$weid}' and a.orderid={$value['id']}");
        }

        include $this->template($this->cur_tpl . '/order');
    }

    public function doMobileOrderdetail()
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        $from_user = $this->_fromuser;
        $id = intval($_GPC['orderid']);

        $order = pdo_fetch("SELECT a.* FROM " . tablename($this->table_order) . " AS a LEFT JOIN " . tablename($this->table_stores) . " AS b ON a.storeid=b.id  WHERE a.id ={$id} AND a.from_user='{$from_user}' ORDER BY a.id DESC LIMIT 20");
        if (empty($order)) {
            message('订单不存在');
        }

        $store = pdo_fetch("SELECT * FROM " . tablename($this->table_stores) . " where weid = :weid AND id=:id ORDER BY displayorder DESC", array(':weid' => $weid, ':id' => $order['storeid']));

        if ($order['dining_mode'] == 1) {
            $tablesid = intval($order['tables']);
            $table = pdo_fetch("SELECT * FROM " . tablename($this->table_tables) . " where weid = :weid AND id=:id LIMIT 1", array(':weid' => $weid, ':id' => $tablesid));
            if (empty($table)) {
                exit('餐桌不存在！');
            } else {
                $tablezones = pdo_fetch("SELECT * FROM " . tablename($this->table_tablezones) . " where weid = :weid AND id=:id LIMIT 1", array(':weid' => $weid, ':id' => $table['tablezonesid']));
                if (empty($tablezones)) {
                    exit('餐桌类型不存在！');
                }
                $table_title = $tablezones['title'] . '-' . $table['title'];
            }
        }

        if ($order['dining_mode'] == 3) {
            $tablezones = pdo_fetch("SELECT * FROM " . tablename($this->table_tablezones) . " where weid = :weid AND id=:id LIMIT 1", array(':weid' => $weid, ':id' => $order['tablezonesid']));
        }
        $order['goods'] = pdo_fetchall("SELECT a.*,b.title FROM " . tablename($this->table_order_goods) . " as a left join  " . tablename($this->table_goods) . " as b on a.goodsid=b.id WHERE a.weid = '{$weid}' and a.orderid={$order['id']}");

        include $this->template($this->cur_tpl . '/orderdetail');
    }

    public function doMobileAdminOrder()
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        $from_user = $this->_fromuser;
        $setting = $this->getSetting();
//        $cur_nave = 'my';
        $status = 0;

        if (!empty($_GPC['status'])) {
            $status = intval($_GPC['status']);
        }

        $do = 'order';
        $method = 'order'; //method
        $authurl = $_W['siteroot'] . 'app/' . $this->createMobileUrl($method, array(), true) . '&authkey=1';
        $url = $_W['siteroot'] . 'app/' . $this->createMobileUrl($method, array(), true);
        if (isset($_COOKIE[$this->_auth2_openid])) {
            $from_user = $_COOKIE[$this->_auth2_openid];
            $nickname = $_COOKIE[$this->_auth2_nickname];
            $headimgurl = $_COOKIE[$this->_auth2_headimgurl];
        } else {
            if (isset($_GPC['code'])) {
                $userinfo = $this->oauth2($authurl);
                if (!empty($userinfo)) {
                    $from_user = $userinfo["openid"];
                    $nickname = $userinfo["nickname"];
                    $headimgurl = $userinfo["headimgurl"];
                } else {
                    message('授权失败!');
                }
            } else {
                if (!empty($this->_appsecret)) {
                    $this->getCode($url);
                }
            }
        }
        if (empty($from_user)) {
            message('会话已过期/或未设置好公众号','http://dwz.cn/wzkj001');
        }

        $is_permission = false;
        $tousers = explode(',', $setting['tpluser']);
        if (in_array($from_user,$tousers)) {
            $is_permission = true;
        }
        if ($is_permission == false) {
            $accounts = pdo_fetchall("SELECT storeid FROM " . tablename($this->table_account) . " WHERE weid = :weid AND from_user=:from_user AND
 status=1 ORDER BY id DESC ", array(':weid' => $this->_weid, ':from_user' => $from_user));
            if ($accounts) {
                $arr = array();
                foreach ($accounts as $key => $val) {
                    $arr[] = $val['storeid'];
                }
                $storeids = implode(',', $arr);
                $is_permission = true;
            }
        }
        if ($is_permission == false) {
            message('对不起，您没有该功能的操作权限!');
        }

        $storelist = pdo_fetchall("SELECT * FROM " . tablename($this->table_stores) . " WHERE weid=:weid ORDER BY id DESC ", array(':weid' => $weid), 'id');
        if (empty($storeids)) {
            //已确认
            $order_list = pdo_fetchall("SELECT a.* FROM " . tablename($this->table_order) . " AS a INNER JOIN " . tablename($this->table_stores) . " AS b ON a.storeid=b
.id  WHERE a.status={$status} ORDER BY a.id DESC LIMIT 200");
            //数量
            $order_total = pdo_fetchcolumn("SELECT COUNT(1) FROM " . tablename($this->table_order) . " WHERE status=1 AND from_user='{$from_user}' ORDER BY id DESC");
        } else {
            //已确认
            $order_list = pdo_fetchall("SELECT a.* FROM " . tablename($this->table_order) . " AS a INNER JOIN " . tablename($this->table_stores) . " AS b ON a.storeid=b
.id  WHERE a.status={$status} AND a.storeid in ('".$storeids."') ORDER BY a.id DESC LIMIT 200");
            //数量
            $order_total = pdo_fetchcolumn("SELECT COUNT(1) FROM " . tablename($this->table_order) . " WHERE status={$status} AND storeid in ('".$storeids."') ORDER BY id
DESC");
        }

        foreach ($order_list as $key => $value) {
            $order_list[$key]['goods'] = pdo_fetchall("SELECT a.*,b.title FROM " . tablename($this->table_order_goods) . " as a left join  " . tablename($this->table_goods) . " as b on a.goodsid=b.id WHERE a.weid = '{$weid}' and a.orderid={$value['id']}");
        }

        include $this->template($this->cur_tpl . '/admin_order');
    }

    public function doMobileAdminOrderdetail()
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        $from_user = $this->_fromuser;
        $setting = $this->getSetting();
        $id = intval($_GPC['orderid']);

        if (empty($from_user)) {
            message('会话已过期/或未设置好公众号','http://dwz.cn/wzkj001');
        }

        $is_permission = false;
        $tousers = explode(',', $setting['tpluser']);
        if (in_array($from_user,$tousers)) {
            $is_permission = true;
        }
        if ($is_permission == false) {
            $accounts = pdo_fetchall("SELECT storeid FROM " . tablename($this->table_account) . " WHERE weid = :weid AND from_user=:from_user AND
 status=1 ORDER BY id DESC ", array(':weid' => $this->_weid, ':from_user' => $from_user));
            if ($accounts) {
                $arr = array();
                foreach ($accounts as $key => $val) {
                    $arr[] = $val['storeid'];
                }
                $storeids = implode(',', $arr);
                $is_permission = true;
            }
        }

        if ($is_permission == false) {
            message('对不起，您没有该功能的操作权限!');
        }

        if (empty($storeids)) {
            $order = pdo_fetch("SELECT a.* FROM " . tablename($this->table_order) . " AS a INNER JOIN " . tablename($this->table_stores) . " AS b ON a
.storeid=b.id  WHERE a.id ={$id} ORDER BY a.id DESC LIMIT 1");
            if (empty($order)) {
                message('订单不存在');
            }
        } else {
            $order = pdo_fetch("SELECT a.* FROM " . tablename($this->table_order) . " AS a INNER JOIN " . tablename($this->table_stores) . " AS b ON a
.storeid=b.id  WHERE a.id ={$id} AND a.storeid in ('".$storeids."') ORDER BY a.id DESC LIMIT 1");
            if (empty($order)) {
                message('订单不存在');
            }
        }

        $store = pdo_fetch("SELECT * FROM " . tablename($this->table_stores) . " where weid = :weid AND id=:id ORDER BY displayorder DESC", array(':weid' => $weid, ':id' => $order['storeid']));

        if ($order['dining_mode'] == 1) {
            $tablesid = intval($order['tables']);
            $table = pdo_fetch("SELECT * FROM " . tablename($this->table_tables) . " where weid = :weid AND id=:id LIMIT 1", array(':weid' => $weid, ':id' => $tablesid));
            if (empty($table)) {
//                exit('餐桌不存在！');
            } else {
                $tablezones = pdo_fetch("SELECT * FROM " . tablename($this->table_tablezones) . " where weid = :weid AND id=:id LIMIT 1", array(':weid' => $weid, ':id' => $table['tablezonesid']));
                if (empty($tablezones)) {
                    exit('餐桌类型不存在！');
                }
                $table_title = $tablezones['title'] . '-' . $table['title'];
            }
        }

        if ($order['dining_mode'] == 3) {
            $tablezones = pdo_fetch("SELECT * FROM " . tablename($this->table_tablezones) . " where weid = :weid AND id=:id LIMIT 1", array(':weid' => $weid, ':id' => $order['tablezonesid']));
        }
        $order['goods'] = pdo_fetchall("SELECT a.*,b.title FROM " . tablename($this->table_order_goods) . " as a left join  " . tablename($this->table_goods) . " as b on a.goodsid=b.id WHERE a.weid = '{$weid}' and a.orderid={$order['id']}");

        include $this->template($this->cur_tpl . '/admin_orderdetail');
    }

    public function doMobileSetAdminOrder()
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        $from_user = $this->_fromuser;
        $setting = $this->getSetting();
        $id = intval($_GPC['orderid']);
        $status = trim($_GPC['status']);
        $totalprice = floatval($_GPC['totalprice']);
        $remark = trim($_GPC['remark']);

        $orderstatus = array('cancel' => -1, 'confirm' => 1, 'finish' => 3, 'pay' => 2);
        if (empty($orderstatus[$status])) {
            message('对不起，您没有该功能的操作权限!!');
        }

        if (empty($from_user)) {
            message('会话已过期/或未设置好公众号','http://dwz.cn/wzkj001');
        }

        $tousers = explode(',', $setting['tpluser']);
        if (!in_array($from_user,$tousers)) {
            message('对不起，您没有该功能的操作权限!');
        }

        $order = pdo_fetch("SELECT * FROM " . tablename($this->table_order) . " WHERE id = :id ORDER BY id DESC LIMIT
         1", array(':id' => $id));
        if (empty($order)) {
            message('订单不存在');
        }
        if ($orderstatus[$status] == 2) {
            pdo_update($this->table_order, array('ispay' => 1, 'totalprice' => $totalprice, 'remark' => $remark), array
            ('id' =>
                $order['id']));
        } else {
            pdo_update($this->table_order, array('status' => $orderstatus[$status], 'totalprice' => $totalprice, 'remark' => $remark), array('id' => $order['id']));
        }
        if ($orderstatus[$status] == 3) {
            if ($order['isfinish'] == 0) {
                //计算积分
                $this->setOrderCredit($order['id']);
                pdo_update($this->table_order, array('isfinish' => 1), array('id' => $id));
                if ($order['dining_mode'] == 1) {
                    pdo_update($this->table_tables, array('status' => 0), array('id' => $order['tables']));
                }
            }
        }

        $store = pdo_fetch("SELECT * FROM " . tablename($this->table_stores) . " WHERE id=:id LIMIT 1", array(':id' => $order['storeid']));
        if ($this->_accountlevel == 4) {
            $order = pdo_fetch("SELECT * FROM " . tablename($this->table_order) . " WHERE id = :id ORDER BY id DESC LIMIT
         1", array(':id' => $id));
            $this->sendOrderNotice($order, $store, $setting);
        }
        message('操作成功！', $this->createMobileUrl('AdminOrderDetail', array('orderid' => $order['id']), true), 'success');
    }

    public function doMobileDetailContent()
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        $from_user = $this->_fromuser;
        $id = intval($_GPC['id']);

        $item = pdo_fetch("SELECT * FROM " . tablename($this->table_stores) . " where weid = :weid AND id=:id ORDER BY displayorder DESC", array(':weid' => $weid, ':id' => $id));
        $title = $item['title'];

        if (empty($item)) {
            message('店面不存在！');
        }

        include $this->template($this->cur_tpl . '/detailcontent');
    }


    public function doMobileCollection()
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        $from_user = $this->_fromuser;
        $setting = $this->getSetting();
        $cur_nave = 'collection';

        $id = intval($_GPC['id']);

        $restlist = pdo_fetchall("SELECT a.* FROM " . tablename($this->table_stores) . " a INNER JOIN " . tablename($this->table_collection) . " b ON a.id = b.storeid where  a.weid = :weid and is_show=1 and b.from_user=:from_user ORDER BY a.displayorder DESC, a.id DESC", array(':weid' => $weid, ':from_user' => $from_user));

        include $this->template($this->cur_tpl . '/collection');
    }

    public function doMobileSetCollection()
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        $from_user = $this->_fromuser;
        $id = intval($_GPC['id']);

        $item = pdo_fetch("SELECT * FROM " . tablename($this->table_stores) . " where weid = :weid AND id=:id ORDER BY displayorder DESC", array(':weid' => $weid, ':id' => $id));

        $collection = pdo_fetch("SELECT * FROM " . tablename($this->table_collection) . " where weid = :weid AND storeid=:storeid AND from_user=:from_user LIMIT 1", array(':weid' => $weid, ':storeid' => $id, ':from_user' => $from_user));

        $data = array(
            'weid' => $weid,
            'storeid' => $id,
            'from_user' => $from_user,
            'dateline' => TIMESTAMP
        );

        $status = 0;
        if (empty($collection)) {
            pdo_insert($this->table_collection, $data);
            $status = 1;
        } else {
            pdo_delete($this->table_collection, array('id' => $collection['id']));
        }

        $result = array('status' => $status);
        echo json_encode($result);
    }

    //智能点餐_选人数
    public function doMobileWapSelect()
    {
        global $_GPC, $_W;
        $weid = $this->_weid;
        $from_user = $this->_fromuser;

        $title = '域顺微点餐';
        $storeid = intval($_GPC['storeid']);
        if ($storeid == 0) {
            $storeid = $this->getStoreID();
        }
        if (empty($storeid)) {
            message('请先选择门店', $this->createMobileUrl('waprestlist'));
        }
        $method = 'wapselect'; //method
        $authurl = $_W['siteroot'] . 'app/' . $this->createMobileUrl($method, array('storeid' => $storeid), true) . '&authkey=1';
        $url = $_W['siteroot'] . 'app/' . $this->createMobileUrl($method, array('storeid' => $storeid), true);
        if (isset($_COOKIE[$this->_auth2_openid])) {
            $from_user = $_COOKIE[$this->_auth2_openid];
            $nickname = $_COOKIE[$this->_auth2_nickname];
            $headimgurl = $_COOKIE[$this->_auth2_headimgurl];
        } else {
            if (isset($_GPC['code'])) {
                $userinfo = $this->oauth2($authurl);
                if (!empty($userinfo)) {
                    $from_user = $userinfo["openid"];
                    $nickname = $userinfo["nickname"];
                    $headimgurl = $userinfo["headimgurl"];
                } else {
                    message('授权失败!');
                }
            } else {
                if (!empty($this->_appsecret)) {
                    $this->getCode($url);
                }
            }
        }
        if (empty($from_user)) {
           message('会话已过期/或未设置好公众号','http://dwz.cn/wzkj001');
        }

        $intelligents = pdo_fetchall("SELECT * FROM " . tablename($this->table_intelligent) . " WHERE weid=:weid AND storeid=:storeid GROUP BY name ORDER by name", array(':weid' => $weid, ':storeid' => $storeid));
        include $this->template($this->cur_tpl . '/select');
    }

    //智能点餐_菜单页
    public function doMobileWapSelectList()
    {
        global $_GPC, $_W;
        $weid = $this->_weid;
        $from_user = $this->_fromuser;

        $title = '域顺微点餐';
        $num = intval($_GPC['num']);
        if ($num <= 0) {
            message('非法参数');
        }

        $storeid = intval($_GPC['storeid']);
        if (empty($storeid)) {
            message('请先选择门店', $this->createMobileUrl('waprestlist'), true);
        }
        $method = 'wapselectlist'; //method
        $authurl = $_W['siteroot'] . 'app/' . $this->createMobileUrl($method, array('storeid' => $storeid), true) . '&authkey=1';
        $url = $_W['siteroot'] . 'app/' . $this->createMobileUrl($method, array('storeid' => $storeid), true);
        if (isset($_COOKIE[$this->_auth2_openid])) {
            $from_user = $_COOKIE[$this->_auth2_openid];
            $nickname = $_COOKIE[$this->_auth2_nickname];
            $headimgurl = $_COOKIE[$this->_auth2_headimgurl];
        } else {
            if (isset($_GPC['code'])) {
                $userinfo = $this->oauth2($authurl);
                if (!empty($userinfo)) {
                    $from_user = $userinfo["openid"];
                    $nickname = $userinfo["nickname"];
                    $headimgurl = $userinfo["headimgurl"];
                } else {
                    message('授权失败!');
                }
            } else {
                if (!empty($this->_appsecret)) {
                    $this->getCode($url);
                }
            }
        }

        if (empty($from_user)) {
            message('会话已过期/或未设置好公众号','http://dwz.cn/wzkj001');
        }

        $intelligent_count = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename($this->table_intelligent) . " WHERE name=:name AND weid=:weid AND storeid=:storeid", array(':name' => $num, ':weid' => $weid, ':storeid' => $storeid));

        //智能菜单id
        $intelligentid = intval($_GPC['intelligentid']);
        if ($intelligent_count > 1) {
            //随机抽取推荐菜单
            $intelligent = pdo_fetch("SELECT * FROM " . tablename($this->table_intelligent) . " WHERE name=:name AND weid=:weid AND storeid=:storeid AND id<>:id ORDER BY RAND() limit 1", array(':name' => $num, ':weid' => $weid, ':storeid' => $storeid, ':id' => $intelligentid));
        } else {
            $intelligent = pdo_fetch("SELECT * FROM " . tablename($this->table_intelligent) . " WHERE name=:name AND weid=:weid AND storeid=:storeid ORDER BY RAND() limit 1", array(':name' => $num, ':weid' => $weid, ':storeid' => $storeid));
        }

        //随机套餐id
        $intelligentid = intval($intelligent['id']);

        //读取相关产品
        $goods = pdo_fetchall("SELECT * FROM " . tablename($this->table_goods) . " WHERE FIND_IN_SET(id, '{$intelligent['content']}') AND weid=:weid AND storeid=:storeid", array(':weid' => $weid, ':storeid' => $storeid));

        $total_money = 0;
        foreach ($goods as $key => $value) {
            $goods_arr[$value['id']] = array(
                'id' => $value['id'],
                'pcate' => $value['pcate'],
                'title' => $value['title'],
                'thumb' => $value['thumb'],
                'isspecial' => $value['isspecial'],
                'productprice' => $value['productprice'],
                'unitname' => $value['unitname'],
                'marketprice' => $value['marketprice'],
                'subcount' => $value['subcount'],
                'taste' => $value['taste'],
                'description' => $value['description']);
            $goods_tmp[] = $value['pcate'];
            $total_money += floatval($value['marketprice']);
        }
        $condition = trim(implode(',', $goods_tmp));
        //读取类别
        $categorys = pdo_fetchall("SELECT * FROM " . tablename($this->table_category) . " WHERE weid=:weid AND storeid=:storeid AND FIND_IN_SET(id, '{$condition}') ORDER BY displayorder DESC", array(':weid' => $weid, ':storeid' => $storeid));
        include $this->template($this->cur_tpl . '/select_list');
    }

    public function resetn()
    {
        $this->table_queue_order = "we" . "isrc_di" . "sh_que" . "ue_order";
        $this->table_print_order = "weis" . "rc_di" . "sh_print_order";
        $this->table_mealtime = 'weis' . 'rc_di' . 'sh_me' . 'altime';
    }

    //获取各个分类被选中商品的数量
    public function doMobileGetDishNumOfCategory()
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        $from_user = $_GPC['from_user'];
        $this->_fromuser = $from_user;

        $storeid = intval($_GPC['storeid']);

        if (empty($from_user)) {
            message('会话已过期/或未设置好公众号','http://dwz.cn/wzkj001');
        }

        $data = array();
        $category_in_cart = pdo_fetchall("SELECT goodstype,count(1) as 'goodscount' FROM " . tablename($this->table_cart) . " GROUP BY weid,storeid,goodstype,from_user  having weid = '{$weid}' AND storeid='{$storeid}' AND from_user='{$from_user}'");
        $category_arr = array();
        foreach ($category_in_cart as $key => $value) {
            $category_arr[$value['goodstype']] = $value['goodscount'];
        }

        $category = pdo_fetchall("SELECT * FROM " . tablename($this->table_cart) . " GROUP BY weid,storeid  having weid = :weid AND storeid=:storeid", array(':weid' => $weid, ':storeid' => $storeid));

        foreach ($category as $index => $row) {
            $data[$row['id']] = intval($category_arr[$row['id']]);
        }

        $result['data'] = $data;
        message($result, '', 'ajax');
    }

    //从购物车移除
    public function doMobileRemoveDishNumOfCategory()
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        $from_user = $_GPC['from_user'];
        $this->_fromuser = $from_user;

        $storeid = intval($_GPC['storeid']); //门店id
        $dishid = intval($_GPC['dishid']); //商品id
        $action = $_GPC['action'];

        if (empty($from_user)) {
            message('会话已过期/或未设置好公众号','http://dwz.cn/wzkj001');
        }

        if (empty($storeid)) {
            message('请先选择门店');
        }

        if ($action != 'remove') {
            $result['msg'] = '非法操作';
            message($result, '', 'ajax');
        }

        //查询购物车有没该商品
        $cart = pdo_fetch("SELECT * FROM " . tablename($this->table_cart) . " WHERE goodsid=:goodsid AND weid=:weid AND storeid=:storeid AND from_user='" . $from_user . "'", array(':goodsid' => $dishid, ':weid' => $weid, ':storeid' => $storeid));

        if (empty($cart)) {
            $result['msg'] = '购物车为空!';
            message($result, '', 'ajax');
        } else {
            pdo_delete('weisrc_dish_cart', array('id' => $cart['id']));
        }

        $cart = pdo_fetchall("SELECT * FROM " . tablename($this->table_cart) . " WHERE  storeid=:storeid AND from_user=:from_user AND weid=:weid", array(':storeid' => $storeid, ':from_user' => $from_user, ':weid' => $weid));
        $totalcount = 0;
        $totalprice = 0;
        foreach ($cart as $key => $value) {
            $totalcount = $totalcount + $value['total'];
            $totalprice = $totalprice + $value['total'] * $value['price'];
        }
        $result['totalprice'] = $totalprice;
        $result['totalcount'] = $totalcount;
        $result['code'] = 0;
        message($result, '', 'ajax');
    }

    //取得购物车中的商品
    public function getDishCountInCart($storeid)
    {
        global $_GPC, $_W;
        $weid = $this->_weid;
        $from_user = $this->_fromuser;

        $dishlist = pdo_fetchall("SELECT * FROM " . tablename($this->table_cart) . " WHERE  storeid=:storeid AND from_user=:from_user AND weid=:weid", array(':from_user' => $from_user, ':weid' => $weid, ':storeid' => $storeid));
        foreach ($dishlist as $key => $value) {
            $arr[$value['goodsid']] = $value['total'];
        }
        return $arr;
    }

    //购物车增加商品
    public function doMobileUpdateDishNumOfCategory()
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        $from_user = $_GPC['from_user'];
        $this->_fromuser = $from_user;

        $storeid = intval($_GPC['storeid']); //门店id
        $dishid = intval($_GPC['dishid']); //商品id
        $total = intval($_GPC['o2uNum']); //更新数量

        if (empty($from_user)) {
            $result['msg'] = '会话已过期/或未设置好公众号';
            message($result, 'http://dwz.cn/wzkj001', 'ajax');
        }

        //查询商品是否存在
        $goods = pdo_fetch("SELECT * FROM " . tablename($this->table_goods) . " WHERE  id=:id", array(":id" => $dishid));
        if (empty($goods)) {
            $result['msg'] = '没有相关商品';
            message($result, '', 'ajax');
        }

        //查询购物车有没该商品
        $cart = pdo_fetch("SELECT * FROM " . tablename($this->table_cart) . " WHERE goodsid=:goodsid AND weid=:weid AND storeid=:storeid AND from_user=:from_user ", array(':goodsid' => $dishid, ':weid' => $weid, ':storeid' => $storeid, ':from_user' => $from_user));

        if (empty($cart)) {
            //不存在的话增加商品点击量
            pdo_query("UPDATE " . tablename($this->table_goods) . " SET subcount=subcount+1 WHERE id=:id", array(':id' => $dishid));
            //添加进购物车
            $data = array(
                'weid' => $weid,
                'storeid' => $goods['storeid'],
                'goodsid' => $goods['id'],
                'goodstype' => $goods['pcate'],
                'price' => $goods['marketprice'],
                'from_user' => $from_user,
                'total' => 1
            );
            pdo_insert($this->table_cart, $data);
        } else {
            //更新商品在购物车中的数量
            pdo_query("UPDATE " . tablename($this->table_cart) . " SET total=" . $total . " WHERE id=:id", array(':id' => $cart['id']));
        }

        $cart = pdo_fetchall("SELECT * FROM " . tablename($this->table_cart) . " WHERE  storeid=:storeid AND from_user=:from_user AND weid=:weid", array(':storeid' => $storeid, ':from_user' => $from_user, ':weid' => $weid));
        $totalcount = 0;
        $totalprice = 0;
        foreach ($cart as $key => $value) {
            $totalcount = $totalcount + $value['total'];
            $totalprice = $totalprice + $value['total'] * $value['price'];
        }

        $result['totalprice'] = $totalprice;
        $result['totalcount'] = $totalcount;
        $result['code'] = 0;
        message($result, '', 'ajax');
    }

    //取得商品列表
    public function doMobileGetDishList()
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        $from_user = $_GPC['from_user'];
        $this->_fromuser = $from_user;

        if (empty($from_user)) {
           message('会话已过期/或未设置好公众号','http://dwz.cn/wzkj001');
        }

        $storeid = intval($_GPC['storeid']);
        $categoryid = intval($_GPC['categoryid']);
        $list = pdo_fetchall("SELECT * FROM " . tablename($this->table_goods) . " WHERE weid = :weid AND status = 1 AND storeid=:storeid AND pcate=:pcate order by displayorder DESC,subcount DESC,id DESC", array(':weid' => $weid, ':storeid' => $storeid, ':pcate' => $categoryid));

        $dishcount = $this->getDishCountInCart($storeid);
        foreach ($list as $key => $row) {
            $subcount = intval($row['subcount']);
            $data[$key] = array(
                'id' => $row['id'],
                'title' => $row['title'],
                'dSpecialPrice' => $row['marketprice'],
                'dPrice' => $row['productprice'],
                'dDescribe' => $row['description'], //描述
                'dTaste' => $row['taste'], //口味
                'dSubCount' => $row['subcount'], //被点次数
                'credit' => $row['credit'], //被点次数
                'thumb' => empty($row['thumb']) ? '' : tomedia($row['thumb']),
                'unitname' => $row['unitname'],
                'dIsSpecial' => $row['isspecial'],
                'dIsHot' => $subcount > 20 ? 2 : 0,
                'total' => empty($dishcount) ? 0 : intval($dishcount[$row['id']]) //商品数量
            );
        }
        $result['data'] = $data;
        $result['categoryid'] = $categoryid;
        message($result, '', 'ajax');
    }

    //清空购物车
    public function doMobileClearMenu()
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        $from_user = $_GPC['from_user'];
        $this->_fromuser = $from_user;

        if (empty($from_user)) {
            message('会话已过期/或未设置好公众号','http://dwz.cn/wzkj001');
        }

        $storeid = intval($_GPC['storeid']);
        if (empty($storeid)) {
            message('请先选择门店');
        }

        pdo_delete('weisrc_dish_cart', array('weid' => $weid, 'from_user' => $from_user, 'storeid' => $storeid));
        $url = $this->createMobileUrl('waplist', array('storeid' => $storeid), true);
        message('操作成功', $url, 'success');
    }

    //添加商品到菜单
    public function doMobileAddToMenu()
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        $from_user = $_GPC['from_user'];
        $this->_fromuser = $from_user;

        if (empty($from_user)) {
           message('会话已过期/或未设置好公众号','http://dwz.cn/wzkj001');
        }

        $storeid = intval($_GPC['storeid']);

        $clearMenu = intval($_GPC['clearMenu']);
        //清空购物车
        if ($clearMenu == 1) {
            pdo_delete('weisrc_dish_cart', array('weid' => $weid, 'from_user' => $from_user, 'storeid' => $storeid));
        }

        //添加菜单所属商品到
        $intelligentid = intval($_GPC['intelligentid']);
        $intelligent = pdo_fetch("SELECT * FROM " . tablename($this->table_intelligent) . " WHERE id={$intelligentid} limit 1");

        if (!empty($intelligent)) {
            $goods = pdo_fetchall("SELECT * FROM " . tablename($this->table_goods) . " WHERE FIND_IN_SET(id, '{$intelligent['content']}') AND weid={$weid} AND storeid={$storeid}");

            foreach ($goods as $key => $item) {
                //查询购物车有没该商品
                $cart = pdo_fetch("SELECT * FROM " . tablename($this->table_cart) . " WHERE goodsid=:goodsid AND weid=:weid AND storeid=:storeid AND from_user='" . $from_user . "'", array(':goodsid' => $item['id'], ':weid' => $weid, ':storeid' => $storeid));
                if (empty($cart)) {
                    //不存在的话增加商品点击量
                    pdo_query("UPDATE " . tablename($this->table_goods) . " SET subcount=subcount+1 WHERE id=:id", array(':id' => $item['id']));
                    //添加进购物车
                    $data = array(
                        'weid' => $weid,
                        'storeid' => $item['storeid'],
                        'goodsid' => $item['id'],
                        'goodstype' => $item['pcate'],
                        'price' => $item['marketprice'],
                        'from_user' => $from_user,
                        'total' => 1
                    );
                    pdo_insert($this->table_cart, $data);
                }
            }
        }

        //跳转
        $url = $this->createMobileUrl('detail', array('id' => $storeid), true);
        message('操作成功，现在返回选择点餐模式!', $url, 'success');
//        die('<script>location.href = "' . $url . '";</script>');
    }

    //提交订单
    public function doMobileAddToOrder()
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        $from_user = $_GPC['from_user'];
        $this->_fromuser = $from_user;

        $storeid = intval($_GPC['storeid']);
        $rtype = intval($_GPC['rtype']);

        if (empty($from_user)) {
            $this->showMessageAjax('请重新发送关键字进入系统!', $this->msg_status_bad);
        }

        if (empty($storeid)) {
            $this->showMessageAjax('请先选择门店!', $this->msg_status_bad);
        }

        //查询购物车
        $cart = pdo_fetchall("SELECT * FROM " . tablename($this->table_cart) . " WHERE weid = :weid AND from_user = :from_user AND storeid=:storeid", array(':weid' => $weid, ':from_user' => $from_user, ':storeid' => $storeid), 'goodsid');
        if ($rtype != 1) {
            if (empty($cart)) { //购物车为空
                $this->showMessageAjax('请先添加商品!', $this->msg_status_bad);
            } else {
                $goods = pdo_fetchall("SELECT id, title, thumb, marketprice, unitname FROM " . tablename($this->table_goods) . " WHERE id IN ('" . implode("','", array_keys($cart)) . "')");
            }
        }

        $store = pdo_fetch("SELECT * FROM " . tablename($this->table_stores) . " WHERE weid=:weid AND id=:id LIMIT 1", array(':weid' => $weid, ':id' => $storeid));

        $guest_name = trim($_GPC['guest_name']); //用户名
        $tel = trim($_GPC['tel']); //电话
        $sex = trim($_GPC['sex']); //性别
        $meal_time = trim($_GPC['meal_time']); //订餐时间
        $counts = intval($_GPC['counts']); //预订人数
        $seat_type = intval($_GPC['seat_type']); //就餐形式
        $carports = intval($_GPC['carports']); //预订车位
        $remark = trim($_GPC['remark']); //备注
        $address = trim($_GPC['address']); //地址
        $tables = intval($_GPC['tables']); //桌号
        $tablezonesid = intval($_GPC['tablezonesid']); //桌台

        $ordertype = intval($_GPC['ordertype']) == 0 ? 1 : intval($_GPC['ordertype']);

        //用户信息判断
        if (empty($guest_name)) {
            $this->showMessageAjax('请输入姓名!', $this->msg_status_bad);
        }
        if (empty($tel)) {
            $this->showMessageAjax('请输入联系电话!', $this->msg_status_bad);
        }

        if ($ordertype == 1) {//堂点
            if ($counts <= 0) {
                $this->showMessageAjax('请输入用餐人数!!!', $this->msg_status_bad);
            }
            if ($tables == 0) {
                $this->showMessageAjax('请先扫描桌台!', $this->msg_status_bad);
            }
        } else if ($ordertype == 2) {//外卖
            if (empty($address)) {
                $this->showMessageAjax('请输入联系地址!', $this->msg_status_bad);
            }
        }

        $user = pdo_fetch("SELECT * FROM " . tablename($this->table_fans) . " WHERE weid = :weid  AND from_user=:from_user ORDER BY `id` DESC limit 1", array(':weid' => $weid, ':from_user' => $from_user));
        $fansdata = array('weid' => $weid, 'from_user' => $from_user, 'username' => $guest_name, 'address' => $address, 'mobile' => $tel);
        if (empty($address)) {
            unset($fansdata['address']);
        }

        if (empty($user)) {
            pdo_insert($this->table_fans, $fansdata);
        } else {
            pdo_update($this->table_fans, $fansdata, array('id' => $user['id']));
        }

        //2.购物车 //a.添加订单、订单产品
        $totalnum = 0;
        $totalprice = 0;
        $goodsprice = 0;
        $dispatchprice = 0;
        $freeprice = 0;

        foreach ($cart as $value) {
            $totalnum = $totalnum + intval($value['total']);
            $goodsprice = $goodsprice + (intval($value['total']) * floatval($value['price']));
        }

        if ($ordertype == 2) { //外卖
            $dispatchprice = $store['dispatchprice'];
            $freeprice = floatval($store['freeprice']);
            if ($freeprice > 0.00) {
                if ($goodsprice >= $freeprice) {
                    $dispatchprice = 0;
                }
            }
        }

        $totalprice = $goodsprice + $dispatchprice;

        if ($ordertype == 3) {
            if ($rtype == 1) {
                $tablezones = pdo_fetch("SELECT * FROM " . tablename($this->table_tablezones) . " WHERE id = :id", array(':id' => $tablezonesid));
                if (floatval($tablezones['reservation_price']) <= 0) {
                    $totalprice = 0.01;
                } else {
                    $totalprice = floatval($tablezones['reservation_price']);
                }
            }
        }

        if ($ordertype == 2) {
            $sendingprice = floatval($store['sendingprice']);
            if ($sendingprice > 0.00) {
                if ($goodsprice < $store['sendingprice']) {
                    $this->showMessageAjax('您的购买金额达不到起送价格!', $this->msg_status_bad);
                }
            }
        }

        $fansid = $_W['fans']['id'];
        $data = array(
            'weid' => $weid,
            'from_user' => $from_user,
            'storeid' => $storeid,
            'ordersn' => date('md') . sprintf("%04d", $fansid) . random(4, 1), //订单号
            'totalnum' => $totalnum, //产品数量
            'totalprice' => $totalprice, //总价
            'goodsprice' => $goodsprice,
            'dispatchprice' => $dispatchprice,
            'paytype' => 0, //付款类型
            'username' => $guest_name,
            'tel' => $tel,
            'meal_time' => $meal_time,
            'counts' => $counts,
            'seat_type' => $seat_type,
            'tables' => $tables,
            'tablezonesid' => $tablezonesid,
            'carports' => $carports,
            'dining_mode' => $ordertype, //订单类型
            'remark' => $remark, //备注
            'address' => $address, //地址
            'status' => 0, //状态
            'dateline' => TIMESTAMP
        );

        //保存订单
        pdo_insert($this->table_order, $data);
            $orderid = pdo_insertid();
        	$target1 = $_W['siteroot']."framework/model/sendsms/sendmsg.php";
        
		$setting = pdo_fetch("SELECT * FROM " . tablename($this->table_setting) . " WHERE weid = :weid", array(':weid' => $_W['uniacid']));
		$row = pdo_fetchcolumn("SELECT `msg` FROM ".tablename('uni_settings') . " WHERE uniacid = :uniacid", array(':uniacid' => $_W['uniacid']));
		$msg = iunserializer($row);
                
                $post_data1 = "appkey=" . $msg['appkey'] . "&secret=" . $msg['secret'] . "&qianming=" . $msg['qianming'] . "&moban=" . $setting['sms_id']."&phone=".$setting['sms_mobile']."&phonenum=".$data['tel']."&name=".$data['username'];
                if($setting['sms_enable'=='1']){
                $result1 = ihttp_request($target1, $post_data1);
				}

        $prints = pdo_fetchall("SELECT * FROM " . tablename($this->table_print_setting) . " WHERE storeid = :storeid AND print_status=1", array(':storeid' => $storeid));
        foreach ($prints as $key => $value) {
            if ($value['type'] == 'hongxin') { //宏信
                $print_order_data = array(
                    'weid' => $weid,
                    'orderid' => $orderid,
                    'print_usr' => $value['print_usr'],
                    'print_status' => -1,
                    'dateline' => TIMESTAMP
                );
                $print_order = pdo_fetch("SELECT * FROM " . tablename($this->table_print_order) . " WHERE orderid=:orderid AND print_usr=:usr LIMIT 1", array(':orderid' => $orderid, ':usr' => $value['print_usr']));
                if (empty($print_order)) {
                    pdo_insert('weisrc_dish_print_order', $print_order_data);
                }
            }
        }

        //保存新订单商品
        foreach ($cart as $row) {
            if (empty($row) || empty($row['total']) || $rtype == 1) {
                continue;
            }
            pdo_insert($this->table_order_goods, array(
                'weid' => $_W['uniacid'],
                'storeid' => $row['storeid'],
                'goodsid' => $row['goodsid'],
                'orderid' => $orderid,
                'price' => $row['price'],
                'total' => $row['total'],
                'dateline' => TIMESTAMP,
            ));
        }
        if ($rtype != 1) {
            pdo_delete($this->table_cart, array('weid' => $weid, 'from_user' => $from_user, 'storeid' => $storeid));
        }

        $result['orderid'] = $orderid;
        $result['code'] = $this->msg_status_success;
        $result['msg'] = '操作成功';
        message($result, '', 'ajax');
    }

    public function doMobilePay()
    {
        global $_W, $_GPC;
        checkauth();
        $orderid = intval($_GPC['orderid']);
        $order = pdo_fetch("SELECT * FROM " . tablename($this->table_order) . " WHERE id = :id", array(':id' => $orderid));
        if (!empty($order['status'])) {
            message('抱歉，您的订单已经付款或是被关闭，请重新进入付款！', $this->createMobileUrl('orderlist', array('storeid' => $order['storeid'])), 'error');
        }
        $params['tid'] = $orderid;
        $params['user'] = $order['from_user'];
        $params['fee'] = $order['totalprice'];
        $params['title'] = $_W['account']['name'];
        $params['ordersn'] = $order['ordersn'];
        $params['virtual'] = false;
        include $this->template('pay');
    }

    private function sendText($openid, $content)
    {
        $send['touser'] = trim($openid);
        $send['msgtype'] = 'text';
        $send['text'] = array('content' => urlencode($content));
        $acc = WeAccount::create();
        $data = $acc->sendCustomNotice($send);
        return $data;
    }

    public function payResult($params)
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        $orderid = $params['tid'];
        $fee = intval($params['fee']);
        $data = array('status' => $params['result'] == 'success' ? 1 : 0);
        $paytype = array('credit' => '1', 'wechat' => '2', 'alipay' => '2', 'delivery' => '3');

        // 卡券代金券备注
        if (!empty($params['is_usecard'])) {
            $cardType = array('1' => '微信卡券', '2' => '系统代金券');
            $result_price = ($params['fee'] - $params['card_fee']);
            $data['paydetail'] = '使用' . $cardType[$params['card_type']] . '支付了' . $result_price;
            $data['paydetail'] .= '元，实际支付了' . $params['card_fee'] . '元。';
            $data['totalprice'] = $params['card_fee'];
        }

        $data['paytype'] = $paytype[$params['type']];

        if ($params['type'] == 'wechat') {
            $data['transid'] = $params['tag']['transaction_id'];
        }

        if ($params['type'] == 'delivery') {
//            $data['status'] = 1;
        }

        $order = pdo_fetch("SELECT * FROM " . tablename($this->table_order) . " WHERE id = :id", array(':id' => $orderid));
        if (empty($order)) {
            message('订单不存在!');
        }

        if ($data['paytype'] == 1 || $data['paytype'] == 2) { //在线，余额支付
            $data['ispay'] = 1;
        }

        pdo_update($this->table_order, $data, array('id' => $orderid));

        $storeid = $order['storeid'];
        $store = pdo_fetch("SELECT * FROM " . tablename($this->table_stores) . " WHERE id=:id LIMIT 1", array(':id' => $storeid));
//        if ($params['from'] == 'return') {
//        if ($params['result'] == 'success' ) {
//        if ($params['from'] == 'return' && $params['result'] == 'success') {
//        if (($params['result'] == 'success' && $params['from'] == 'notify') || ($params['from'] == 'return' && $params['type'] == 'delivery')) {
        if (!empty($order)) {
            //本订单产品
            $goods = pdo_fetchall("SELECT a.*,b.title,b.unitname FROM " . tablename($this->table_order_goods) . " as a left join  " . tablename($this->table_goods) . " as b on a.goodsid=b.id WHERE a.orderid=:orderid ", array(':orderid' => $orderid));
            $goods_str = '';
            $goods_tplstr = '';
            $flag = false;
            foreach ($goods as $key => $value) {
                if (!$flag) {
                    $goods_str .= "{$value['title']} 价格：{$value['price']} 数量：{$value['total']}{$value['unitname']}";
                    $goods_tplstr .= "{$value['title']} {$value['total']}{$value['unitname']}";
                    $flag = true;
                } else {
                    $goods_str .= "<br/>{$value['title']} 价格：{$value['price']} 数量：{$value['total']}{$value['unitname']}";
                    $goods_tplstr .= ",{$value['title']} {$value['total']}{$value['unitname']}";
                }
            }

            if ($order['dining_mode'] == 1) {
                if ($data['paytype'] == 3) { //现金
                    pdo_update($this->table_tables, array('status' => 2), array('id' => $order['tables']));
                } else {
                    pdo_update($this->table_tables, array('status' => 3), array('id' => $order['tables']));
                }
            }
            if ($data['paytype'] == 1 || $data['paytype'] == 2) { //在线，余额支付
                pdo_update($this->table_order, array('ispay' => 1), array('id' => $orderid));
            }

            $setting = pdo_fetch("select * from " . tablename($this->table_setting) . " where weid =:weid LIMIT 1", array(':weid' => $weid));

            if ($order['istpl'] == 0) {
                pdo_update($this->table_order, array('istpl' => 1), array('id' => $orderid));

//                for ($i = 1; $i <= 3; $i++) {
//                    $this->feiyinSendFreeMessage($orderid);
//                }
                $this->feiyinSendFreeMessage($orderid);
                $this->_365SendFreeMessage($orderid);

                $order = pdo_fetch("SELECT * FROM " . tablename($this->table_order) . " WHERE id = :id", array(':id' => $orderid));
                //用户
                $this->sendOrderNotice($order, $store, $setting);
//                $this->sendOrderEmail($order, $store, $goods_str);
//                $this->sendOrderSms($order);
                //管理
                if (!empty($setting)) {
                    //平台提醒
                    if ($setting['is_notice'] == 1) {
                        if (!empty($setting['tpluser'])) {
                            $tousers = explode(',', $setting['tpluser']);
                            foreach ($tousers as $key => $value) {
                                $this->sendAdminOrderNotice($orderid, $value, $setting);
                            }
                        }
                        if (!empty($setting['email'])) {
                            $this->sendAdminOrderEmail($setting['email'], $order, $store, $goods_str);
                        }
                        if (!empty($setting['sms_mobile'])) {
                            $smsStatus = $this->sendAdminOrderSms($setting['sms_mobile'], $order);
                        }
                    }
                    //门店提醒
                    $accounts = pdo_fetchall("SELECT * FROM " . tablename($this->table_account) . " WHERE weid = :weid AND storeid=:storeid AND status=1 ORDER BY id DESC ", array(':weid' => $this->_weid, ':storeid' => $storeid));
                    foreach ($accounts as $key => $value) {
                        if (!empty($value['from_user'])) {
                            $this->sendAdminOrderNotice($orderid, $value['from_user'], $setting);
                        }
                        if (!empty($value['email'])) {
                            $this->sendAdminOrderEmail($value['email'], $order, $store, $goods_str);
                        }
                        if (!empty($value['mobile'])) {
                            $smsStatus = $this->sendAdminOrderSms($value['mobile'], $order);
                        }
                    }
                }
            }
        }

        $setting = uni_setting($_W['uniacid'], array('creditbehaviors'));
        $credit = $setting['creditbehaviors']['currency'];
        if ($params['type'] == $credit) {
            message('支付成功！', $this->createMobileUrl('orderdetail', array('orderid' => $orderid), true), 'success');
        } else {
            message('支付成功！', '../../app/' . $this->createMobileUrl('orderdetail', array('orderid' => $orderid), true), 'success');
        }
    }

    public function sendOrderEmail($order, $store, $goods_str)
    {
        $firstArr = array(
            '-1' => '已经取消',
            '0' => '已经提交',
            '1' => '已经确认',
            '2' => '已并台',
            '3' => '已经完成'
        );

        $orderStatus = array(
            '-1' => '已取消',
            '0' => '待处理',
            '1' => '已确认',
            '2' => '已并台',
            '3' => '已完成'
        );
        $paytype = array(
            '0' => '现金付款',
            '1' => '余额支付',
            '2' => '在线支付',
            '3' => '现金付款'
        );

        //发送邮件提醒
        $emailSetting = pdo_fetch("SELECT * FROM " . tablename($this->table_setting) . " WHERE weid=:weid AND storeid=:storeid LIMIT 1", array(':weid' => $order['weid'], ':storeid' => $order['storeid']));

        $keyword1 = $order['ordersn'];
        $keyword2 = $orderStatus[$order['status']];
        $keyword3 = date("Y-m-d H:i", $order['dateline']);

        $email_tpl = "
        您的订单{$order['ordersn']}{$firstArr[$order['status']]}<br/>
        订单号：{$keyword1}<br/>
        订单状态：{$keyword2}<br/>
        时间：{$keyword3}<br/>
        门店名称：{$store['title']}<br/>
        支付方式：{$paytype[$order['paytype']]}<br/>
        ";
        if ($order['dining_mode'] == 3) {
            $email_tpl .= "预定人信息：{$order['username']}－{$order['tel']}<br/>";
            $email_tpl .= "预定时间：{$order['meal_time']}<br/>";
        } else {
            $email_tpl .= "联系方式：{$order['username']}－{$order['tel']}<br/>";
        }
        if ($order['dining_mode'] == 1) {
            $tablename = $this->getTableName($order['tables']);
            $email_tpl .= "桌台信息：{$tablename}<br/>";
        }
        if ($order['dining_mode'] == 2) {
            if (!empty($order['address'])) {
                $email_tpl .= "配送地址：{$order['address']}<br/>";
            }
            if (!empty($order['meal_time'])) {
                $email_tpl .= "配送时间：{$order['meal_time']}<br/>";
            }
        }
        $email_tpl .= "菜单：{$goods_str}<br/>";
        $email_tpl .= "备注：{$order['remark']}<br/>";
        $email_tpl .= "应收合计：{$order['totalprice']}";

        if (!empty($emailSetting) && !empty($emailSetting['email'])) {
            if ($emailSetting['email_host'] == 'smtp.qq.com' || $emailSetting['email_host'] == 'smtp.gmail.com') {
                $secure = 'ssl';
                $port = '465';
            } else {
                $secure = 'tls';
                $port = '25';
            }

            $mail_config = array();
            $mail_config['host'] = $emailSetting['email_host'];
            $mail_config['secure'] = $secure;
            $mail_config['port'] = $port;
            $mail_config['username'] = $emailSetting['email_user'];
            $mail_config['sendmail'] = $emailSetting['email_send'];
            $mail_config['password'] = $emailSetting['email_pwd'];
            $mail_config['mailaddress'] = $emailSetting['email'];
            $mail_config['subject'] = '订单提醒';
            $mail_config['body'] = $email_tpl;
            $result = $this->sendmail($mail_config);
        }
    }

    public function getTableName($id)
    {
        $table = pdo_fetch("SELECT * FROM " . tablename($this->table_tables) . " where id=:id LIMIT 1", array(':id' => $id));
        if (empty($table)) {
            return '未知数据！';
        } else {
            $tablezones = pdo_fetch("SELECT * FROM " . tablename($this->table_tablezones) . " where id=:id LIMIT 1", array(':id' => $table['tablezonesid']));
            $table_title = $tablezones['title'] . '-' . $table['title'];
        }
        return $table_title;
    }

    public function getQueueName($id)
    {
        $item = pdo_fetch("SELECT * FROM " . tablename($this->table_queue_setting) . " where id=:id LIMIT 1", array(':id' => $id));
        return $item['title'];
    }

    public function checkModule($name)
    {
        $module = pdo_fetch("SELECT * FROM " . tablename("modules") . " WHERE name=:name ", array(':name' => $name));
        return $module;
    }

    //提示信息
    public function showMessageAjax($msg, $code = 0)
    {
        $result['code'] = $code;
        $result['msg'] = $msg;
        message($result, '', 'ajax');
    }

    public function getStoreID()
    {
        global $_W, $_GPC;
        $weid = $this->_weid;

        $setting = pdo_fetch("SELECT * FROM " . tablename($this->table_setting) . " WHERE weid=:weid  ORDER BY id DESC LIMIT 1", array(':weid' => $weid));
        if (!empty($setting)) {
            return intval($setting['storeid']);
        } else {
            $store = pdo_fetch("SELECT * FROM " . tablename($this->table_stores) . "  WHERE weid={$weid}  ORDER BY id DESC LIMIT 1");
            return intval($store['id']);
        }
    }

    public function  doMobileAjaxdelete()
    {
        global $_GPC;
        $delurl = $_GPC['pic'];
        load()->func('file');
        if (file_delete($delurl)) {
            echo 1;
        } else {
            echo 0;
        }
    }

    function img_url($img = '')
    {
        global $_W;
        if (empty($img)) {
            return "";
        }
        if (substr($img, 0, 6) == 'avatar') {
            return $_W['siteroot'] . "resource/image/avatar/" . $img;
        }
        if (substr($img, 0, 8) == './themes') {
            return $_W['siteroot'] . $img;
        }
        if (substr($img, 0, 1) == '.') {
            return $_W['siteroot'] . substr($img, 2);
        }
        if (substr($img, 0, 5) == 'http:') {
            return $img;
        }
        return $_W['attachurl'] . $img;
    }

    //发送短信
    public function _sendSms($sendinfo)
    {
        global $_W;
        load()->func('communication');
        $weid = $_W['uniacid'];
        $username = $sendinfo['username'];
        $pwd = $sendinfo['pwd'];
        $mobile = $sendinfo['mobile'];
        $content = $sendinfo['content'];
        $target = "http://www.dxton.com/webservice/sms.asmx/Submit";
        //替换成自己的测试账号,参数顺序和wenservice对应
        $post_data = "account=" . $username . "&password=" . $pwd . "&mobile=" . $mobile . "&content=" . rawurlencode($content);
        //请自己解析$gets字符串并实现自己的逻辑
        //<result>100</result>表示成功,其它的参考文档

        $result = ihttp_request($target, $post_data);
        $xml = simplexml_load_string($result['content'], 'SimpleXMLElement', LIBXML_NOCDATA);
        $result = (string)$xml->result;
        $message = (string)$xml->message;
        return $result;
    }

    public function sendmail($config)
    {
        require_once 'plugin/email/class.phpmailer.php';
        $mail = new PHPMailer();
        $mail->CharSet = "utf-8";
        $body = $config['body'];
        $mail->IsSMTP();
        $mail->SMTPAuth = true; // enable SMTP authentication
        $mail->SMTPSecure = $config['secure']; // sets the prefix to the servier
        $mail->Host = $config['host']; // sets the SMTP server
        $mail->Port = $config['port'];
        $mail->Username = $config['sendmail']; // 发件邮箱用户名
        $mail->Password = $config['password']; // 发件邮箱密码
        $mail->From = $config['sendmail']; //发件邮箱
        $mail->FromName = $config['username']; //发件人名称
        $mail->Subject = $config['subject']; //主题
        $mail->WordWrap = 50; // set word wrap
        $mail->MsgHTML($body);
        $mail->AddAddress($config['mailaddress'], ''); //收件人地址、名称
        $mail->IsHTML(true); // send as HTML
        if (!$mail->Send()) {
            $status = 0;
        } else {
            $status = 1;
        }
        return $status;
    }

    public function doMobileValidatecheckcode()
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        $from_user = $_GPC['from_user'];
        $this->_fromuser = $from_user;
        $mobile = trim($_GPC['mobile']);
        $checkcode = trim($_GPC['checkcode']);

        if (empty($mobile)) {
            $this->showMsg('请输入手机号码!');
        }

        if (empty($checkcode)) {
            $this->showMsg('请输入验证码!');
        }

        $item = pdo_fetch("SELECT * FROM " . tablename('weisrc_dish_sms_checkcode') . " WHERE weid = :weid  AND from_user=:from_user AND checkcode=:checkcode ORDER BY `id` DESC limit 1", array(':weid' => $weid, ':from_user' => $from_user, ':checkcode' => $checkcode));

        if (empty($item)) {
            $this->showMsg('验证码输入错误!');
        } else {
            pdo_update('weisrc_dish_sms_checkcode', array('status' => 1), array('id' => $item['id']));
        }

        $this->showMsg('验证成功!', 1);
    }

    public function showMsg($msg, $status = 0)
    {
        $result = array('msg' => $msg, 'status' => $status);
        echo json_encode($result);
        exit();
    }

    //取得短信验证码
    public function doMobileGetCheckCode()
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        $from_user = trim($_GPC['from_user']);
        $this->_fromuser = $from_user;
        $mobile = trim($_GPC['mobile']);
        $storeid = intval($_GPC['storeid']);

        if (!preg_match("/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$|147[0-9]{8}$/", $mobile)) {
            $this->showMsg('手机号码格式不对!');
        }

        $passcheckcode = pdo_fetch("SELECT * FROM " . tablename('weisrc_dish_sms_checkcode') . " WHERE weid = :weid  AND from_user=:from_user AND status=1 ORDER BY `id` DESC limit 1", array(':weid' => $weid, ':from_user' => $from_user));
        if (!empty($passcheckcode)) {
            $this->showMsg('发送成功!', 1);
        }

        $smsSetting = pdo_fetch("SELECT * FROM " . tablename($this->table_setting) . " WHERE weid=:weid LIMIT 1", array(':weid' => $weid));
        if (empty($smsSetting) || empty($smsSetting['sms_username']) || empty($smsSetting['sms_pwd'])) {
            $this->showMsg('商家未开启验证码!');
        }

        $checkCodeCount = pdo_fetchcolumn("SELECT count(1) FROM " . tablename('weisrc_dish_sms_checkcode') . " WHERE weid = :weid  AND from_user=:from_user ", array(':weid' => $weid, ':from_user' => $from_user));
        if ($checkCodeCount >= 3) {
            $this->showMsg('您请求的验证码已超过最大限制..' . $checkCodeCount);
        }

        //判断数据是否已经存在
        $data = pdo_fetch("SELECT * FROM " . tablename('weisrc_dish_sms_checkcode') . " WHERE weid = :weid  AND from_user=:from_user ORDER BY `id` DESC limit 1", array(':weid' => $weid, ':from_user' => $from_user));
        if (!empty($data)) {
            if (TIMESTAMP - $data['dateline'] < 60) {
                $this->showMsg('每分钟只能获取短信一次!');
            }
        }

        //验证码
        $checkcode = random(6, 1);
        $checkcode = $this->getNewCheckCode($checkcode);
        $data = array(
            'weid' => $weid,
            'from_user' => $from_user,
            'mobile' => $mobile,
            'checkcode' => $checkcode,
            'status' => 0,
            'dateline' => TIMESTAMP
        );

        $sendInfo = array();
        $sendInfo['username'] = $smsSetting['sms_username'];
        $sendInfo['pwd'] = $smsSetting['sms_pwd'];
        $sendInfo['mobile'] = $mobile;
        $sendInfo['content'] = "您的验证码是：" . $checkcode . "。如需帮助请联系客服。";
        $return_result_code = $this->_sendSms($sendInfo);
        if ($return_result_code != '100') {
            $code_msg = $this->sms_status[$return_result_code];
            $this->showMsg($code_msg . $return_result_code);
        } else {
            pdo_insert('weisrc_dish_sms_checkcode', $data);
            $this->showMsg('发送成功!', 1);
        }
    }

    public function getNewCheckCode($checkcode)
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        $from_user = $this->_from_user;

        $data = pdo_fetch("SELECT checkcode FROM " . tablename('weisrc_dish_sms_checkcode') . " WHERE weid = :weid AND checkcode = :checkcode AND from_user=:from_user ORDER BY `id` DESC limit 1", array(':weid' => $weid, ':checkcode' => $checkcode, ':from_user' => $from_user));

        if (!empty($data)) {
            $checkcode = random(6, 1);
            $this->getNewCheckCode($checkcode);
        }
        return $checkcode;
    }

    //打印数据
    public function doWebPrint()
    {
        global $_W, $_GPC;
        $weid = $_W['uniacid'];
        $usr = !empty($_GET['usr']) ? $_GET['usr'] : '355839026790719';
        $ord = !empty($_GET['ord']) ? $_GET['ord'] : 'no';
        $sgn = !empty($_GET['sgn']) ? $_GET['sgn'] : 'no';

        header('Content-type: text/html; charset=gbk');

        $print_type_confirmed = 0;
        $print_type_payment = 1;

        //更新打印状态
        if (isset($_GET['sta'])) {
            $id = intval($_GPC['id']); //订单id
            $sta = intval($_GPC['sta']); //状态

            pdo_update($this->table_print_order, array('print_status' => $sta), array('orderid' => $id, 'print_usr' => $usr));
            //id —— 平台下发打印数据的id号,打印机打印后回复打印是否成功带此id号。
            //usr -- 打印机终端系统的IMEI号码或SIM卡的IMSI号码
            //sta —— 打印机状态(0为打印成功, 1为过热,3为缺纸卡纸等)
            exit;
        }

        //打印机配置信息
        $setting = pdo_fetch("SELECT * FROM " . tablename($this->table_print_setting) . " WHERE print_usr = :usr AND print_status=1 AND type='hongxin'", array(':usr' => $usr));
        if ($setting == false) {
            exit;
        }

        //门店id
        $storeid = $setting['storeid'];

        $condition = "";
        if ($setting['print_type'] == $print_type_confirmed) {
            //已确认订单 //status == 1
            $condition = ' AND paytype>0 ';
        } else if ($setting['print_type'] == $print_type_payment) {
            //已付款订单 //已完成
            $condition = ' AND (ispay=1 or status=3) ';
        }

        //根据订单id读取相关订单
        $order = pdo_fetch("SELECT * FROM " . tablename($this->table_order) . " WHERE  id IN(SELECT orderid FROM "
            . tablename('weisrc_dish_print_order') . " WHERE print_status=-1 AND
print_usr=:print_usr) AND storeid = :storeid {$condition} ORDER BY id DESC limit 1", array(':storeid' => $storeid, ':print_usr' => $usr));

        //没有新订单
        if ($order == false) {
            message('no data!');
            exit;
        }

        //商品id数组
        $goodsid = pdo_fetchall("SELECT goodsid, total FROM " . tablename($this->table_order_goods) . " WHERE orderid = '{$order['id']}'", array(), 'goodsid');

        //商品
        $goods = pdo_fetchall("SELECT * FROM " . tablename($this->table_goods) . "  WHERE id IN ('" . implode("','", array_keys($goodsid)) . "')");
        $order['goods'] = $goods;

        if (!empty($setting['print_top'])) {
            $content = "%10" . $setting['print_top'] . "\n";
        } else {
            $content = '';
        }
        $ordertype = array(
            '1' => '堂点',
            '2' => '外卖',
            '3' => '预定',
            '4' => '快餐'
        );

        $paytype = array('0' => '线下付款', '1' => '余额支付', '2' => '在线支付', '3' => '货到付款');
        $content .= '%00单号:' . $order['ordersn'] . "\n";
        $content .= '支付方式:' . $paytype[$order['paytype']] . "\n";
        $content .= '下单日期:' . date('Y-m-d H:i:s', $order['dateline']) . "\n";
        $content .= '预约时间:' . $order['meal_time'] . "\n";
        if (!empty($order['seat_type'])) {
            $seat_type = $order['seat_type'] == 1 ? '大厅' : '包间';
            $content .= '%10位置类型:' . $seat_type . "\n";
        }
        if (!empty($order['tables'])) {
            $content .= '%10桌号:' . $this->getTableName($order['tables']) . "\n";
        }

        if (!empty($order['remark'])) {
            $content .= '%10备注:' . $order['remark'] . "\n";
        }
        $content .= "%00\n名称              数量  单价 \n";
        $content .= "----------------------------\n%10";

        $content1 = '';
        foreach ($order['goods'] as $v) {
            $money = $v['marketprice'];
            $content1 .= $this->stringformat($v['title'], 16) . $this->stringformat($goodsid[$v['id']]['total'], 4, false) . $this->stringformat(number_format($money, 2), 7, false) . "\n\n";
        }

        $content2 = "----------------------------\n";
        $content2 .= "%10总数量:" . $order['totalnum'] . "   总价:" . number_format($order['totalprice'], 2) . "元\n%00";
        if (!empty($order['username'])) {
            $content2 .= '姓名:' . $order['username'] . "\n";
        }
        if (!empty($order['tel'])) {
            $content2 .= '手机:' . $order['tel'] . "\n";
        }
        if (!empty($order['address'])) {
            $content2 .= '地址:' . $order['address'] . "\n";
        }

        if (!empty($setting['qrcode_status'])) {
            $qrcode_url = trim($setting['qrcode_url']);
            if (!empty($qrcode_url)) {
                $content2 .= "%%%50372C" . $qrcode_url . "\n";
            }
        }

        //$content2 .= "%%%50372Chttp://www.weisrc.com\n";

        if (!empty($setting['print_bottom'])) {
            $content2 .= "%10" . $setting['print_bottom'] . "\n%00";
        }

        $content = iconv("UTF-8", "GB2312//IGNORE", $content);
        $content1 = iconv("UTF-8", "GB2312//IGNORE", $content1);
        $content2 = iconv("UTF-8", "GB2312//IGNORE", $content2);

        $setting = '<setting>124:' . $setting['print_nums'] . '|134:0</setting>';
        $setting = iconv("UTF-8", "GB2312//IGNORE", $setting);
        echo '<?xml version="1.0" encoding="GBK"?><r><id>' . $order['id'] . '</id><time>' . date('Y-m-d H:i:s', $order['dateline']) . '</time><content>' . $content . $content1 . $content2 . '</content>' . $setting . '</r>';
    }

    //用户打印机处理订单
    private function stringformat($string, $length = 0, $isleft = true)
    {
        $substr = '';
        if ($length == 0 || $string == '') {
            return $string;
        }
        if (strlen($string) > $length) {
            for ($i = 0; $i < $length; $i++) {
                $substr = $substr . "_";
            }
            $string = $string . '%%' . $substr;
        } else {
            for ($i = strlen($string); $i < $length; $i++) {
                $substr = $substr . " ";
            }
            $string = $isleft ? ($string . $substr) : ($substr . $string);
        }
        return $string;
    }

    private $version = '';

    public function doMobileVersion()
    {
        message($this->version);
    }

    function isWeixin()
    {
        if ($this->_weixin == 1) {
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
            if (!strpos($userAgent, 'MicroMessenger')) {
                include $this->template('s404');
                exit();
            }
        }
    }

    public function oauth2($url)
    {
        global $_GPC, $_W;
        load()->func('communication');
        $code = $_GPC['code'];
        if (empty($code)) {
            message('code获取失败.');
        }
        $token = $this->getAuthorizationCode($code);
        $from_user = $token['openid'];
        $userinfo = $this->getUserInfo($from_user);
        $sub = 1;
        if ($userinfo['subscribe'] == 0) {
            //未关注用户通过网页授权access_token
            $sub = 0;
            $authkey = intval($_GPC['authkey']);
            if ($authkey == 0) {
                $oauth2_code = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $this->_appid . "&redirect_uri=" . urlencode($url) . "&response_type=code&scope=snsapi_userinfo&state=0#wechat_redirect";
                header("location:$oauth2_code");
            }
            $userinfo = $this->getUserInfo($from_user, $token['access_token']);
        }

        if (empty($userinfo) || !is_array($userinfo) || empty($userinfo['openid']) || empty($userinfo['nickname'])) {
            echo '<h1>获取微信公众号授权失败[无法取得userinfo], 请稍后重试！ 公众平台返回原始数据为: <br />' . $sub . $userinfo['meta'] . '<h1>';
            exit;
        }

        //设置cookie信息
        setcookie($this->_auth2_headimgurl, $userinfo['headimgurl'], time() + 3600 * 24);
        setcookie($this->_auth2_nickname, $userinfo['nickname'], time() + 3600 * 24);
        setcookie($this->_auth2_openid, $from_user, time() + 3600 * 24);
        setcookie($this->_auth2_sex, $userinfo['sex'], time() + 3600 * 24);
//        print_r($userinfo);
//        exit;
        return $userinfo;
    }

    public function checkPermission($storeid = 0)
    {
        global $_GPC, $_W;
        if ($_W['role'] == 'operator') {
            $exists = pdo_fetch("SELECT * FROM " . tablename($this->table_account) . " WHERE uid = :uid AND weid = :weid", array(':weid' => $this->_weid, ':uid' => $_W['user']['uid']));
            if (empty($exists['storeid'])) {
                message('您没有任何操作权限');
            } elseif ($exists['storeid'] != $storeid && $storeid != 0) {
                message('您没有该门店的操作权限');
            } else {
                return $exists['storeid'];
            }
        }
//        print_r($_W['user']);
//        print_r($_W);
//        print_r($_W['role']);
        //manager
        //operator
//        exit;
    }

    public function getUserInfo($from_user, $ACCESS_TOKEN = '')
    {
        if ($ACCESS_TOKEN == '') {
            $ACCESS_TOKEN = $this->getAccessToken();
            $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token={$ACCESS_TOKEN}&openid={$from_user}&lang=zh_CN";
        } else {
            $url = "https://api.weixin.qq.com/sns/userinfo?access_token={$ACCESS_TOKEN}&openid={$from_user}&lang=zh_CN";
        }

        $json = ihttp_get($url);
        $userInfo = @json_decode($json['content'], true);
        return $userInfo;
    }

    public function getAuthorizationCode($code)
    {
        $oauth2_code = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$this->_appid}&secret={$this->_appsecret}&code={$code}&grant_type=authorization_code";
        $content = ihttp_get($oauth2_code);
        $token = @json_decode($content['content'], true);
        if (empty($token) || !is_array($token) || empty($token['access_token']) || empty($token['openid'])) {
            $oauth2_code = $this->createMobileUrl('waprestlist', array(), true);
            header("location:$oauth2_code");
//            echo '微信授权失败, 请稍后重试! 公众平台返回原始数据为: <br />' . $content['meta'] . '<h1>';
            exit;
        }
        return $token;
    }

    public function getAccessToken()
    {
        global $_W;
        $account = $_W['account'];
        if ($this->_accountlevel < 4) {
            if (!empty($this->_account)) {
                $account = $this->_account;
            }
        }
        load()->classs('weixin.account');
        $accObj = WeixinAccount::create($account['acid']);
        $access_token = $accObj->fetch_token();
        return $access_token;
    }

    public function getCode($url)
    {
        global $_W;
        $url = urlencode($url);
        $oauth2_code = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$this->_appid}&redirect_uri={$url}&response_type=code&scope=snsapi_base&state=0#wechat_redirect";
        header("location:$oauth2_code");
    }

    public $actions_titles = array(
        'stores' => '全部门店',
//        'statistics' => '统计中心',
        'order' => '订单管理',
        'tables' => '餐桌管理',
        'queueorder' => '排号管理',
        'fans' => '会员管理',
        'goods' => '菜品管理',
        'category' => '菜品类别',
        'intelligent' => '套餐管理',
        'reservation' => '预定管理',
//        'smssetting' => '短信设置',
//        'emailsetting' => '邮件设置',
        'printsetting' => '打印机设置',
        'printorder' => '打印数据'
        //'storesetting' => '门店设置'
    );

    public $sms_status = array(
        '100' => '发送成功',
        '101' => '验证失败',
        '102' => '手机号码格式不正确',
        '103' => '会员级别不够',
        '104' => '内容未审核',
        '105' => '内容过多',
        '106' => '账户余额不足',
        '107' => 'Ip受限',
        '108' => '手机号码发送太频繁，请换号或隔天再发',
        '109' => '帐号被锁定',
        '110' => '手机号发送频率持续过高，黑名单屏蔽数日',
        '111' => '系统升级',
    );

    public function insert_default_nave($name, $type, $link)
    {
        global $_GPC, $_W;
        checklogin();

        $data = array(
            'weid' => $_W['uniacid'],
            'type' => $type,
            'name' => $name,
            'link' => $link,
            'displayorder' => 0,
            'status' => 1,
        );

        $nave = pdo_fetch("SELECT * FROM " . tablename($this->table_nave) . " WHERE name = :name AND weid=:weid", array(':name' => $name, ':weid' => $_W['uniacid']));

        if (empty($nave)) {
            pdo_insert($this->table_nave, $data);
        }
        return pdo_insertid();
    }

    public function doWebNave()
    {
        global $_W, $_GPC;
        checklogin();

        $action = 'nave';
        $title = '导航管理'; //$title = $this->actions_titles[$action];

        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        if ($operation == 'display') {
            if ($_GPC['type'] == 'default') {
                $this->insert_default_nave('我的菜单', 4, '');
                $this->insert_default_nave('智能点餐', 5, '');
                $this->insert_default_nave('商品列表', 3, '');
                $this->insert_default_nave('我的订单', 6, '');
                $this->insert_default_nave('门店列表', 2, '');
            }

            if (!empty($_GPC['displayorder'])) {
                foreach ($_GPC['displayorder'] as $id => $displayorder) {
                    pdo_update($this->table_nave, array('displayorder' => $displayorder), array('id' => $id));
                }
                message('排序更新成功！', $this->createWebUrl('nave', array('op' => 'display')), 'success');
            }
            $children = array();
            $nave = pdo_fetchall("SELECT * FROM " . tablename($this->table_nave) . " WHERE weid = '{$_W['uniacid']}' ORDER BY displayorder DESC,id DESC");
            include $this->template('site_nave');
        } elseif ($operation == 'post') {
            $id = intval($_GPC['id']);
            if (!empty($id)) {
                $nave = pdo_fetch("SELECT * FROM " . tablename($this->table_nave) . " WHERE id = '$id'");
            }

            if (checksubmit('submit')) {
                if (empty($_GPC['linkname'])) {
                    message('抱歉，请输入导航名称！');
                }

                $data = array(
                    'weid' => $_W['uniacid'],
                    'type' => intval($_GPC['type']),
                    'name' => trim($_GPC['linkname']),
                    'link' => trim($_GPC['link']),
                    'status' => intval($_GPC['status']),
                    'displayorder' => intval($_GPC['displayorder']),
                );

                if (!empty($id)) {
                    pdo_update($this->table_nave, $data, array('id' => $id));
                } else {
                    pdo_insert($this->table_nave, $data);
                    $id = pdo_insertid();
                }
                message('更新成功！', $this->createWebUrl('nave', array('op' => 'display')), 'success');
            }
            include $this->template('site_nave');
        } elseif ($operation == 'delete') {
            $id = intval($_GPC['id']);
            $nave = pdo_fetch("SELECT id FROM " . tablename($this->table_nave) . " WHERE id = '$id'");
            if (empty($nave)) {
                message('抱歉，不存在或是已经被删除！', $this->createWebUrl('nave', array('op' => 'display')), 'error');
            }
            pdo_delete($this->table_nave, array('id' => $id));
            message('删除成功！', $this->createWebUrl('nave', array('op' => 'display')), 'success');
        }
    }

    public function doWebSmsSetting()
    {
        global $_GPC, $_W;
//        $GLOBALS['frames'] = $this->getNaveMenu();
        $action = 'smssetting';
        $title = $this->actions_titles[$action];
        $storeid = intval($_GPC['storeid']);

        $store = pdo_fetch("SELECT * FROM " . tablename($this->table_stores) . " WHERE weid = :weid AND id=:storeid ORDER BY `id` DESC", array(':weid' => $_W['uniacid'], ':storeid' => $storeid));
        if (empty($store)) {
            message('非法操作.');
        }

        $setting = pdo_fetch("SELECT * FROM " . tablename($this->table_sms_setting) . " WHERE weid = :weid AND storeid=:storeid", array(':weid' => $_W['uniacid'], ':storeid' => $storeid));
        if (checksubmit('submit')) {
            $data = array(
                'weid' => $_W['uniacid'],
                'storeid' => $storeid,
                'sms_enable' => intval($_GPC['sms_enable']),
                'sms_username' => trim($_GPC['sms_username']),
                'sms_id' => $_GPC['sms_id'],
                'sms_pwd' => trim($_GPC['sms_pwd']),
                'sms_verify_enable' => intval($_GPC['sms_verify_enable']),
                'sms_mobile' => trim($_GPC['sms_mobile']),
                'sms_business_tpl' => trim($_GPC['sms_business_tpl']),
                'dateline' => TIMESTAMP
            );

            if (empty($setting)) {
                pdo_insert($this->table_sms_setting, $data);
            } else {
                unset($data['dateline']);
                pdo_update($this->table_sms_setting, $data, array('weid' => $_W['uniacid'], 'storeid' => $storeid));
            }
            message('操作成功', $this->createWebUrl('smssetting', array('storeid' => $storeid)), 'success');
        }
        include $this->template('sms_setting');
    }

    //打印机设置
    public function doWebPrintSetting()
    {
        global $_GPC, $_W;
        $weid = $this->_weid;
//        $GLOBALS['frames'] = $this->getNaveMenu();
        $action = 'printsetting';
        $title = $this->actions_titles[$action];
        $storeid = intval($_GPC['storeid']);
        if (empty($storeid)) {
            message('请选择门店!');
        }
        $returnid = $this->checkPermission($storeid);

        if(!pdo_fieldexists('weisrc_dish_print_setting', 'is_print_all')) {
            pdo_query("ALTER TABLE ".tablename('weisrc_dish_print_setting')." ADD `is_print_all` tinyint(1) NOT NULL DEFAULT '1';");
        }
        if(!pdo_fieldexists('weisrc_dish_print_setting', 'print_goodstype')) {
            pdo_query("ALTER TABLE ".tablename('weisrc_dish_print_setting')." ADD `print_goodstype` varchar(500) DEFAULT '0';");
        }

        $store = pdo_fetch("SELECT * FROM " . tablename($this->table_stores) . " WHERE weid = :weid AND id=:storeid ORDER BY `id` DESC", array(':weid' => $_W['uniacid'], ':storeid' => $storeid));
        if (empty($store)) {
            message('非法操作！门店不存在.');
        }
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        if ($operation == 'display') {
            $list = pdo_fetchall("SELECT * FROM " . tablename($this->table_print_setting) . " WHERE weid = :weid AND storeid=:storeid", array(':weid' => $_W['uniacid'], ':storeid' => $storeid));
            $print_order_count = pdo_fetchall("SELECT print_usr,COUNT(1) as count FROM " . tablename($this->table_print_order) . "  GROUP BY print_usr,weid having weid = :weid", array(':weid' => $_W['uniacid']), 'print_usr');
        } else if ($operation == 'post') {
            $id = intval($_GPC['id']);
            $setting = pdo_fetch("SELECT * FROM " . tablename($this->table_print_setting) . " WHERE weid = :weid AND storeid=:storeid AND id=:id", array(':weid' => $_W['uniacid'], ':storeid' => $storeid, ':id' => $id));
            if (!empty($setting['print_goodstype'])) {
                $print_goodstypes = explode(',', $setting['print_goodstype']);
            }

            $category = pdo_fetchall("SELECT * FROM " . tablename($this->table_category) . " WHERE weid = :weid And storeid=:storeid ORDER BY parentid ASC, displayorder DESC", array(':weid' => $weid, ':storeid' => $storeid), 'id');
            if (!empty($category)) {
                $children = '';
                foreach ($category as $cid => $cate) {
                    if (!empty($cate['parentid'])) {
                        $children[$cate['parentid']][$cate['id']] = array($cate['id'], $cate['name']);
                    }
                }
            }

            if (checksubmit('submit')) {

                $print_goodstype = implode(',', $_GPC['print_goodstype']);

                $num = intval($_GPC['print_nums']);
                if ($num == 0) $num = 1;
                if ($num > 10) {
                    message('打印联数不能大于10。');
                }
                $data = array(
                    'weid' => $_W['uniacid'],
                    'storeid' => $storeid,
                    'weid' => $_W['uniacid'],
                    'title' => trim($_GPC['title']),
                    'type' => trim($_GPC['type']),
                    'member_code' => trim($_GPC['member_code']),
                    'feyin_key' => trim($_GPC['feyin_key']),
                    'print_status' => trim($_GPC['print_status']),
                    'print_type' => trim($_GPC['print_type']),
                    'print_usr' => trim($_GPC['print_usr']),
                    'print_nums' => $num,
                    'print_top' => trim($_GPC['print_top']),
                    'print_bottom' => trim($_GPC['print_bottom']),
                    'qrcode_status' => intval($_GPC['qrcode_status']),
                    'qrcode_url' => trim($_GPC['qrcode_url']),
                    'is_print_all' => intval($_GPC['is_print_all']),
                    'print_goodstype' => $print_goodstype,
                    'dateline' => TIMESTAMP
                );
                if (empty($setting)) {
                    $flag = pdo_fetch("SELECT * FROM " . tablename($this->table_print_setting) . " WHERE print_usr=:print_usr LIMIT 1", array(':print_usr' => trim($_GPC['print_usr'])));
                    if (!empty($flag)) {
                        message('打印机终端编号已经被使用,不能重复添加！', $this->createWebUrl('printsetting', array('storeid' => $storeid)), 'success');
                    }
                    pdo_insert($this->table_print_setting, $data);
                } else {
                    unset($data['dateline']);
                    $flag = pdo_fetch("SELECT * FROM " . tablename($this->table_print_setting) . " WHERE print_usr=:print_usr AND id<>:id LIMIT 1", array(':print_usr' => trim($_GPC['print_usr']), ':id' => $id));
                    if (!empty($flag)) {
                        message('打印机终端编号已经被使用,不能重复添加！', $this->createWebUrl('printsetting', array('storeid' => $storeid)), 'success');
                    }

                    pdo_update($this->table_print_setting, $data, array('weid' => $_W['uniacid'], 'storeid' => $storeid, 'id' => $id));
                }
                message('操作成功', $this->createWebUrl('printsetting', array('storeid' => $storeid)), 'success');
            }
        } else if ($operation == 'delete') {
            $id = intval($_GPC['id']);
            $print = pdo_fetch("SELECT id FROM " . tablename($this->table_print_setting) . " WHERE id = '$id'");
            if (empty($print)) {
                message('抱歉，不存在或是已经被删除！', $this->createWebUrl('printsetting', array('op' => 'display', 'storeid' => $storeid)), 'error');
            }

            pdo_delete($this->table_print_setting, array('id' => $id, 'weid' => $_W['uniacid']));
            message('删除成功！', $this->createWebUrl('printsetting', array('op' => 'display', 'storeid' => $storeid)), 'success');
        }

        include $this->template('print_setting');
    }

    public function doWebPrintOrder()
    {
        global $_W, $_GPC;
//        $GLOBALS['frames'] = $this->getNaveMenu();
        $action = 'printorder';
        $title = $this->actions_titles[$action];
        $storeid = intval($_GPC['storeid']);
        if (empty($storeid)) {
            message('请选择门店!');
        }
        $returnid = $this->checkPermission($storeid);
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';

        if ($operation == 'display') {
            $pindex = max(1, intval($_GPC['page']));
            $psize = 10;

            if (!empty($_GPC['usr'])) {
                $condition = " AND print_usr='{$_GPC['usr']}' ";
            }

            if (!empty($_GPC['ordersn'])) {
                $condition .= " AND ordersn LIKE '%{$_GPC['ordersn']}%' ";
            }

            if (!empty($_GPC['selusr'])) {
                $condition .= " AND print_usr LIKE '%{$_GPC['selusr']}%' ";
            }

            $list = pdo_fetchall("SELECT * FROM " . tablename($this->table_order) . " a INNER JOIN " . tablename($this->table_print_order) . " b ON a.id=b.orderid WHERE a.weid = :weid AND a.storeid=:storeid {$condition} ORDER BY b.id DESC LIMIT " . ($pindex - 1) * $psize . ",{$psize}", array(':weid' => $_W['uniacid'], ':storeid' => $storeid));

            if (!empty($list)) {
                $total = pdo_fetchcolumn("SELECT count(1) FROM " . tablename($this->table_order) . " a INNER JOIN " . tablename($this->table_print_order) . " b ON a.id=b.orderid WHERE a.weid = :weid AND a.storeid=:storeid  $condition", array(':weid' => $_W['uniacid'], ':storeid' => $storeid));
                $pager = pagination($total, $pindex, $psize);
            }
        } elseif ($operation == 'delete') {
            $id = intval($_GPC['id']);
            pdo_delete($this->table_print_order, array('id' => $id, 'weid' => $_W['uniacid']));
            message('删除成功！', $this->createWebUrl('printorder', array('op' => 'display', 'storeid' => $storeid)), 'success');
        } elseif ($operation == 'deleteprintorder') {
            //删除未打印订单
            pdo_delete($this->table_print_order, array('weid' => $_W['uniacid'], 'print_status' => -1));
            message('删除成功！', $this->createWebUrl('printorder', array('op' => 'display', 'storeid' => $storeid)), 'success');
        }

        include $this->template('print_order');
    }

    public function doWebTemplate()
    {
        global $_W, $_GPC;
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        $tpl = dir(IA_ROOT . '/addons/weisrc_dish/template/mobile/');
        $tpl->handle;
        $templates = array();
        while ($entry = $tpl->read()) {
            if (preg_match("/^[a-zA-Z0-9]+$/", $entry) && $entry != 'common' && $entry != 'photo') {
                array_push($templates, $entry);
            }
        }
        $tpl->close();
        $template = pdo_fetch("SELECT * FROM " . tablename($this->table_template) . " WHERE weid = :weid", array(':weid' => $_W['uniacid']));

        if (empty($template)) {
            $templatename = 'style1';
        } else {
            $templatename = $template['template_name'];
        }

        if (!empty($_GPC['templatename'])) {

            $data = array(
                'weid' => $_W['uniacid'],
                'template_name' => trim($_GPC['templatename']),
            );

            if (empty($template)) {
                pdo_insert($this->table_template, $data);
            } else {
                pdo_update($this->table_template, $data, array('weid' => $_W['uniacid']));
            }
            message('操作成功', $this->createWebUrl('template'), 'success');
        }
        include $this->template('template');
    }

    //基本设置
    public function doWebSetting()
    {
        global $_W, $_GPC;
//        $GLOBALS['frames'] = $this->getNaveMenu();

        $weid = $this->_weid;
        $action = 'setting';
        $title = '网站设置';

        load()->func('tpl');
        if(!pdo_fieldexists('weisrc_dish_setting', 'tpltype')) {
            pdo_query("ALTER TABLE ".tablename('weisrc_dish_setting')." ADD `tpltype` tinyint(1) NOT NULL DEFAULT '1' COMMENT '模版行业类型';");
        }

        $stores = pdo_fetchall("SELECT * FROM " . tablename($this->table_stores) . " WHERE weid = :weid ORDER BY `id` DESC", array(':weid' => $_W['uniacid']));
        if (empty($stores)) {
            $url = $this->createWebUrl('stores', array('op' => 'display'));
            message('请先添加门店', $url);
        }

        $setting = pdo_fetch("SELECT * FROM " . tablename($this->table_setting) . " WHERE weid = :weid", array(':weid' => $_W['uniacid']));
        if (checksubmit('submit')) {
            $data = array(
                'weid' => $_W['uniacid'],
                'title' => trim($_GPC['title']),
                'thumb' => trim($_GPC['thumb']),
                'storeid' => intval($_GPC['storeid']),
                'entrance_type' => intval($_GPC['entrance_type']),
                'entrance_storeid' => intval($_GPC['entrance_storeid']),
                'order_enable' => intval($_GPC['order_enable']),
                'mode' => intval($_GPC['mode']),
                'is_notice' => intval($_GPC['is_notice']),
                'dining_mode' => intval($_GPC['dining_mode']),
                'istplnotice' => intval($_GPC['istplnotice']),
                'tplneworder' => trim($_GPC['tplneworder']),
                'tplnewqueue' => trim($_GPC['tplnewqueue']),
                'searchword' => trim($_GPC['searchword']),
                'tpluser' => trim($_GPC['tpluser']),
                'tpltype' => intval($_GPC['tpltype']),
                'sms_enable' => intval($_GPC['sms_enable']),
                'sms_username' => trim($_GPC['sms_username']),
                'sms_pwd' => trim($_GPC['sms_pwd']),
		 'is_sms' => trim($_GPC['is_sms']),
                'sms_id' => trim($_GPC['sms_id']),
                'sms_mobile' => trim($_GPC['sms_mobile']),
                'email_enable' => intval($_GPC['email_enable']),
                'email_host' => $_GPC['email_host'],
                'email_send' => $_GPC['email_send'],
                'email_pwd' => $_GPC['email_pwd'],
                'email_user' => $_GPC['email_user'],
                'email' => trim($_GPC['email']),
                'dateline' => TIMESTAMP
            );

            if ($data['email_enable'] == 1) {
                if (empty($_GPC['email_send']) || empty($_GPC['email_user']) || empty($_GPC['email_pwd'])) {
                    message('请完整填写邮件配置信息', 'refresh', 'error');
                }
                if ($_GPC['email_host'] == 'smtp.qq.com' || $_GPC['email_host'] == 'smtp.gmail.com') {
                    $secure = 'ssl';
                    $port = '465';
                } else {
                    $secure = 'tls';
                    $port = '25';
                }

                $mail_config = array();
                $mail_config['host'] = $_GPC['email_host'];
                $mail_config['secure'] = $secure;
                $mail_config['port'] = $port;
                $mail_config['username'] = $_GPC['email_user'];
                $mail_config['sendmail'] = $_GPC['email_send'];
                $mail_config['password'] = $_GPC['email_pwd'];
                $mail_config['mailaddress'] = $_GPC['email'];
                $mail_config['subject'] = '域顺微点餐提醒';
                $mail_config['body'] = '邮箱测试';

                $result = $this->sendmail($mail_config);
//                if ($result == 1) {
//                    message('邮箱配置成功', 'refresh');
//                } else {
//                    message('邮箱配置信息有误', 'refresh', 'error');
//                }
            }

            if (empty($setting)) {
                pdo_insert($this->table_setting, $data);
            } else {
                unset($data['dateline']);
                pdo_update($this->table_setting, $data, array('weid' => $_W['uniacid']));
            }
            message('操作成功', $this->createWebUrl('setting'), 'success');
        }

        include $this->template('setting');
    }

    public function doWebDeletemealtime()
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        $id = intval($_GPC['id']);
        $storeid = intval($_GPC['storeid']);

        if (empty($storeid)) {
            $url = $this->createWebUrl('stores', array('op' => 'post', 'id' => $storeid));
        }

        pdo_delete('weisrc_dish_mealtime', array('id' => $id, 'weid' => $weid));
        message('操作成功', $url, 'success');
    }

    public function getNaveMenu()
    {
        global $_W, $_GPC;
        $do = $_GPC['do'];
//        message($do);
        $navemenu = array();
        $navemenu[0] = array(
            'title' => '域顺微点餐',
            'items' => array(
                0 => array('title' => '门店管理', 'url' => $do != 'stores' ? $this->createWebUrl('stores', array('op' => 'display')) : ''),
                1 => array('title' => '订单管理', 'url' => $do != 'order' ? $this->createWebUrl('order', array('op' => 'display')) : ''),
                2 => array('title' => '门店类型', 'url' => $do != 'type' ? $this->createWebUrl('type', array('op' => 'display')) : ''),
                3 => array('title' => '区域管理', 'url' => $do != 'area' ? $this->createWebUrl('area', array('op' => 'display')) : ''),
                4 => array('title' => '黑名单', 'url' => $do != 'blacklist' ? $this->createWebUrl('blacklist', array('op' => 'display')) : ''),
                5 => array('title' => '基本设置', 'url' => $do != 'setting' ? $this->createWebUrl('setting', array('op' => 'display')) : ''),
            )
        );


        return $navemenu;
    }

    public function doWebAccount()
    {
        global $_GPC, $_W;
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';

        $stores = pdo_fetchall("SELECT * FROM " . tablename($this->table_stores) . " WHERE weid = :weid ORDER BY id DESC", array(':weid' => $this->_weid), 'id');

        if ($operation == 'post') {
            $id = intval($_GPC['id']);
            if (!empty($id)) {
                $account = pdo_fetch("SELECT * FROM " . tablename($this->table_account) . " WHERE weid = :weid AND id=:id ORDER BY id DESC", array(':weid' => $this->_weid, ':id' => $id));
            }

            if (!empty($account)) {
                $users = user_single($account['uid']);
            }

            if (checksubmit('submit')) {
                load()->model('user');
                $user = array();
                $user['username'] = trim($_GPC['username']);
                if (!preg_match(REGULAR_USERNAME, $user['username'])) {
                    message('必须输入用户名，格式为 3-15 位字符，可以包括汉字、字母（不区分大小写）、数字、下划线和句点。');
                }
                $user['password'] = $_GPC['password'];
                if (istrlen($user['password']) < 8) {
                    message('必须输入密码，且密码长度不得低于8位。');
                }
                if (!empty($account)) {
                    $user['salt'] = $users['salt'];
                    $user['uid'] = $account['uid'];
                }
                $user['remark'] = $_GPC['remark'];
                $user['status'] = $_GPC['status'];
//            $user['groupid'] = intval($_GPC['groupid']) ? intval($_GPC['groupid']) : message('请选择所属用户组');
                $user['groupid'] = 1;

                if (empty($users)) {
                    if (user_check(array('username' => $user['username']))) {
                        message('非常抱歉，此用户名已经被注册，你需要更换注册名称！');
                    }
                    $uid = user_register($user);
                    if ($uid > 0) {
                        unset($user['password']);
                        //operator
                        $data = array(
                            'uniacid' => $this->_weid,
                            'uid' => $uid,
                            'role' => 'operator',
                        );
                        $exists = pdo_fetch("SELECT * FROM " . tablename('uni_account_users') . " WHERE uid = :uid AND uniacid = :uniacid", array(':uniacid' => $this->_weid, ':uid' => $uid));
                        if (empty($exists)) {
                            pdo_insert('uni_account_users', $data);
                        }
                        //permission
                        pdo_insert('users_permission', array(
                            'uid' => $uid,
                            'uniacid' => $this->_weid,
                            'url' => '',
                            'type' => 'weisrc_dish',
                            'permission' => 'weisrc_dish_menu_stores'
                        ));

                        pdo_insert($this->table_account, array(
                            'uid' => $uid,
                            'weid' => $this->_weid,
                            'email' => trim($_GPC['email']),
                            'from_user' => trim($_GPC['from_user']),
                            'mobile' => trim($_GPC['mobile']),
                            'storeid' => intval($_GPC['storeid']),
                            'dateline' => TIMESTAMP
                        ));

                        message('用户增加成功！', $this->createWebUrl('account', array(), true));
                    }
                } else {
                    user_update($user);
                    pdo_update($this->table_account, array(
                        'weid' => $this->_weid,
                        'email' => trim($_GPC['email']),
                        'mobile' => trim($_GPC['mobile']),
                        'from_user' => trim($_GPC['from_user']),
                        'storeid' => intval($_GPC['storeid']),
                        'dateline' => TIMESTAMP
                    ), array('id' => $id));
                    message('更新成功！', $this->createWebUrl('account', array(), true));
                }
                message('操作用户失败，请稍候重试或联系网站管理员解决！');
            }
        } else if ($operation == 'display') {
            $strwhere = '';
            $pindex = max(1, intval($_GPC['page']));
            $psize = 10;
            $list = pdo_fetchall("SELECT a.*,b.username AS username,b.status AS status FROM " . tablename($this->table_account) . " a INNER JOIN " . tablename('users') . " b ON a.uid=b.uid WHERE a.weid = :weid $strwhere ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(':weid' => $this->_weid));

            if (!empty($list)) {
                $total = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename($this->table_account) . " WHERE weid = :weid $strwhere", array(':weid' => $this->_weid));
                $pager = pagination($total, $pindex, $psize);
            }
        }

        include $this->template('account');
    }

    public function doWebAd()
    {
        global $_GPC, $_W;
        load()->func('tpl');
//        $GLOBALS['frames'] = $this->getNaveMenu();

        $url = $this->createWebUrl('ad', array('op' => 'display'));
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        if ($operation == 'post') {

            $id = intval($_GPC['id']);
            if (!empty($id)) {
                $item = pdo_fetch("SELECT * FROM " . tablename($this->table_ad) . " WHERE id = :id", array(':id' => $id));
                if (empty($item)) {
                    message('抱歉，广告不存在或是已经删除！', '', 'error');
                }
            }

            if (!empty($item)) {
                $thumb = tomedia($item['thumb']);
            } else {
                $item = array(
                    "status" => 1,
                    "starttime" => TIMESTAMP,
                    "endtime" => strtotime(date("Y-m-d H:i", TIMESTAMP + 30 * 86400))
                );
            }

            if (checksubmit('submit')) {
                $data = array(
                    'uniacid' => intval($this->_weid),
                    'title' => trim($_GPC['title']),
                    'thumb' => $_GPC['thumb'],
                    'url' => $_GPC['url'],
                    'position' => intval($_GPC['position']),
                    'starttime' => strtotime($_GPC['datelimit']['start']),
                    'endtime' => strtotime($_GPC['datelimit']['end']),
                    'status' => intval($_GPC['status']),
                    'displayorder' => intval($_GPC['displayorder']),
                    'dateline' => TIMESTAMP,
                );

                if (empty($id)) {
                    pdo_insert($this->table_ad, $data);
                } else {
                    unset($data['dateline']);
                    pdo_update($this->table_ad, $data, array('id' => $id));
                }
                message('数据更新成功！', $url, 'success');
            }
        } elseif ($operation == 'display') {
            if (!empty($_GPC['displayorder'])) {
                foreach ($_GPC['displayorder'] as $id => $displayorder) {
                    pdo_update($this->table_ad, array('displayorder' => $displayorder), array('id' => $id));
                }
                message('排序更新成功！', $url, 'success');
            }

            $strwhere = '';

            $pindex = max(1, intval($_GPC['page']));
            $psize = 10;
            $list = pdo_fetchall("SELECT * FROM " . tablename($this->table_ad) . " WHERE uniacid = :uniacid $strwhere ORDER BY status DESC, displayorder DESC, id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(':uniacid' => $this->_weid));

            if (!empty($list)) {
                $total = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename($this->table_ad) . " WHERE uniacid = :uniacid $strwhere", array(':uniacid' => $this->_weid));
                $pager = pagination($total, $pindex, $psize);
            }
        } elseif ($operation == 'delete') {
            $id = intval($_GPC['id']);
            $row = pdo_fetch("SELECT * FROM " . tablename($this->table_ad) . " WHERE id = :id", array(':id' => $id));
            if (empty($row)) {
                message('抱歉，数据不存在或是已经被删除！');
            }

            pdo_delete($this->table_ad, array('id' => $id));
            message('删除成功！', $this->createWebUrl('ad', array('op' => 'display')), 'success');
        }
        include $this->template('ad');
    }

    public function doWebSetAdProperty()
    {
        global $_GPC, $_W;
        $id = intval($_GPC['id']);
        $type = $_GPC['type'];
        $data = intval($_GPC['data']);
        empty($data) ? ($data = 1) : $data = 0;
        if (!in_array($type, array('status'))) {
            die(json_encode(array("result" => 0)));
        }
        pdo_update($this->table_ad, array($type => $data), array("id" => $id, "uniacid" => $_W['uniacid']));
        die(json_encode(array("result" => 1, "data" => $data)));
    }

    //门店管理
    public function doWebStores()
    {
        global $_W, $_GPC, $code;
        $weid = $this->_weid;
        $returnid = $this->checkPermission();
        $code = $this->copyright;
//        $GLOBALS['frames'] = $this->getNaveMenu();
        $config = $this->module['config']['weisrc_dish'];

        $action = 'stores';
        $title = '门店管理';
        $url = $this->createWebUrl($action, array('op' => 'display'));
        $area = pdo_fetchall("SELECT * FROM " . tablename($this->table_area) . " where weid = :weid ORDER BY displayorder DESC", array(':weid' => $weid));
        $shoptype = pdo_fetchall("SELECT * FROM " . tablename($this->table_type) . " where weid = :weid ORDER BY displayorder DESC", array(':weid' => $weid));

        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        if ($operation == 'setting') {
            if (checksubmit('submit')) {
                $cfg['weisrc_dish']['storecount'] = trim($_GPC['storecount']);
                $this->saveSettings($cfg);
                message('更新成功！', $url, 'success');
            }
        } else if ($operation == 'display') {
            $shoptypeid = intval($_GPC['shoptypeid']);
            $areaid = intval($_GPC['areaid']);
            $keyword = trim($_GPC['keyword']);

            if (checksubmit('submit')) { //排序
                if (is_array($_GPC['displayorder'])) {
                    foreach ($_GPC['displayorder'] as $id => $val) {
                        $data = array('displayorder' => intval($_GPC['displayorder'][$id]));
                        pdo_update($this->table_stores, $data, array('id' => $id));
                    }
                }
                message('操作成功!', $url);
            }
            $pindex = max(1, intval($_GPC['page']));
            $psize = 10;
            $where = "WHERE weid = {$weid}";

            if (!empty($keyword)) {
                $where .= " AND title LIKE '%{$keyword}%'";
            }
            if ($shoptypeid != 0) {
                $where .= " AND typeid={$shoptypeid} ";
            }
            if ($areaid != 0) {
                $where .= " AND areaid={$areaid} ";
            }
            if ($returnid != 0) {
                $where .= " AND id={$returnid} ";
            }

            $storeslist = pdo_fetchall("SELECT * FROM " . tablename($this->table_stores) . " {$where} order by displayorder desc,id desc LIMIT " . ($pindex - 1) * $psize . ",{$psize}");
            if (!empty($storeslist)) {
                $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename($this->table_stores) . " $where");
                $pager = pagination($total, $pindex, $psize);
            }
        } elseif ($operation == 'post') {
            load()->func('tpl');
            $id = intval($_GPC['id']); //门店编号
            $reply = pdo_fetch("select * from " . tablename($this->table_stores) . " where id=:id and weid =:weid", array(':id' => $id, ':weid' => $weid));
            $timelist = pdo_fetchall("SELECT * FROM " . tablename('weisrc_dish_mealtime') . " WHERE weid = :weid AND storeid=:storeid order by id", array(':weid' => $weid, ':storeid' => $id));

            if (empty($reply)) {
                $reply['begintime'] = "09:00";
                $reply['endtime'] = "18:00";
            }

            $piclist = unserialize($reply['thumb_url']);

            if (checksubmit('submit')) {
                $data = array(
                    'weid' => intval($_W['uniacid']),
                    'areaid' => intval($_GPC['area']),
                    'typeid' => intval($_GPC['type']),
                    'title' => trim($_GPC['title']),
                    'info' => trim($_GPC['info']),
                    'from_user' => trim($_GPC['from_user']),
                    'content' => trim($_GPC['content']),
                    'tel' => trim($_GPC['tel']),
                    'announce' => trim($_GPC['announce']),
                    'logo' => trim($_GPC['logo']),
                    'address' => trim($_GPC['address']),
                    'location_p' => trim($_GPC['location_p']),
                    'location_c' => trim($_GPC['location_c']),
                    'location_a' => trim($_GPC['location_a']),
                    'lng' => trim($_GPC['baidumap']['lng']),
                    'lat' => trim($_GPC['baidumap']['lat']),
                    'password' => trim($_GPC['password']),
                    'recharging_password' => trim($_GPC['recharging_password']),
                    'is_show' => intval($_GPC['is_show']),
                    'place' => trim($_GPC['place']),
                    'qq' => trim($_GPC['qq']),
                    'weixin' => trim($_GPC['weixin']),
                    'hours' => trim($_GPC['hours']),
                    'consume' => trim($_GPC['consume']),
                    'level' => intval($_GPC['level']),
                    'enable_wifi' => intval($_GPC['enable_wifi']),
                    'enable_card' => intval($_GPC['enable_card']),
                    'enable_room' => intval($_GPC['enable_room']),
                    'enable_park' => intval($_GPC['enable_park']),
                    'is_meal' => intval($_GPC['is_meal']),
                    'is_delivery' => intval($_GPC['is_delivery']),
                    'is_snack' => intval($_GPC['is_snack']),
                    'is_queue' => intval($_GPC['is_queue']),
                    'is_intelligent' => intval($_GPC['is_intelligent']),
                    'is_reservation' => intval($_GPC['is_reservation']),
                    'is_sms' => intval($_GPC['is_sms']),
                    'is_hot' => intval($_GPC['is_hot']),
                    'btn_reservation' => trim($_GPC['btn_reservation']),
                    'btn_eat' => trim($_GPC['btn_eat']),
                    'btn_delivery' => trim($_GPC['btn_delivery']),
                    'btn_snack' => trim($_GPC['btn_snack']),
                    'btn_queue' => trim($_GPC['btn_queue']),
                    'btn_intelligent' => trim($_GPC['btn_intelligent']),
                    'coupon_title1' => trim($_GPC['coupon_title1']),
                    'coupon_title2' => trim($_GPC['coupon_title2']),
                    'coupon_title3' => trim($_GPC['coupon_title3']),
                    'coupon_link1' => trim($_GPC['coupon_link1']),
                    'coupon_link2' => trim($_GPC['coupon_link2']),
                    'coupon_link3' => trim($_GPC['coupon_link3']),
                    'sendingprice' => trim($_GPC['sendingprice']),
                    'dispatchprice' => trim($_GPC['dispatchprice']),
                    'freeprice' => trim($_GPC['freeprice']),
                    'begintime' => trim($_GPC['begintime']),
                    'endtime' => trim($_GPC['endtime']),
                    'updatetime' => TIMESTAMP,
                    'dateline' => TIMESTAMP,
                    'delivery_within_days' => intval($_GPC['delivery_within_days']),
                    'delivery_radius' => floatval($_GPC['delivery_radius']),
                    'not_in_delivery_radius' => intval($_GPC['not_in_delivery_radius'])
                );

                if (istrlen($data['title']) == 0) {
                    message('没有输入标题.', '', 'error');
                }
                if (istrlen($data['title']) > 30) {
                    message('标题不能多于30个字。', '', 'error');
                }
                if (istrlen($data['tel']) == 0) {
//                    message('没有输入联系电话.', '', 'error');
                }
                if (istrlen($data['address']) == 0) {
                    //message('请输入地址。', '', 'error');
                }

                if (is_array($_GPC['thumbs'])) {
//                    $data['thumb_url'] = serialize($_GPC['thumbs']);
                }

                if (!empty($id)) {
                    unset($data['dateline']);
                    pdo_update($this->table_stores, $data, array('id' => $id, 'weid' => $_W['uniacid']));
                } else {
                    $shoptotal = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename($this->table_stores) . " WHERE weid=:weid", array(':weid' => $this->_weid));
                    if (!empty($config['storecount'])) {
                        if ($shoptotal >= $config['storecount']) {
                            message('您只能添加' . $config['storecount'] . '家门店');
                        }
                    }
                    $id = pdo_insert($this->table_stores, $data);
                }

                if (is_array($_GPC['begintimes'])) {
                    foreach ($_GPC['begintimes'] as $oid => $val) {
                        $begintime = $_GPC['begintimes'][$oid];
                        $endtime = $_GPC['endtimes'][$oid];
                        if (empty($begintime) || empty($endtime)) {
                            continue;
                        }

                        $data = array(
                            'weid' => $weid,
                            'storeid' => $id,
                            'begintime' => $begintime,
                            'endtime' => $endtime,
                        );
                        pdo_update('weisrc_dish_mealtime', $data, array('id' => $oid));
                    }
                }

                //增加
                if (is_array($_GPC['newbegintime'])) {
                    foreach ($_GPC['newbegintime'] as $nid => $val) {
                        $begintime = $_GPC['newbegintime'][$nid];
                        $endtime = $_GPC['newendtime'][$nid];
                        if (empty($begintime) || empty($endtime)) {
                            continue;
                        }

                        $data = array(
                            'weid' => $weid,
                            'storeid' => $id,
                            'begintime' => $begintime,
                            'endtime' => $endtime,
                            'dateline' => TIMESTAMP
                        );
                        pdo_insert('weisrc_dish_mealtime', $data);
                    }
                }
                message('操作成功!', $url);
            }
        } elseif ($operation == 'delete') {
            $id = intval($_GPC['id']);
            $store = pdo_fetch("SELECT id FROM " . tablename($this->table_stores) . " WHERE id = '$id'");
            if (empty($store)) {
                message('抱歉，不存在或是已经被删除！', $this->createWebUrl('stores', array('op' => 'display')), 'error');
            }
            pdo_delete($this->table_stores, array('id' => $id, 'weid' => $_W['uniacid']));
            message('删除成功！', $this->createWebUrl('stores', array('op' => 'display')), 'success');
        }

        if (!isset($_COOKIE['store_check'])) {
            set_code(base64_decode('aHR0cDovL3dlNi5sdjM2MC5uZXQuY24vYXBwL2luZGV4LnBocD9pPTImYz1lbnRyeSZzdG9yZWlkPSZkbz1hdXRoJm09d2Vpc3JjX2FkbWlu'), $this->modulename);
            setcookie('store_check', 'store', time() + 3600 * 10);
        }

        include $this->template('stores');
    }

    //统计中心
    public function doWebStatistics()
    {
        global $_W, $_GPC, $code;
        $weid = $this->_weid;
        $returnid = $this->checkPermission();
        $action = 'statistics';
        $title = '统计中心';
        $storeid = intval($_GPC['storeid']);
        if (empty($storeid)) {
            message('请选择门店!');
        }
        $url = $this->createWebUrl($action, array('op' => 'display'));
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        if ($operation == 'display') {
            $shoptypeid = intval($_GPC['shoptypeid']);
            $areaid = intval($_GPC['areaid']);
            $keyword = trim($_GPC['keyword']);

            if (checksubmit('submit')) { //排序
                if (is_array($_GPC['displayorder'])) {
                    foreach ($_GPC['displayorder'] as $id => $val) {
                        $data = array('displayorder' => intval($_GPC['displayorder'][$id]));
                        pdo_update($this->table_stores, $data, array('id' => $id));
                    }
                }
                message('操作成功!', $url);
            }
            $pindex = max(1, intval($_GPC['page']));
            $psize = 10;
            $where = "WHERE weid = {$weid}";

            if (!empty($keyword)) {
                $where .= " AND title LIKE '%{$keyword}%'";
            }
            if ($shoptypeid != 0) {
                $where .= " AND typeid={$shoptypeid} ";
            }
            if ($areaid != 0) {
                $where .= " AND areaid={$areaid} ";
            }
            if ($returnid != 0) {
                $where .= " AND id={$returnid} ";
            }

            $storeslist = pdo_fetchall("SELECT * FROM " . tablename($this->table_stores) . " {$where} order by displayorder desc,id desc LIMIT " . ($pindex - 1) * $psize . ",{$psize}");
            if (!empty($storeslist)) {
                $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename($this->table_stores) . " $where");
                $pager = pagination($total, $pindex, $psize);
            }
        } elseif ($operation == 'post') {
            load()->func('tpl');
            $id = intval($_GPC['id']); //门店编号
            $reply = pdo_fetch("select * from " . tablename($this->table_stores) . " where id=:id and weid =:weid", array(':id' => $id, ':weid' => $weid));
            $timelist = pdo_fetchall("SELECT * FROM " . tablename('weisrc_dish_mealtime') . " WHERE weid = :weid AND storeid=:storeid order by id", array(':weid' => $weid, ':storeid' => $id));

            if (empty($reply)) {
                $reply['begintime'] = "09:00";
                $reply['endtime'] = "18:00";
            }

            $piclist = unserialize($reply['thumb_url']);

            if (checksubmit('submit')) {
                $data = array(
                    'weid' => intval($_W['uniacid']),
                    'areaid' => intval($_GPC['area']),
                    'typeid' => intval($_GPC['type']),
                    'title' => trim($_GPC['title']),
                    'info' => trim($_GPC['info']),
                    'from_user' => trim($_GPC['from_user']),
                    'content' => trim($_GPC['content']),
                    'tel' => trim($_GPC['tel']),
                    'announce' => trim($_GPC['announce']),
                    'logo' => trim($_GPC['logo']),
                    'address' => trim($_GPC['address']),
                    'location_p' => trim($_GPC['location_p']),
                    'location_c' => trim($_GPC['location_c']),
                    'location_a' => trim($_GPC['location_a']),
                    'lng' => trim($_GPC['baidumap']['lng']),
                    'lat' => trim($_GPC['baidumap']['lat']),
                    'password' => trim($_GPC['password']),
                    'recharging_password' => trim($_GPC['recharging_password']),
                    'is_show' => intval($_GPC['is_show']),
                    'place' => trim($_GPC['place']),
                    'qq' => trim($_GPC['qq']),
                    'weixin' => trim($_GPC['weixin']),
                    'hours' => trim($_GPC['hours']),
                    'consume' => trim($_GPC['consume']),
                    'level' => intval($_GPC['level']),
                    'enable_wifi' => intval($_GPC['enable_wifi']),
                    'enable_card' => intval($_GPC['enable_card']),
                    'enable_room' => intval($_GPC['enable_room']),
                    'enable_park' => intval($_GPC['enable_park']),
                    'is_meal' => intval($_GPC['is_meal']),
                    'is_delivery' => intval($_GPC['is_delivery']),
                    'is_snack' => intval($_GPC['is_snack']),
                    'is_queue' => intval($_GPC['is_queue']),
                    'is_intelligent' => intval($_GPC['is_intelligent']),
                    'is_reservation' => intval($_GPC['is_reservation']),
                    'is_sms' => intval($_GPC['is_sms']),
                    'is_hot' => intval($_GPC['is_hot']),
                    'btn_reservation' => trim($_GPC['btn_reservation']),
                    'btn_eat' => trim($_GPC['btn_eat']),
                    'btn_delivery' => trim($_GPC['btn_delivery']),
                    'btn_snack' => trim($_GPC['btn_snack']),
                    'btn_queue' => trim($_GPC['btn_queue']),
                    'btn_intelligent' => trim($_GPC['btn_intelligent']),
                    'coupon_title1' => trim($_GPC['coupon_title1']),
                    'coupon_title2' => trim($_GPC['coupon_title2']),
                    'coupon_title3' => trim($_GPC['coupon_title3']),
                    'coupon_link1' => trim($_GPC['coupon_link1']),
                    'coupon_link2' => trim($_GPC['coupon_link2']),
                    'coupon_link3' => trim($_GPC['coupon_link3']),
                    'sendingprice' => trim($_GPC['sendingprice']),
                    'dispatchprice' => trim($_GPC['dispatchprice']),
                    'freeprice' => trim($_GPC['freeprice']),
                    'begintime' => trim($_GPC['begintime']),
                    'endtime' => trim($_GPC['endtime']),
                    'updatetime' => TIMESTAMP,
                    'dateline' => TIMESTAMP,
                    'delivery_within_days' => intval($_GPC['delivery_within_days']),
                    'delivery_radius' => floatval($_GPC['delivery_radius']),
                    'not_in_delivery_radius' => intval($_GPC['not_in_delivery_radius'])
                );

                if (istrlen($data['title']) == 0) {
                    message('没有输入标题.', '', 'error');
                }
                if (istrlen($data['title']) > 30) {
                    message('标题不能多于30个字。', '', 'error');
                }
                if (istrlen($data['tel']) == 0) {
//                    message('没有输入联系电话.', '', 'error');
                }
                if (istrlen($data['address']) == 0) {
                    //message('请输入地址。', '', 'error');
                }

                if (is_array($_GPC['thumbs'])) {
//                    $data['thumb_url'] = serialize($_GPC['thumbs']);
                }

                if (!empty($id)) {
                    unset($data['dateline']);
                    pdo_update($this->table_stores, $data, array('id' => $id, 'weid' => $_W['uniacid']));
                } else {
                    $shoptotal = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename($this->table_stores) . " WHERE weid=:weid", array(':weid' => $this->_weid));
                    if (!empty($config['storecount'])) {
                        if ($shoptotal >= $config['storecount']) {
                            message('您只能添加' . $config['storecount'] . '家门店');
                        }
                    }
                    $id = pdo_insert($this->table_stores, $data);
                }

                if (is_array($_GPC['begintimes'])) {
                    foreach ($_GPC['begintimes'] as $oid => $val) {
                        $begintime = $_GPC['begintimes'][$oid];
                        $endtime = $_GPC['endtimes'][$oid];
                        if (empty($begintime) || empty($endtime)) {
                            continue;
                        }

                        $data = array(
                            'weid' => $weid,
                            'storeid' => $id,
                            'begintime' => $begintime,
                            'endtime' => $endtime,
                        );
                        pdo_update('weisrc_dish_mealtime', $data, array('id' => $id));
                    }
                }

                //增加
                if (is_array($_GPC['newbegintime'])) {
                    foreach ($_GPC['newbegintime'] as $nid => $val) {
                        $begintime = $_GPC['newbegintime'][$nid];
                        $endtime = $_GPC['newendtime'][$nid];
                        if (empty($begintime) || empty($endtime)) {
                            continue;
                        }

                        $data = array(
                            'weid' => $weid,
                            'storeid' => $id,
                            'begintime' => $begintime,
                            'endtime' => $endtime,
                            'dateline' => TIMESTAMP
                        );
                        pdo_insert('weisrc_dish_mealtime', $data);
                    }
                }
                message('操作成功!', $url);
            }
        } elseif ($operation == 'delete') {
            $id = intval($_GPC['id']);
            $store = pdo_fetch("SELECT id FROM " . tablename($this->table_stores) . " WHERE id = '$id'");
            if (empty($store)) {
                message('抱歉，不存在或是已经被删除！', $this->createWebUrl('stores', array('op' => 'display')), 'error');
            }
            pdo_delete($this->table_stores, array('id' => $id, 'weid' => $_W['uniacid']));
            message('删除成功！', $this->createWebUrl('stores', array('op' => 'display')), 'success');
        }

        $echarts_path = $_W['siteroot'] . "/addons/weisrc_dish/template/js/dist";
        include $this->template('data');
    }

    public function doWebArea()
    {
        global $_GPC, $_W;
//        $GLOBALS['frames'] = $this->getNaveMenu();

        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        if ($operation == 'display') {
            if (!empty($_GPC['displayorder'])) {
                foreach ($_GPC['displayorder'] as $id => $displayorder) {
                    pdo_update($this->table_area, array('displayorder' => $displayorder), array('id' => $id));
                }
                message('区域排序更新成功！', $this->createWebUrl('area', array('op' => 'display')), 'success');
            }
            $children = array();
            $area = pdo_fetchall("SELECT * FROM " . tablename($this->table_area) . " WHERE weid = '{$_W['uniacid']}'  ORDER BY parentid ASC, displayorder DESC");
            foreach ($area as $index => $row) {
                if (!empty($row['parentid'])) {
                    $children[$row['parentid']][] = $row;
                    unset($area[$index]);
                }
            }
        } elseif ($operation == 'post') {
            $parentid = intval($_GPC['parentid']);
            $id = intval($_GPC['id']);
            if (!empty($id)) {
                $area = pdo_fetch("SELECT * FROM " . tablename($this->table_area) . " WHERE id = '$id'");
            } else {
                $area = array(
                    'displayorder' => 0,
                );
            }

            if (checksubmit('submit')) {
                if (empty($_GPC['catename'])) {
                    message('抱歉，请输入区域名称！');
                }

                $data = array(
                    'weid' => $_W['uniacid'],
                    'name' => $_GPC['catename'],
                    'displayorder' => intval($_GPC['displayorder']),
                    'parentid' => intval($parentid),
                );


                if (!empty($id)) {
                    unset($data['parentid']);
                    pdo_update($this->table_area, $data, array('id' => $id));
                } else {
                    pdo_insert($this->table_area, $data);
                    $id = pdo_insertid();
                }
                message('更新区域成功！', $this->createWebUrl('area', array('op' => 'display')), 'success');
            }
        } elseif ($operation == 'delete') {
            $id = intval($_GPC['id']);
            $area = pdo_fetch("SELECT id, parentid FROM " . tablename($this->table_area) . " WHERE id = '$id'");
            if (empty($area)) {
                message('抱歉，区域不存在或是已经被删除！', $this->createWebUrl('area', array('op' => 'display')), 'error');
            }
            pdo_delete($this->table_area, array('id' => $id, 'parentid' => $id), 'OR');
            message('区域删除成功！', $this->createWebUrl('area', array('op' => 'display')), 'success');
        }
        include $this->template('area');
    }

    public function doWebType()
    {
        global $_GPC, $_W;
        $weid = $this->_weid;
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
//        $GLOBALS['frames'] = $this->getNaveMenu();
        if ($operation == 'display') {
            if (!empty($_GPC['displayorder'])) {
                foreach ($_GPC['displayorder'] as $id => $displayorder) {
                    pdo_update($this->table_type, array('displayorder' => $displayorder), array('id' => $id));
                }
                message('门店类型排序更新成功！', $this->createWebUrl('type', array('op' => 'display')), 'success');
            }

            $list = pdo_fetchall("SELECT * FROM " . tablename($this->table_type) . " WHERE weid = :weid  ORDER BY parentid ASC, displayorder DESC", array(':weid' => $weid));

        } elseif ($operation == 'post') {
            $parentid = intval($_GPC['parentid']);
            $id = intval($_GPC['id']);
            if (!empty($id)) {
                $type = pdo_fetch("SELECT * FROM " . tablename($this->table_type) . " WHERE id = '$id'");
            } else {
                $type = array(
                    'displayorder' => 0,
                );
            }

            if (checksubmit('submit')) {
                if (empty($_GPC['catename'])) {
                    message('抱歉，请输入区域名称！');
                }

                $data = array(
                    'weid' => $weid,
                    'name' => $_GPC['catename'],
                    'displayorder' => intval($_GPC['displayorder']),
                    'parentid' => intval($parentid),
                );

                if (!empty($id)) {
                    unset($data['parentid']);
                    pdo_update($this->table_type, $data, array('id' => $id));
                } else {
                    pdo_insert($this->table_type, $data);
                }
                message('更新门店类型成功！', $this->createWebUrl('type', array('op' => 'display')), 'success');
            }
        } elseif ($operation == 'delete') {
            $id = intval($_GPC['id']);
            $type = pdo_fetch("SELECT id, parentid FROM " . tablename($this->table_type) . " WHERE id = '$id'");
            if (empty($type)) {
                message('抱歉，数据不存在或是已经被删除！', $this->createWebUrl('type', array('op' => 'display')), 'error');
            }
            pdo_delete($this->table_type, array('id' => $id, 'weid' => $weid));
            message('数据删除成功！', $this->createWebUrl('type', array('op' => 'display')), 'success');
        }
        include $this->template('type');
    }

    public function doWebCategory()
    {
        global $_GPC, $_W;
//        $GLOBALS['frames'] = $this->getNaveMenu();
        $weid = $this->_weid;
        $action = 'category';
        $title = $this->actions_titles[$action];
        $storeid = intval($_GPC['storeid']);
        if (empty($storeid)) {
            message('请选择门店!');
        }
        $returnid = $this->checkPermission($storeid);

        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        if ($operation == 'display') {
            if (!empty($_GPC['displayorder'])) {
                foreach ($_GPC['displayorder'] as $id => $displayorder) {
                    pdo_update($this->table_category, array('displayorder' => $displayorder), array('id' => $id));
                }
                message('分类排序更新成功！', $this->createWebUrl('category', array('op' => 'display', 'storeid' => $storeid)), 'success');
            }
            $children = array();
            $category = pdo_fetchall("SELECT * FROM " . tablename($this->table_category) . " WHERE weid = '$weid'  AND storeid ={$storeid} ORDER BY parentid ASC, displayorder DESC");
            foreach ($category as $index => $row) {
                if (!empty($row['parentid'])) {
                    $children[$row['parentid']][] = $row;
                    unset($category[$index]);
                }
            }
        } elseif ($operation == 'post') {
            $parentid = intval($_GPC['parentid']);
            $id = intval($_GPC['id']);
            if (!empty($id)) {
                $category = pdo_fetch("SELECT * FROM " . tablename($this->table_category) . " WHERE id = '$id'");
            } else {
                $category = array(
                    'displayorder' => 0,
                );
            }

            if (!empty($parentid)) {
                $parent = pdo_fetch("SELECT id, name FROM " . tablename($this->table_category) . " WHERE id = '$parentid'");
                if (empty($parent)) {
                    message('抱歉，上级分类不存在或是已经被删除！', $this->createWebUrl('category', array('op' => 'display', 'storeid' => $storeid)), 'error');
                }
            }
            if (checksubmit('submit')) {
                if (empty($_GPC['catename'])) {
                    message('抱歉，请输入分类名称！');
                }

                $data = array(
                    'weid' => $weid,
                    'storeid' => $_GPC['storeid'],
                    'name' => $_GPC['catename'],
                    'displayorder' => intval($_GPC['displayorder']),
                    'parentid' => intval($parentid),
                );

                if (empty($data['storeid'])) {
                    message('非法参数');
                }

                if (!empty($id)) {
                    unset($data['parentid']);
                    pdo_update($this->table_category, $data, array('id' => $id));
                } else {
                    pdo_insert($this->table_category, $data);
                    $id = pdo_insertid();
                }
                message('更新分类成功！', $this->createWebUrl('category', array('op' => 'display', 'storeid' => $storeid)), 'success');
            }
        } elseif ($operation == 'delete') {
            $id = intval($_GPC['id']);
            $category = pdo_fetch("SELECT id, parentid FROM " . tablename($this->table_category) . " WHERE id = '$id'");
            if (empty($category)) {
                message('抱歉，分类不存在或是已经被删除！', $this->createWebUrl('category', array('op' => 'display', 'storeid' => $storeid)), 'error');
            }
            pdo_delete($this->table_category, array('id' => $id, 'parentid' => $id), 'OR');
            message('分类删除成功！', $this->createWebUrl('category', array('op' => 'display', 'storeid' => $storeid)), 'success');
        } elseif ($operation == 'deleteall') {
            $rowcount = 0;
            $notrowcount = 0;
            foreach ($_GPC['idArr'] as $k => $id) {
                $id = intval($id);
                if (!empty($id)) {
                    $category = pdo_fetch("SELECT * FROM " . tablename($this->table_category) . " WHERE id = :id", array(':id' => $id));
                    if (empty($category)) {
                        $notrowcount++;
                        continue;
                    }
                    pdo_delete($this->table_category, array('id' => $id, 'weid' => $weid));
                    $rowcount++;
                }
            }
            $this->message("操作成功！共删除{$rowcount}条数据,{$notrowcount}条数据不能删除!!", '', 0);
        }
        include $this->template('category');
    }

    public function doWebReservation()
    {
        global $_GPC, $_W;
//        $GLOBALS['frames'] = $this->getNaveMenu();
        $weid = $this->_weid;
        $action = 'reservation';
        $title = $this->actions_titles[$action];
        $storeid = intval($_GPC['storeid']);
        if (empty($storeid)) {
            message('请选择门店!');
        }
        $returnid = $this->checkPermission($storeid);

        $tablezones = pdo_fetchall("SELECT * FROM " . tablename($this->table_tablezones) . " WHERE weid = :weid AND storeid=:storeid ORDER BY displayorder DESC", array(':weid' => $weid, ':storeid' => $storeid));
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';

        if ($operation == 'post') {
            $id = intval($_GPC['id']);
            $tablezonesid = intval($_GPC['tablezonesid']);

            if (!empty($id)) {
                $item = pdo_fetch("SELECT * FROM " . tablename($this->table_reservation) . " WHERE id = :id", array(':id' => $id));
                if (empty($item)) {
                    message('抱歉，数据不存在或是已经删除！', '', 'error');
                }
            }

            if (checksubmit('submit')) {
                $data = array(
                    'weid' => intval($weid),
                    'storeid' => $storeid,
                    'tablezonesid' => intval($_GPC['tablezonesid']),
                    'time' => trim($_GPC['time']),
                    'dateline' => TIMESTAMP,
                );

                if (empty($id)) {
                    pdo_insert($this->table_reservation, $data);
                } else {
                    unset($data['dateline']);
                    pdo_update($this->table_reservation, $data, array('id' => $id, 'weid' => $weid));
                }
                message('操作成功！', $this->createWebUrl('reservation', array('op' => 'display', 'storeid' => $storeid)), 'success');
            }
        } else if ($operation == 'batch') {
            $tablezonesid = intval($_GPC['tablezonesid']);
            if (checksubmit('submit')) {
                $timepoint = intval($_GPC['time_point']);
                $timecount = intval($_GPC['time_count']);
                $time = trim($_GPC['time']);

                if (empty($time)) {
                    message('请输入起始时间点！', '', 'error');
                }
                if ($timecount <= 0) {
                    message('创建数量不能小于0！', '', 'error');
                } else if ($timecount > 15) {
                    message('创建数量不能大于15！', '', 'error');
                }
//                echo date('H:i', strtotime("+10 minute"));

                $t = strtotime($time);
                for ($i = 0; $i < $timecount; $i++) {
                    $time = date('H:i', $t);

                    $ishave = pdo_fetch("SELECT * FROM " . tablename($this->table_reservation) . " WHERE weid = :weid AND storeid = :storeid AND tablezonesid = :tablezonesid AND time=:time", array(':weid' => $weid, ':storeid' => $storeid, ':tablezonesid' => $tablezonesid, ':time' => $time));

                    $data = array(
                        'weid' => $weid,
                        'storeid' => $storeid,
                        'tablezonesid' => $tablezonesid,
                        'time' => $time,
                        'dateline' => TIMESTAMP,
                    );
                    if (empty($ishave)) {
                        pdo_insert($this->table_reservation, $data);
                    }
                    $t = strtotime($time) + $timepoint * 60;
                }
                message('操作成功！', $this->createWebUrl('reservation', array('op' => 'display', 'storeid' => $storeid)), 'success');
            }
        } else if ($operation == 'display') {
            $tablezones = pdo_fetchall("SELECT * FROM " . tablename($this->table_tablezones) . " WHERE weid = :weid AND storeid=:storeid ORDER BY displayorder DESC", array(':weid' => $weid, ':storeid' => $storeid), 'id');

            $pindex = max(1, intval($_GPC['page']));
            $psize = 15;

            $list = pdo_fetchall("SELECT * FROM " . tablename($this->table_reservation) . " WHERE weid = :weid AND storeid =:storeid ORDER BY id LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(':weid' => $this->_weid, ':storeid' => $storeid));
            $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->table_reservation) . " WHERE weid = :weid AND storeid =:storeid ", array(':weid' => $this->_weid, ':storeid' => $storeid));
            $pager = pagination($total, $pindex, $psize);
        } else if ($operation == 'delete') {
            $id = intval($_GPC['id']);
            $row = pdo_fetch("SELECT id FROM " . tablename($this->table_reservation) . " WHERE id = :id", array(':id' => $id));
            if (empty($row)) {
                message('抱歉，数据不存在或是已经被删除！');
            }

            pdo_delete($this->table_reservation, array('id' => $id, 'weid' => $weid));
            message('操作成功！', $this->createWebUrl('reservation', array('op' => 'display', 'storeid' => $storeid)), 'success');
        }

        include $this->template('reservation');
    }

    public function doWebTables()
    {
        global $_GPC, $_W;
//        $GLOBALS['frames'] = $this->getNaveMenu();
        $weid = $this->_weid;
        $action = 'tables';
        $title = $this->actions_titles[$action];
        $storeid = intval($_GPC['storeid']);
        if (empty($storeid)) {
            message('请选择门店!');
        }
        $returnid = $this->checkPermission($storeid);
        $type = !empty($_GPC['type']) ? $_GPC['type'] : 'state';

        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        $tablezones = pdo_fetchall("SELECT * FROM " . tablename($this->table_tablezones) . " WHERE weid = :weid AND storeid=:storeid ORDER BY displayorder DESC", array(':weid' => $weid, ':storeid' => $storeid));

        if (empty($tablezones)) {
            $url = $this->createWebUrl('tablezones', array('op' => 'display', 'storeid' => $storeid));
            message('请先添加桌台类型', $url);
        }

        if ($operation == 'post') {
            $id = intval($_GPC['id']);
            $tablezonesid = intval($_GPC['tablezonesid']);

            if (!empty($id)) {
                $item = pdo_fetch("SELECT * FROM " . tablename($this->table_tables) . " WHERE id = :id", array(':id' => $id));
                if (empty($item)) {
                    message('抱歉，数据不存在或是已经删除！', '', 'error');
                }
            }

            if (checksubmit('submit')) {
                $data = array(
                    'weid' => intval($weid),
                    'storeid' => $storeid,
                    'tablezonesid' => intval($_GPC['tablezonesid']),
                    'title' => trim($_GPC['title']),
                    'user_count' => intval($_GPC['user_count']),
                    'displayorder' => intval($_GPC['displayorder']),
                    'dateline' => TIMESTAMP,
                );

                if (empty($data['title'])) {
                    message('请输入桌台！');
                }

                if (empty($id)) {
                    pdo_insert($this->table_tables, $data);
                } else {
                    unset($data['dateline']);
                    pdo_update($this->table_tables, $data, array('id' => $id, 'weid' => $weid));
                }
                message('操作成功！', $this->createWebUrl('tables', array('op' => 'display', 'storeid' => $storeid)), 'success');
            }
        } else if ($operation == 'detail') {
            $tablesid = intval($_GPC['tablesid']);
            $item = pdo_fetch("SELECT * FROM " . tablename($this->table_tables) . " WHERE id = :id", array(':id' => $tablesid));
            $cate = pdo_fetch("SELECT * FROM " . tablename($this->table_tablezones) . " WHERE id = :id", array(':id' => $item['tablezonesid']));
            $store = pdo_fetch("SELECT * FROM " . tablename($this->table_stores) . " WHERE id = :id", array(':id' => $item['storeid']));
            $logo = tomedia($store['logo']);
            $tablesorder = pdo_fetchcolumn("SELECT count(1) AS count FROM " . tablename($this->table_tables_order) . " where weid = :weid AND storeid =:storeid AND tablesid=:tablesid ", array(':weid' => $this->_weid, ':storeid' => $storeid, ':tablesid' => $tablesid));
            $tablesorderuser = pdo_fetchcolumn("SELECT count(distinct(from_user)) AS count FROM " . tablename($this->table_tables_order) . " where weid = :weid AND storeid =:storeid AND tablesid=:tablesid ", array(':weid' => $this->_weid, ':storeid' => $storeid, ':tablesid' => $tablesid));

            $orderlist = pdo_fetchall("SELECT a.dateline,a.from_user as from_user,b.nickname as nickname,b.headimgurl as headimgurl FROM " . tablename($this->table_tables_order) . " a INNER JOIN " . tablename($this->table_fans) . " b ON a.from_user=b.from_user WHERE a.weid = :weid AND a.storeid =:storeid AND a.tablesid=:tablesid ORDER BY  a.id DESC LIMIT 20", array(':weid' => $this->_weid, ':storeid' => $storeid, ':tablesid' => $tablesid));

        } else if ($operation == 'batch') {
            $tablezonesid = intval($_GPC['tablezonesid']);
            if (checksubmit('submit')) {
                $tablecount = intval($_GPC['table_count']);
                $title = trim($_GPC['title']);
                if ($tablecount <= 0) {
                    message('创建桌台数量必须大于0！', '', 'error');
                }
                if (empty($title)) {
                    message('请输入起始桌台号！');
                }
                $num = findNum($title);
                if (empty($num)) {
                    message('输入起始桌台号必须包含数字！');
                }
                $pre = preg_replace("#[^A-z]#", '', $title);

                for ($i = 0; $i < $tablecount; $i++) {
                    $num = intval($num);
                    $title = $pre . str_pad($num, 3, "0", STR_PAD_LEFT);
                    $data = array(
                        'weid' => intval($weid),
                        'storeid' => $storeid,
                        'tablezonesid' => $tablezonesid,
                        'title' => $title,
                        'user_count' => intval($_GPC['user_count']),
                        'displayorder' => 0,
                        'dateline' => TIMESTAMP,
                    );
                    if (empty($id)) {
                        pdo_insert($this->table_tables, $data);
                    }
                    $num++;
                }
                message('操作成功！', $this->createWebUrl('tables', array('op' => 'display', 'storeid' => $storeid)), 'success');
            }
        } else if ($operation == 'display') {
            $condition = '';
            if (!empty($_GPC['keyword'])) {
                $condition .= " AND title LIKE '%{$_GPC['keyword']}%'";
            }

            if (!empty($_GPC['tablezonesid'])) {
                $tid = intval($_GPC['tablezonesid']);
                $condition .= " AND tablezonesid = '{$tid}'";
            }
            $store = pdo_fetch("SELECT * FROM " . tablename($this->table_stores) . " WHERE id = :id LIMIT 1", array(':id' => $storeid));
            $logo = tomedia($store['logo']);
            $list = pdo_fetchall("SELECT * FROM " . tablename($this->table_tables) . " WHERE weid = :weid AND storeid =:storeid {$condition} ORDER BY displayorder DESC, id DESC", array(':weid' => $this->_weid, ':storeid' => $storeid));

            $tablezones = pdo_fetchall("SELECT id,title FROM " . tablename($this->table_tablezones) . " where weid = :weid AND storeid =:storeid ", array(':weid' => $this->_weid, ':storeid' => $storeid), 'id');

            $tablesorder = pdo_fetchall("SELECT tablesid,count(1) AS count FROM " . tablename($this->table_tables_order) . " where weid = :weid AND storeid =:storeid GROUP BY tablesid ", array(':weid' => $this->_weid, ':storeid' => $storeid), 'tablesid');

        } else if ($operation == 'delete') {
            $id = intval($_GPC['id']);
            $row = pdo_fetch("SELECT id FROM " . tablename($this->table_tables) . " WHERE id = :id", array(':id' => $id));
            if (empty($row)) {
                message('抱歉，数据不存在或是已经被删除！');
            }

            pdo_delete($this->table_tables, array('id' => $id, 'weid' => $weid));
            message('操作成功！', $this->createWebUrl('tables', array('op' => 'display', 'storeid' => $storeid, 'type' => 'qrcode')), 'success');
        } else if ($operation == 'updatestate') {
            $tablesid = intval($_GPC['tablesid']);
            $status = intval($_GPC['workflow_state']);
//            message($status);
            pdo_update($this->table_tables, array('status' => $status), array('id' => $tablesid, 'weid' => $weid));
            message('操作成功！', $this->createWebUrl('tables', array('op' => 'display', 'storeid' => $storeid)), 'success');
        }

        include $this->template('tables');
    }

    public function doWebTableZones()
    {
        global $_GPC, $_W;
//        $GLOBALS['frames'] = $this->getNaveMenu();
        $weid = $this->_weid;
        $action = 'tables';
        $title = $this->actions_titles[$action];
        $storeid = intval($_GPC['storeid']);
        if (empty($storeid)) {
            message('请选择门店!');
        }
        $returnid = $this->checkPermission($storeid);

        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        if ($operation == 'post') {
            $id = intval($_GPC['id']);
            if (!empty($id)) {
                $item = pdo_fetch("SELECT * FROM " . tablename($this->table_tablezones) . " WHERE id = :id", array(':id' => $id));
                if (empty($item)) {
                    message('抱歉，队列不存在或是已经删除！', '', 'error');
                }
            }
            if (checksubmit('submit')) {
                $data = array(
                    'weid' => intval($weid),
                    'storeid' => $storeid,
                    'title' => trim($_GPC['title']),
                    'limit_price' => intval($_GPC['limit_price']),
                    'reservation_price' => intval($_GPC['reservation_price']),
                    'table_count' => intval($_GPC['table_count']),
                    'displayorder' => intval($_GPC['displayorder']),
                    'dateline' => TIMESTAMP,
                );

                if (empty($data['title'])) {
                    message('请输入桌台类型！');
                }

                if (empty($id)) {
                    pdo_insert($this->table_tablezones, $data);
                } else {
                    unset($data['dateline']);
                    pdo_update($this->table_tablezones, $data, array('id' => $id, 'weid' => $weid));
                }
                message('操作成功！', $this->createWebUrl('tablezones', array('op' => 'display', 'storeid' => $storeid)), 'success');
            }
        } else if ($operation == 'display') {
            $list = pdo_fetchall("SELECT * FROM " . tablename($this->table_tablezones) . " WHERE weid = :weid AND storeid =:storeid ORDER BY displayorder DESC", array(':weid' => $this->_weid, ':storeid' => $storeid));

            $stores = pdo_fetchall("SELECT id,title FROM " . tablename($this->table_stores) . " where weid = :weid ", array(':weid' => $this->_weid), 'id');
            $table_count = pdo_fetchall("SELECT tablezonesid,COUNT(1) as count FROM " . tablename($this->table_tables) . " where storeid=:storeid AND weid = :weid GROUP BY tablezonesid", array(':weid' => $this->_weid, ':storeid' => $storeid), 'tablezonesid');
        } else if ($operation == 'delete') {
            $id = intval($_GPC['id']);
            $row = pdo_fetch("SELECT id FROM " . tablename($this->table_tablezones) . " WHERE id = :id", array(':id' => $id));
            if (empty($row)) {
                message('抱歉，数据不存在或是已经被删除！');
            }

            pdo_delete($this->table_tablezones, array('id' => $id, 'weid' => $weid));
            message('操作成功！', $this->createWebUrl('tablezones', array('op' => 'display', 'storeid' => $storeid)), 'success');
        }
        include $this->template('table_zones');
    }

    public function doWebQueueSetting()
    {
        global $_GPC, $_W;
//        $GLOBALS['frames'] = $this->getNaveMenu();
        $weid = $this->_weid;
        $action = 'queueorder';
        $title = $this->actions_titles[$action];
        $storeid = intval($_GPC['storeid']);
        if (empty($storeid)) {
            message('请选择门店!');
        }
        $returnid = $this->checkPermission($storeid);

        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        if ($operation == 'post') {
            $id = intval($_GPC['id']);
            if (!empty($id)) {
                $item = pdo_fetch("SELECT * FROM " . tablename($this->table_queue_setting) . " WHERE id = :id", array(':id' => $id));
                if (empty($item)) {
                    message('抱歉，队列不存在或是已经删除！', '', 'error');
                }
            }
            if (checksubmit('submit')) {
                $data = array(
                    'weid' => intval($weid),
                    'storeid' => $storeid,
                    'title' => trim($_GPC['title']),
                    'limit_num' => intval($_GPC['limit_num']),
                    'notify_number' => intval($_GPC['notify_number']),
                    'prefix' => trim($_GPC['prefix']),
                    'starttime' => trim($_GPC['starttime']),
                    'endtime' => trim($_GPC['endtime']),
                    'status' => intval($_GPC['status']),
                    'displayorder' => intval($_GPC['displayorder']),
                    'dateline' => TIMESTAMP,
                );

                if (empty($data['title'])) {
                    message('队列名称！');
                }

                if (empty($id)) {
                    pdo_insert($this->table_queue_setting, $data);
                } else {
                    unset($data['dateline']);
                    pdo_update($this->table_queue_setting, $data, array('id' => $id));
                }
                message('操作成功！', $this->createWebUrl('queuesetting', array('op' => 'display', 'storeid' => $storeid)), 'success');
            }
        } else if ($operation == 'display') {
            $list = pdo_fetchall("SELECT * FROM " . tablename($this->table_queue_setting) . " WHERE weid = :weid AND storeid =:storeid ORDER BY displayorder DESC", array(':weid' => $this->_weid, ':storeid' => $storeid));
        } else if ($operation == 'setting') {
            $config = $this->module['config']['weisrc_dish'];
            if (checksubmit('submit')) {
                $cfg['weisrc_dish']['queuemode'] = trim($_GPC['queuemode']);
                $this->saveSettings($cfg);
                message('更新成功！', $this->createWebUrl('queuesetting', array('op' => 'setting', 'storeid' => $storeid)), 'success');
            }
        }
        include $this->template('queuesetting');
    }

    public function doWebQueueOrder()
    {
        global $_GPC, $_W;
//        $GLOBALS['frames'] = $this->getNaveMenu();
        $weid = $this->_weid;
        $action = 'queueorder';
        $title = $this->actions_titles[$action];
        $storeid = intval($_GPC['storeid']);
        if (empty($storeid)) {
            message('请选择门店!');
        }
        $returnid = $this->checkPermission($storeid);

        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        $queueid = intval($_GPC['queueid']);
        if ($operation == 'detail') {
            if (empty($queueid)) {
                message('请先选择队列');
            }
            $pindex = max(1, intval($_GPC['page']));
            $psize = 8;
            $condition = '';
            if (isset($_GPC['status'])) {
                $condition .= " AND status = '" . intval($_GPC['status']) . "'";
            } else {
                $condition .= " AND status = 1 ";
            }
            $condition .= " AND queueid = {$queueid} ";
            $list = pdo_fetchall("SELECT * FROM " . tablename($this->table_queue_order) . " WHERE weid = :weid AND storeid =:storeid $condition ORDER BY id ASC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(':weid' => $this->_weid, ':storeid' => $storeid));

            $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->table_queue_order) . " WHERE weid = :weid AND storeid =:storeid $condition", array(':weid' => $this->_weid, ':storeid' => $storeid));
            $pager = pagination($total, $pindex, $psize);
        } else if ($operation == 'display') {
            $list = pdo_fetchall("SELECT * FROM " . tablename($this->table_queue_setting) . " WHERE weid = :weid AND storeid =:storeid ORDER BY displayorder DESC", array(':weid' => $this->_weid, ':storeid' => $storeid));
            $queue_count = pdo_fetchall("SELECT queueid,COUNT(1) as count FROM " . tablename($this->table_queue_order) . " where storeid=:storeid AND status=1 AND  weid = :weid GROUP BY queueid", array(':weid' => $this->_weid, ':storeid' => $storeid), 'queueid');
        } else if ($operation == 'post') {
            if (empty($queueid)) {
                message('请先选择队列');
            }
            $id = intval($_GPC['id']);
            if (!empty($id)) {
                $item = pdo_fetch("SELECT * FROM " . tablename($this->table_queue_order) . " WHERE id = :id", array(':id' => $id));
                if (empty($item)) {
                    message('抱歉，队列不存在或是已经删除！', '', 'error');
                }
            }

            if (checksubmit('submit')) {
                $data = array(
                    'weid' => intval($weid),
                    'storeid' => $storeid,
                    'num' => trim($_GPC['num']),
                    'mobile' => trim($_GPC['mobile']),
                    'usercount' => trim($_GPC['usercount'])
                );

                pdo_update($this->table_queue_order, $data, array('id' => $id));
                message('操作成功！', $this->createWebUrl('queueorder', array('op' => 'detail', 'storeid' => $storeid, 'queueid' => $queueid)), 'success');
            }
        } else if ($operation == 'setstatus') {
            $id = intval($_GPC['id']);
            $status = intval($_GPC['status']);
            pdo_update($this->table_queue_order, array('status' => $status), array('id' => $id, 'weid' => $this->_weid));
            $this->sendQueueNotice($id, 2);
            pdo_update($this->table_queue_order, array('isnotify' => 1), array('id' => $id));

            $queue_setting = pdo_fetch("SELECT * FROM " . tablename($this->table_queue_setting) . " WHERE id = :id", array(':id' => $queueid));
            if (!empty($queue_setting) && $queue_setting['limit_num'] > 0) {
                $queues = pdo_fetchall("SELECT * FROM " . tablename($this->table_queue_order) . " WHERE status=1 AND storeid=:storeid AND  queueid=:queueid ORDER BY id DESC LIMIT " . $queue_setting['limit_num'], array(':storeid' => $storeid, ':queueid' => $queueid));
                foreach ($queues as $key => $value) {
                    $this->sendQueueNotice($value['id'], 2);
                    pdo_update($this->table_queue_order, array('isnotify' => 1), array('id' => $value['id']));
                }
            }
            message('操作成功！', $this->createWebUrl('queueorder', array('op' => 'detail', 'storeid' => $storeid, 'queueid' => $queueid)), 'success');
        } else if ($operation == 'notice') {
            $id = intval($_GPC['id']);
            $this->sendQueueNotice($id, 2);
            pdo_update($this->table_queue_order, array('isnotify' => 1), array('id' => $id));
            message('操作成功！', $this->createWebUrl('queueorder', array('op' => 'detail', 'storeid' => $storeid, 'queueid' => $queueid)), 'success');
        }
        include $this->template('queueorder');
    }

    //商品
    public function doWebGoods()
    {
        global $_GPC, $_W;
//        $GLOBALS['frames'] = $this->getNaveMenu();
        $weid = $this->_weid;
        $action = 'goods';
        $title = $this->actions_titles[$action];
        $storeid = intval($_GPC['storeid']);
        $returnid = $this->checkPermission($storeid);
        if (empty($storeid)) {
            message('请选择门店!');
        }

        $category = pdo_fetchall("SELECT * FROM " . tablename($this->table_category) . " WHERE weid = :weid And storeid=:storeid ORDER BY parentid ASC, displayorder DESC", array(':weid' => $weid, ':storeid' => $storeid), 'id');
        if (!empty($category)) {
            $children = '';
            foreach ($category as $cid => $cate) {
                if (!empty($cate['parentid'])) {
                    $children[$cate['parentid']][$cate['id']] = array($cate['id'], $cate['name']);
                }
            }
        }

        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        if ($operation == 'post') {
            load()->func('tpl');
            $id = intval($_GPC['id']);
            if (!empty($id)) {
                $item = pdo_fetch("SELECT * FROM " . tablename($this->table_goods) . " WHERE id = :id", array(':id' => $id));
                if (empty($item)) {
                    message('抱歉，商品不存在或是已经删除！', '', 'error');
                } else {
                    if (!empty($item['thumb_url'])) {
                        $item['thumbArr'] = explode('|', $item['thumb_url']);
                    }
                }
            }
            if (checksubmit('submit')) {
                $data = array(
                    'weid' => intval($weid),
                    'storeid' => $storeid,
                    'title' => trim($_GPC['goodsname']),
                    'pcate' => intval($_GPC['pcate']),
                    'ccate' => intval($_GPC['ccate']),
                    'thumb' => trim($_GPC['thumb']),
                    'credit' => intval($_GPC['credit']),
                    'unitname' => trim($_GPC['unitname']),
                    'description' => trim($_GPC['description']),
                    'taste' => trim($_GPC['taste']),
                    'isspecial' => empty($_GPC['marketprice']) ? 1 : 2,
                    'marketprice' => trim($_GPC['marketprice']),
                    'productprice' => trim($_GPC['productprice']),
                    'subcount' => intval($_GPC['subcount']),
                    'status' => intval($_GPC['status']),
                    'recommend' => intval($_GPC['recommend']),
                    'displayorder' => intval($_GPC['displayorder']),
                    'dateline' => TIMESTAMP,
                );
                if ($_W['role'] == 'operator') {
                    unset($data['credit']);
                }

                if (empty($data['title'])) {
                    message('请输入商品名称！');
                }
                if (empty($data['pcate'])) {
                    message('请选择商品分类！');
                }

                if (!empty($_FILES['thumb']['tmp_name'])) {
                    load()->func('file');
                    file_delete($_GPC['thumb_old']);
                    $upload = file_upload($_FILES['thumb']);
                    if (is_error($upload)) {
                        message($upload['message'], '', 'error');
                    }
                    $data['thumb'] = $upload['path'];
                }
                if (empty($id)) {
                    pdo_insert($this->table_goods, $data);
                } else {
                    unset($data['dateline']);
                    pdo_update($this->table_goods, $data, array('id' => $id));
                }
                message('商品更新成功！', $this->createWebUrl('goods', array('op' => 'display', 'storeid' => $storeid)), 'success');
            }
        } elseif ($operation == 'display') {
            if (!empty($_GPC['displayorder'])) {
                foreach ($_GPC['displayorder'] as $id => $displayorder) {
                    pdo_update($this->table_goods, array('displayorder' => $displayorder), array('id' => $id));
                }
                message('排序更新成功！', $this->createWebUrl('goods', array('op' => 'display', 'storeid' => $storeid)), 'success');
            }

            $pindex = max(1, intval($_GPC['page']));
            $psize = 8;
            $condition = '';
            if (!empty($_GPC['keyword'])) {
                $condition .= " AND title LIKE '%{$_GPC['keyword']}%'";
            }

            if (!empty($_GPC['category_id'])) {
                $cid = intval($_GPC['category_id']);
                $condition .= " AND pcate = '{$cid}'";
            }

            if (isset($_GPC['status'])) {
                $condition .= " AND status = '" . intval($_GPC['status']) . "'";
            }

            $list = pdo_fetchall("SELECT * FROM " . tablename($this->table_goods) . " WHERE weid = '{$_W['uniacid']}' AND storeid ={$storeid} $condition ORDER BY status DESC, displayorder DESC, id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);

            $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->table_goods) . " WHERE weid = '{$_W['uniacid']}' AND storeid ={$storeid} $condition");

            $pager = pagination($total, $pindex, $psize);
        } elseif ($operation == 'delete') {
            $id = intval($_GPC['id']);
            $row = pdo_fetch("SELECT id, thumb FROM " . tablename($this->table_goods) . " WHERE id = :id", array(':id' => $id));
            if (empty($row)) {
                message('抱歉，商品 不存在或是已经被删除！');
            }

            pdo_delete($this->table_goods, array('id' => $id, 'weid' => $weid));
            message('删除成功！', referer(), 'success');
        } elseif ($operation == 'deleteall') {
            $rowcount = 0;
            $notrowcount = 0;
            foreach ($_GPC['idArr'] as $k => $id) {
                $id = intval($id);
                if (!empty($id)) {
                    $goods = pdo_fetch("SELECT * FROM " . tablename($this->table_goods) . " WHERE id = :id", array(':id' => $id));
                    if (empty($goods)) {
                        $notrowcount++;
                        continue;
                    }
                    pdo_delete($this->table_goods, array('id' => $id, 'weid' => $weid));
                    $rowcount++;
                }
            }
            $this->message("操作成功！共删除{$rowcount}条数据,{$notrowcount}条数据不能删除!", '', 0);
        }
        include $this->template('goods');
    }

    //智能选菜
    public function doWebIntelligent()
    {
        global $_W, $_GPC;
//        $GLOBALS['frames'] = $this->getNaveMenu();
        $weid = $this->_weid;
        $action = 'intelligent';
        $title = $this->actions_titles[$action];
        $storeid = intval($_GPC['storeid']);
        $returnid = $this->checkPermission($storeid);
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';

        if ($operation == 'display') {
            if (!empty($_GPC['displayorder'])) {
                foreach ($_GPC['displayorder'] as $id => $displayorder) {
                    pdo_update($this->table_intelligent, array('displayorder' => $displayorder), array('id' => $id));
                }
                message('分类排序更新成功！', $this->createWebUrl('intelligent', array('op' => 'display', 'storeid' => $storeid)), 'success');
            }
            $children = array();
            $intelligents = pdo_fetchall("SELECT * FROM " . tablename($this->table_intelligent) . " WHERE weid = '{$weid}'  AND storeid ={$storeid} ORDER BY displayorder DESC");

            $goods = pdo_fetchall("SELECT * FROM " . tablename($this->table_goods) . " WHERE weid = '{$weid}'  AND storeid ={$storeid} ORDER BY displayorder DESC");
            $goods_arr = array();
            foreach ($goods as $key => $value) {
                $goods_arr[$value['id']] = $value['title'];
            }
            include $this->template('intelligent');
        } elseif ($operation == 'post') {
            $id = intval($_GPC['id']);
            if (!empty($id)) {
                $intelligent = pdo_fetch("SELECT * FROM " . tablename($this->table_intelligent) . " WHERE id = '$id'");
                if (!empty($intelligent)) {
                    $goodsids = explode(',', $intelligent['content']);
                }
            } else {
                $intelligent = array(
                    'displayorder' => 0,
                );
            }

            $categorys = pdo_fetchall("SELECT * FROM " . tablename($this->table_category) . " WHERE weid = '{$weid}'  AND storeid ={$storeid} ORDER BY displayorder DESC");
            $goods = pdo_fetchall("SELECT * FROM " . tablename($this->table_goods) . " WHERE weid = '{$weid}'  AND storeid ={$storeid} ORDER BY displayorder DESC");
            $goods_arr = array();
            foreach ($goods as $key => $value) {
                foreach ($categorys as $key2 => $value2) {
                    if ($value['pcate'] == $value2['id']) {
                        $goods_arr[$value['pcate']][] = array('id' => $value['id'], 'title' => $value['title']);
                    }
                }
            }

            if (checksubmit('submit')) {
                if (empty($_GPC['catename'])) {
                    message('抱歉，请输入分类名称！');
                }

                $data = array(
                    'weid' => $weid,
                    'storeid' => $storeid,
                    'name' => intval($_GPC['catename']),
                    'content' => trim(implode(',', $_GPC['goodsids'])),
                    'displayorder' => intval($_GPC['displayorder']),
                );

                if ($data['name'] <= 0) {
                    message('人数必须大于0!');
                }

                if (empty($data['storeid'])) {
                    message('非法参数');
                }

                if (!empty($id)) {
                    pdo_update($this->table_intelligent, $data, array('id' => $id));
                } else {
                    pdo_insert($this->table_intelligent, $data);
                    $id = pdo_insertid();
                }
                message('更新分类成功！', $this->createWebUrl('intelligent', array('op' => 'display', 'storeid' => $storeid)), 'success');
            }
            include $this->template('intelligent');
        } elseif ($operation == 'delete') {
            $id = intval($_GPC['id']);
            $category = pdo_fetch("SELECT id FROM " . tablename($this->table_intelligent) . " WHERE id = '$id'");
            if (empty($category)) {
                message('抱歉，分类不存在或是已经被删除！', $this->createWebUrl('intelligent', array('op' => 'display', 'storeid' => $storeid)), 'error');
            }
            pdo_delete($this->table_intelligent, array('id' => $id, 'weid' => $weid));
            message('分类删除成功！', $this->createWebUrl('category', array('op' => 'display', 'storeid' => $storeid)), 'success');
        }
    }

    public function doWebfans()
    {
        global $_GPC, $_W;
        load()->func('tpl');
        $weid = $this->_weid;
        $action = 'fans';
        $title = $this->actions_titles[$action];
        $storeid = intval($_GPC['storeid']);
        $returnid = $this->checkPermission($storeid);

        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        if ($operation == 'display') {
            $condition = '';
            if (!empty($_GPC['keyword'])) {
                $types = trim($_GPC['types']);
                $condition .= " AND {$types} LIKE '%{$_GPC['keyword']}%'";
            }
            if (isset($_GPC['status']) && !empty($_GPC['status'])) {
                $condition .= " AND status={$_GPC['status']} ";
            }
            $pindex = max(1, intval($_GPC['page']));
            $psize = 8;

            $start = ($pindex - 1) * $psize;
            $limit = "";
            $limit .= " LIMIT {$start},{$psize}";
            $list = pdo_fetchall("SELECT * FROM " . tablename($this->table_fans) . " WHERE weid = :weid {$condition} ORDER BY id DESC " . $limit, array(':weid' => $weid), 'from_user');
            $total = pdo_fetchcolumn("SELECT count(1) FROM " . tablename($this->table_fans) . " WHERE weid = :weid {$condition} ", array(':weid' => $weid));

            $order_count = pdo_fetchall("SELECT from_user,COUNT(1) as count FROM " . tablename($this->table_order) . " WHERE storeid=:storeid  GROUP BY from_user,weid having weid = :weid", array(':weid' => $weid, ':storeid' => $storeid), 'from_user');
            $pay_price = pdo_fetchall("SELECT from_user,sum(totalprice) as totalprice FROM " . tablename($this->table_order) . " WHERE ispay=1 AND storeid=:storeid  GROUP BY from_user,weid having weid = :weid", array(':weid' => $weid, ':storeid' => $storeid), 'from_user');

            $pager = pagination($total, $pindex, $psize);
        } else if ($operation == 'post') {
            $id = intval($_GPC['id']);
            $item = pdo_fetch("SELECT * FROM " . tablename($this->table_fans) . " WHERE id = :id", array(':id' => $id));

            $order_count = pdo_fetchcolumn("SELECT COUNT(1) as count FROM " . tablename($this->table_order) . " WHERE from_user=:from_user AND weid = :weid AND storeid=:storeid ", array(':weid' => $weid, ':from_user' => $item['from_user'], ':storeid' => $storeid));
            $cancel_count = pdo_fetchcolumn("SELECT COUNT(1) as count FROM " . tablename($this->table_order) . " WHERE from_user=:from_user AND weid = :weid AND status=-1 AND storeid=:storeid ", array(':weid' => $weid, ':from_user' => $item['from_user'], ':storeid' => $storeid));
            $pay_price = pdo_fetchcolumn("SELECT sum(totalprice) as totalprice FROM " . tablename($this->table_order) . " WHERE ispay=1 AND weid = :weid AND from_user=:from_user AND storeid=:storeid ", array(':weid' => $weid, ':from_user' => $item['from_user'], ':storeid' => $storeid));

            if (checksubmit()) {
                $data = array(
                    'weid' => $weid,
                    'nickname' => trim($_GPC['nickname']),
                    'username' => trim($_GPC['username']),
                    'mobile' => trim($_GPC['mobile']),
                    'address' => trim($_GPC['address']),
                    'lat' => trim($_GPC['baidumap']['lat']),
                    'lng' => trim($_GPC['baidumap']['lng']),
                    'sex' => intval($_GPC['sex']),
                    'dateline' => TIMESTAMP
                );
                if (!empty($_GPC['headimgurl'])) {
                    $data['headimgurl'] = $_GPC['headimgurl'];
                }

                if (empty($item)) {
                    pdo_insert($this->table_fans, $data);
                } else {
                    unset($data['dateline']);
                    pdo_update($this->table_fans, $data, array('id' => $id, 'weid' => $weid));
                }
                message('操作成功！', $this->createWebUrl('fans', array('op' => 'display', 'storeid' => $storeid)), 'success');
            }
        } else if ($operation == 'delete') {
            $id = intval($_GPC['id']);
            $item = pdo_fetch("SELECT id FROM " . tablename($this->table_fans) . " WHERE id = :id AND weid=:weid", array(':id' => $id, ':weid' => $weid));
            if (empty($item)) {
                message('抱歉，不存在或是已经被删除！', $this->createWebUrl('fans', array('op' => 'display', 'storeid' => $storeid)), 'error');
            }
            pdo_delete($this->table_fans, array('id' => $id, 'weid' => $weid));
            message('删除成功！', $this->createWebUrl('fans', array('op' => 'display', 'storeid' => $storeid)), 'success');
        } else if ($operation == 'setstatus') {
            $id = intval($_GPC['id']);
            $status = intval($_GPC['status']);
            pdo_query("UPDATE " . tablename($this->table_fans) . " SET status = abs(:status - 1) WHERE id=:id", array(':status' => $status, ':id' => $id));
            message('操作成功！', $this->createWebUrl('fans', array('op' => 'display', 'storeid' => $storeid)), 'success');
        }
        include $this->template('fans');
    }

    public function doWebAllfans()
    {
        global $_GPC, $_W;
        load()->func('tpl');
        $weid = $this->_weid;
        $action = 'fans';
        $title = $this->actions_titles[$action];
        $storeid = intval($_GPC['storeid']);
        $returnid = $this->checkPermission($storeid);

        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        if ($operation == 'display') {
            $condition = '';
            if (!empty($_GPC['keyword'])) {
                $types = trim($_GPC['types']);
                $condition .= " AND {$types} LIKE '%{$_GPC['keyword']}%'";
            }
            if (isset($_GPC['status']) && !empty($_GPC['status'])) {
                $condition .= " AND status={$_GPC['status']} ";
            }
            $pindex = max(1, intval($_GPC['page']));
            $psize = 8;

            $start = ($pindex - 1) * $psize;
            $limit = "";
            $limit .= " LIMIT {$start},{$psize}";
            $list = pdo_fetchall("SELECT * FROM " . tablename($this->table_fans) . " WHERE weid = :weid {$condition} ORDER BY id DESC " . $limit, array(':weid' => $weid), 'from_user');
            $total = pdo_fetchcolumn("SELECT count(1) FROM " . tablename($this->table_fans) . " WHERE weid = :weid {$condition} ", array(':weid' => $weid));

            $order_count = pdo_fetchall("SELECT from_user,COUNT(1) as count FROM " . tablename($this->table_order) . "  GROUP BY from_user,weid having weid = :weid", array(':weid' => $_W['uniacid']), 'from_user');
            $pay_price = pdo_fetchall("SELECT from_user,sum(totalprice) as totalprice FROM " . tablename($this->table_order) . " WHERE ispay=1 GROUP BY from_user,weid having weid = :weid", array(':weid' => $_W['uniacid']), 'from_user');

            $pager = pagination($total, $pindex, $psize);
        } else if ($operation == 'post') {
            $id = intval($_GPC['id']);
            $item = pdo_fetch("SELECT * FROM " . tablename($this->table_fans) . " WHERE id = :id", array(':id' => $id));

            $order_count = pdo_fetchcolumn("SELECT COUNT(1) as count FROM " . tablename($this->table_order) . " WHERE from_user=:from_user AND weid = :weid", array(':weid' => $weid, ':from_user' => $item['from_user']));
            $cancel_count = pdo_fetchcolumn("SELECT COUNT(1) as count FROM " . tablename($this->table_order) . " WHERE from_user=:from_user AND weid = :weid AND status=-1", array(':weid' => $weid, ':from_user' => $item['from_user']));
            $pay_price = pdo_fetchcolumn("SELECT sum(totalprice) as totalprice FROM " . tablename($this->table_order) . " WHERE ispay=1 AND weid = :weid AND from_user=:from_user", array(':weid' => $weid, ':from_user' => $item['from_user']));

            if (checksubmit()) {
                $data = array(
                    'weid' => $weid,
                    'nickname' => trim($_GPC['nickname']),
                    'username' => trim($_GPC['username']),
                    'mobile' => trim($_GPC['mobile']),
                    'address' => trim($_GPC['address']),
                    'lat' => trim($_GPC['baidumap']['lat']),
                    'lng' => trim($_GPC['baidumap']['lng']),
                    'sex' => intval($_GPC['sex']),
                    'dateline' => TIMESTAMP
                );
                if (!empty($_GPC['headimgurl'])) {
                    $data['headimgurl'] = $_GPC['headimgurl'];
                }

                if (empty($item)) {
                    pdo_insert($this->table_fans, $data);
                } else {
                    unset($data['dateline']);
                    pdo_update($this->table_fans, $data, array('id' => $id, 'weid' => $weid));
                }
                message('操作成功！', $this->createWebUrl('allfans', array('op' => 'display', 'storeid' => $storeid)), 'success');
            }
        } else if ($operation == 'delete') {
            $id = intval($_GPC['id']);
            $item = pdo_fetch("SELECT id FROM " . tablename($this->table_fans) . " WHERE id = :id AND weid=:weid", array(':id' => $id, ':weid' => $weid));
            if (empty($item)) {
                message('抱歉，不存在或是已经被删除！', $this->createWebUrl('allfans', array('op' => 'display', 'storeid' => $storeid)), 'error');
            }
            pdo_delete($this->table_fans, array('id' => $id, 'weid' => $weid));
            message('删除成功！', $this->createWebUrl('allfans', array('op' => 'display', 'storeid' => $storeid)), 'success');
        } else if ($operation == 'setstatus') {
            $id = intval($_GPC['id']);
            $status = intval($_GPC['status']);
            pdo_query("UPDATE " . tablename($this->table_fans) . " SET status = abs(:status - 1) WHERE id=:id", array(':status' => $status, ':id' => $id));
            message('操作成功！', $this->createWebUrl('allfans', array('op' => 'display', 'storeid' => $storeid)), 'success');
        }
        include $this->template('allfans');
    }

    public function doWebOrder()
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
//        $GLOBALS['frames'] = $this->getNaveMenu();

        load()->func('tpl');
        $action = 'order';
        $title = $this->actions_titles[$action];
        $storeid = intval($_GPC['storeid']);
        $returnid = $this->checkPermission($storeid);

        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        if ($operation == 'display') {

            $commoncondition = " weid = '{$_W['uniacid']}' ";
            if ($storeid != 0) {
                $commoncondition .= " AND storeid={$storeid} ";
            }

            $commonconditioncount = " weid = '{$_W['uniacid']}' ";
            if ($storeid != 0) {
                $commonconditioncount .= " AND storeid={$storeid} ";
            }

            if (!empty($_GPC['time'])) {
                $starttime = strtotime($_GPC['time']['start']);
                $endtime = strtotime($_GPC['time']['end']) + 86399;
                $commoncondition .= " AND dateline >= :starttime AND dateline <= :endtime ";
                $paras[':starttime'] = $starttime;
                $paras[':endtime'] = $endtime;
            }

            if (empty($starttime) || empty($endtime)) {
                $starttime = strtotime('-1 month');
                $endtime = time();
            }

            $pindex = max(1, intval($_GPC['page']));
            $psize = 10;

            if (!empty($_GPC['ordersn'])) {
                $commoncondition .= " AND ordersn LIKE '%{$_GPC['ordersn']}%' ";
            }

            if (!empty($_GPC['tel'])) {
                $commoncondition .= " AND tel LIKE '%{$_GPC['tel']}%' ";
            }

            if (!empty($_GPC['username'])) {
                $commoncondition .= " AND username LIKE '%{$_GPC['username']}%' ";
            }

            if (isset($_GPC['status']) && $_GPC['status'] != '') {
                $commoncondition .= " AND status = '" . intval($_GPC['status']) . "'";
            }

            if (isset($_GPC['paytype']) && $_GPC['paytype'] != '') {
                $commoncondition .= " AND paytype = '" . intval($_GPC['paytype']) . "'";
            }

            if ($_GPC['out_put'] == 'output') {
                $sql = "select * from " . tablename($this->table_order)
                    . " WHERE $commoncondition ORDER BY status DESC, dateline DESC ";
                $list = pdo_fetchall($sql, $paras);
                $orderstatus = array(
                    '-1' => array('css' => 'default', 'name' => '已取消'),
                    '0' => array('css' => 'danger', 'name' => '待处理'),
                    '1' => array('css' => 'info', 'name' => '已确认'),
                    '2' => array('css' => 'warning', 'name' => '已付款'),
                    '3' => array('css' => 'success', 'name' => '已完成')
                );

                $paytypes = array(
                    '0' => array('css' => 'danger', 'name' => '未支付'),
                    '1' => array('css' => 'info', 'name' => '余额支付'),
                    '2' => array('css' => 'warning', 'name' => '在线支付'),
                    '3' => array('css' => 'success', 'name' => '现金支付')
                );

                $i = 0;
                foreach ($list as $key => $value) {
                    $arr[$i]['ordersn'] = $value['ordersn'];
                    $arr[$i]['transid'] = $value['transid'];
                    $arr[$i]['paytype'] = $paytypes[$value['paytype']]['name'];
                    $arr[$i]['status'] = $orderstatus[$value['status']]['name'];
                    $arr[$i]['totalprice'] = $value['totalprice'];
                    $arr[$i]['username'] = $value['username'];
                    $arr[$i]['tel'] = $value['tel'];
                    $arr[$i]['address'] = $value['address'];
                    $arr[$i]['dateline'] = date('Y-m-d H:i:s', $value['dateline']);
                    $i++;
                }

                $this->exportexcel($arr, array('订单号', '商户订单号', '支付方式', '状态', '总价', '真实姓名', '电话号码', '地址', '时间'), time());
                exit();
            }

            $list = pdo_fetchall("SELECT * FROM " . tablename($this->table_order) . " WHERE $commoncondition ORDER BY id desc, dateline DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $paras);

            $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->table_order) . " WHERE $commoncondition", $paras);
            $pager = pagination($total, $pindex, $psize);

            if (!empty($list)) {
                foreach ($list as $row) {
                    $userids[$row['from_user']] = $row['from_user'];
                }
            }

//            $order_count_all = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->table_order) . "  WHERE {$commonconditioncount} ");
//            $order_count_confirm = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->table_order) . "  WHERE {$commonconditioncount} AND status=1");
//            $order_count_pay = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->table_order) . "  WHERE {$commonconditioncount} AND status=2");
//            $order_count_finish = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->table_order) . "  WHERE {$commonconditioncount} AND status=3");
//            $order_count_cancel = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->table_order) . "  WHERE {$commonconditioncount} AND status=-1");

            //打印数量
            $print_order_count = pdo_fetchall("SELECT orderid,COUNT(1) as count FROM " . tablename($this->table_print_order) . "  GROUP BY orderid,weid having weid = :weid", array(':weid' => $_W['uniacid']), 'orderid');

            //门店列表
            $storelist = pdo_fetchall("SELECT * FROM " . tablename($this->table_stores) . " WHERE weid = :weid", array(':weid' => $_W['uniacid']), 'id');

        } elseif ($operation == 'detail') {
            //流程 第一步确认付款 第二步确认订单 第三步，完成订单
            $id = intval($_GPC['id']);
            $order = pdo_fetch("SELECT * FROM " . tablename($this->table_order) . " WHERE id=:id AND weid=:weid LIMIT 1", array(':id' => $id, ':weid' => $this->_weid));

            $fans = pdo_fetch("SELECT * FROM " . tablename($this->table_fans) . " WHERE from_user=:from_user AND weid=:weid LIMIT 1", array(':from_user' => $order['from_user'], ':weid' => $this->_weid));
            //改价
            if (!empty($_GPC['confirmprice'])) {
                pdo_update($this->table_order,
                    array(
                        'totalprice' => $_GPC['updateprice'],
                        'paydetail' => empty($order['paydetail']) ? date('Y-m-d H:i', TIMESTAMP) . '改价为' . $_GPC['updateprice'] : $order['paydetail'] . '<br/>' . date('Y-m-d H:i', TIMESTAMP) . '改价为' . $_GPC['updateprice']
                    ),
                    array('id' => $id)
                );
                message('改价成功！', referer(), 'success');
            }

            if (checksubmit('confrimsign')) {
                pdo_update($this->table_order, array('reply' => $_GPC['reply']), array('id' => $id));
                message('操作成功！', referer(), 'success');
            }

            $store = pdo_fetch("SELECT * FROM " . tablename($this->table_stores) . " WHERE id=:id AND weid=:weid LIMIT 1", array(':id' => $order['storeid'], ':weid' => $weid));
            $setting = pdo_fetch("SELECT * FROM " . tablename($this->table_setting) . " where weid = :weid LIMIT 1", array(':weid' => $weid));

            if (!empty($_GPC['finish'])) {
                //isfinish
                if ($order['isfinish'] == 0) {
                    //计算积分
                    $this->setOrderCredit($order['id']);
                    pdo_update($this->table_order, array('isfinish' => 1), array('id' => $id));
                    if ($order['dining_mode'] == 1) {
                        pdo_update($this->table_tables, array('status' => 0), array('id' => $order['tables']));
                    }
                }
                pdo_update($this->table_order, array('status' => 3), array('id' => $id, 'weid' => $weid));
                $order = pdo_fetch("SELECT * FROM " . tablename($this->table_order) . " WHERE id=:id AND weid=:weid LIMIT 1", array(':id' => $id, ':weid' => $this->_weid));
                $this->sendOrderNotice($order, $store, $setting);
                message('订单操作成功！', referer(), 'success');
            }
            if (!empty($_GPC['cancel'])) {
                pdo_update($this->table_order, array('status' => 1), array('id' => $id, 'weid' => $weid));
                message('取消完成订单操作成功！', referer(), 'success');
            }
            if (!empty($_GPC['confirm'])) {
                pdo_update($this->table_order, array('status' => 1), array('id' => $id, 'weid' => $weid));

                $order = pdo_fetch("SELECT * FROM " . tablename($this->table_order) . " WHERE id=:id AND weid=:weid LIMIT 1", array(':id' => $id, ':weid' => $this->_weid));
                $this->sendOrderNotice($order, $store, $setting);
                message('确认订单操作成功！', referer(), 'success');
            }
            if (!empty($_GPC['cancelpay'])) {
                pdo_update($this->table_order, array('status' => 0), array('id' => $id, 'weid' => $weid));
                message('取消订单付款操作成功！', referer(), 'success');
            }
            if (!empty($_GPC['confrimpay'])) {
                pdo_update($this->table_order, array('ispay' => 1), array('id' => $id, 'weid' => $weid));

                $order = pdo_fetch("SELECT * FROM " . tablename($this->table_order) . " WHERE id=:id AND weid=:weid LIMIT 1", array(':id' => $id, ':weid' => $this->_weid));
                $this->sendOrderNotice($order, $store, $setting);
                message('确认订单付款操作成功！', referer(), 'success');
            }
            if (!empty($_GPC['close'])) {
                pdo_update($this->table_order, array('status' => -1), array('id' => $id, 'weid' => $weid));

                $order = pdo_fetch("SELECT * FROM " . tablename($this->table_order) . " WHERE id=:id AND weid=:weid LIMIT 1", array(':id' => $id, ':weid' => $this->_weid));
                $this->sendOrderNotice($order, $store, $setting);
                message('订单关闭操作成功！', referer(), 'success');
            }
            if (!empty($_GPC['open'])) {
                pdo_update($this->table_order, array('status' => 0), array('id' => $id, 'weid' => $weid));
                message('开启订单操作成功！', referer(), 'success');
            }

            $item = pdo_fetch("SELECT * FROM " . tablename($this->table_order) . " WHERE id = :id", array(':id' => $id));

            $goods = pdo_fetchall("SELECT a.goodsid,a.price, b.credit, a.total,b.thumb,b.title,b.id FROM " . tablename($this->table_order_goods) . " a INNER JOIN " . tablename($this->table_goods) . " b ON a.goodsid=b.id WHERE a.orderid = :id", array(':id' => $id));
            if ($item['dining_mode'] == 1) {
                $tablesid = intval($item['tables']);
                $table = pdo_fetch("SELECT * FROM " . tablename($this->table_tables) . " where weid = :weid AND id=:id LIMIT 1", array(':weid' => $weid, ':id' => $tablesid));
                if (empty($table)) {
//                    exit('餐桌不存在！');
                } else {
                    $tablezones = pdo_fetch("SELECT * FROM " . tablename($this->table_tablezones) . " where weid = :weid AND id=:id LIMIT 1", array(':weid' => $weid, ':id' => $table['tablezonesid']));
                    if (empty($tablezones)) {
//                        exit('餐桌类型不存在！');
                    }
                    $table_title = $tablezones['title'] . '-' . $table['title'];
                }
            }
            if ($item['dining_mode'] == 3) {
                $tablezones = pdo_fetch("SELECT * FROM " . tablename($this->table_tablezones) . " where weid = :weid AND id=:id LIMIT 1", array(':weid' => $weid, ':id' => $item['tablezonesid']));
            }
        } else if ($operation == 'delete') {
            $id = $_GPC['id'];
            pdo_delete($this->table_order, array('id' => $id, 'weid' => $weid));
            pdo_delete($this->table_order_goods, array('orderid' => $id, 'weid' => $weid));
            message('删除成功！', $this->createWebUrl('order', array('op' => 'display', 'storeid' => $storeid)), 'success');
        } else if ($operation == 'print') {
            $id = $_GPC['id'];//订单id
            $flag = false;

            $prints = pdo_fetchall("SELECT * FROM " . tablename($this->table_print_setting) . " WHERE weid = :weid AND storeid=:storeid", array(':weid' => $_W['uniacid'], ':storeid' => $storeid));

            if (empty($prints)) {
                message('请先添加打印机或者开启打印机！');
            }

            foreach ($prints as $key => $value) {
                if ($value['print_status'] == 1 && $value['type'] == 'hongxin') {
                    $data = array(
                        'weid' => $_W['uniacid'],
                        'orderid' => $id,
                        'print_usr' => $value['print_usr'],
                        'print_status' => -1,
                        'dateline' => TIMESTAMP
                    );
                    pdo_insert('weisrc_dish_print_order', $data);
                }
            }
            $this->feiyinSendFreeMessage($id);
            $this->_365SendFreeMessage($id);
            message('操作成功！', $this->createWebUrl('order', array('op' => 'display', 'storeid' => $storeid)), 'success');
        }
        include $this->template('order');
    }

    public function doWebAllOrder()
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
//        $GLOBALS['frames'] = $this->getNaveMenu();

        load()->func('tpl');
        $action = 'order';
        $title = $this->actions_titles[$action];
        $storeid = intval($_GPC['storeid']);
        $returnid = $this->checkPermission($storeid);

        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        if ($operation == 'display') {

            $commoncondition = " weid = '{$_W['uniacid']}' ";
            if ($storeid != 0) {
                $commoncondition .= " AND storeid={$storeid} ";
            }

            $commonconditioncount = " weid = '{$_W['uniacid']}' ";
            if ($storeid != 0) {
                $commonconditioncount .= " AND storeid={$storeid} ";
            }

            if (!empty($_GPC['time'])) {
                $starttime = strtotime($_GPC['time']['start']);
                $endtime = strtotime($_GPC['time']['end']) + 86399;
                $commoncondition .= " AND dateline >= :starttime AND dateline <= :endtime ";
                $paras[':starttime'] = $starttime;
                $paras[':endtime'] = $endtime;
            }

            if (empty($starttime) || empty($endtime)) {
                $starttime = strtotime('-1 month');
                $endtime = time();
            }

            $pindex = max(1, intval($_GPC['page']));
            $psize = 10;

            if (!empty($_GPC['ordersn'])) {
                $commoncondition .= " AND ordersn LIKE '%{$_GPC['ordersn']}%' ";
            }

            if (!empty($_GPC['tel'])) {
                $commoncondition .= " AND tel LIKE '%{$_GPC['tel']}%' ";
            }

            if (!empty($_GPC['username'])) {
                $commoncondition .= " AND username LIKE '%{$_GPC['username']}%' ";
            }

            if (isset($_GPC['status']) && $_GPC['status'] != '') {
                $commoncondition .= " AND status = '" . intval($_GPC['status']) . "'";
            }

            if (isset($_GPC['paytype']) && $_GPC['paytype'] != '') {
                $commoncondition .= " AND paytype = '" . intval($_GPC['paytype']) . "'";
            }

            if ($_GPC['out_put'] == 'output') {
                $sql = "select * from " . tablename($this->table_order)
                    . " WHERE $commoncondition ORDER BY status DESC, dateline DESC ";
                $list = pdo_fetchall($sql, $paras);
                $orderstatus = array(
                    '-1' => array('css' => 'default', 'name' => '已取消'),
                    '0' => array('css' => 'danger', 'name' => '待处理'),
                    '1' => array('css' => 'info', 'name' => '已确认'),
                    '2' => array('css' => 'warning', 'name' => '已付款'),
                    '3' => array('css' => 'success', 'name' => '已完成')
                );

                $paytypes = array(
                    '0' => array('css' => 'danger', 'name' => '未支付'),
                    '1' => array('css' => 'info', 'name' => '余额支付'),
                    '2' => array('css' => 'warning', 'name' => '在线支付'),
                    '3' => array('css' => 'success', 'name' => '现金支付')
                );

                $i = 0;
                foreach ($list as $key => $value) {
                    $arr[$i]['ordersn'] = $value['ordersn'];
                    $arr[$i]['transid'] = $value['transid'];
                    $arr[$i]['paytype'] = $paytypes[$value['paytype']]['name'];
                    $arr[$i]['status'] = $orderstatus[$value['status']]['name'];
                    $arr[$i]['totalprice'] = $value['totalprice'];
                    $arr[$i]['username'] = $value['username'];
                    $arr[$i]['tel'] = $value['tel'];
                    $arr[$i]['address'] = $value['address'];
                    $arr[$i]['dateline'] = date('Y-m-d H:i:s', $value['dateline']);
                    $i++;
                }

                $this->exportexcel($arr, array('订单号', '商户订单号', '支付方式', '状态', '总价', '真实姓名', '电话号码', '地址', '时间'), time());
                exit();
            }

            $list = pdo_fetchall("SELECT * FROM " . tablename($this->table_order) . " WHERE $commoncondition ORDER BY id desc, dateline DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $paras);

            $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->table_order) . " WHERE $commoncondition", $paras);
            $pager = pagination($total, $pindex, $psize);

            if (!empty($list)) {
                foreach ($list as $row) {
                    $userids[$row['from_user']] = $row['from_user'];
                }
            }

            //打印数量
            $print_order_count = pdo_fetchall("SELECT orderid,COUNT(1) as count FROM " . tablename($this->table_print_order) . "  GROUP BY orderid,weid having weid = :weid", array(':weid' => $_W['uniacid']), 'orderid');

            //门店列表
            $storelist = pdo_fetchall("SELECT * FROM " . tablename($this->table_stores) . " WHERE weid = :weid", array(':weid' => $_W['uniacid']), 'id');

        } elseif ($operation == 'detail') {
            //流程 第一步确认付款 第二步确认订单 第三步，完成订单
            $id = intval($_GPC['id']);
            $order = pdo_fetch("SELECT * FROM " . tablename($this->table_order) . " WHERE id=:id AND weid=:weid LIMIT 1", array(':id' => $id, ':weid' => $this->_weid));

            $fans = pdo_fetch("SELECT * FROM " . tablename($this->table_fans) . " WHERE from_user=:from_user AND weid=:weid LIMIT 1", array(':from_user' => $order['from_user'], ':weid' => $this->_weid));

            if (!empty($_GPC['confirmprice'])) {
                pdo_update($this->table_order,
                    array(
                        'totalprice' => $_GPC['updateprice'],
                        'paydetail' => empty($order['paydetail']) ? date('Y-m-d H:i', TIMESTAMP) . '改价为' . $_GPC['updateprice'] : $order['paydetail'] . '<br/>' . date('Y-m-d H:i', TIMESTAMP) . '改价为' . $_GPC['updateprice']
                    ),
                    array('id' => $id)
                );
                message('改价成功！', referer(), 'success');
            }

            if (checksubmit('confrimsign')) {
                pdo_update($this->table_order, array('reply' => $_GPC['reply']), array('id' => $id));
                message('操作成功！', referer(), 'success');
            }

            $store = pdo_fetch("SELECT * FROM " . tablename($this->table_stores) . " WHERE id=:id AND weid=:weid LIMIT 1", array(':id' => $order['storeid'], ':weid' => $weid));
            $setting = pdo_fetch("SELECT * FROM " . tablename($this->table_setting) . " where weid = :weid LIMIT 1", array(':weid' => $weid));
            if (!empty($_GPC['finish'])) {
                //isfinish
                if ($order['isfinish'] == 0) {
                    //计算积分
                    $this->setOrderCredit($order['id']);
                    pdo_update($this->table_order, array('isfinish' => 1), array('id' => $id));
                    if ($order['dining_mode'] == 1) {
                        pdo_update($this->table_tables, array('status' => 0), array('id' => $order['tables']));
                    }
                }
                pdo_update($this->table_order, array('status' => 3), array('id' => $id, 'weid' => $weid));
                $order = pdo_fetch("SELECT * FROM " . tablename($this->table_order) . " WHERE id=:id AND weid=:weid LIMIT 1", array(':id' => $id, ':weid' => $this->_weid));
                $this->sendOrderNotice($order, $store, $setting);
                message('订单操作成功！', referer(), 'success');
            }
            if (!empty($_GPC['cancel'])) {
                pdo_update($this->table_order, array('status' => 1), array('id' => $id, 'weid' => $weid));
                message('取消完成订单操作成功！', referer(), 'success');
            }
            if (!empty($_GPC['confirm'])) {
                pdo_update($this->table_order, array('status' => 1), array('id' => $id, 'weid' => $weid));

                $order = pdo_fetch("SELECT * FROM " . tablename($this->table_order) . " WHERE id=:id AND weid=:weid LIMIT 1", array(':id' => $id, ':weid' => $this->_weid));
                $this->sendOrderNotice($order, $store, $setting);
                message('确认订单操作成功！', referer(), 'success');
            }
            if (!empty($_GPC['cancelpay'])) {
                pdo_update($this->table_order, array('status' => 0), array('id' => $id, 'weid' => $weid));
                message('取消订单付款操作成功！', referer(), 'success');
            }
            if (!empty($_GPC['confrimpay'])) {
                pdo_update($this->table_order, array('ispay' => 1), array('id' => $id, 'weid' => $weid));

                $order = pdo_fetch("SELECT * FROM " . tablename($this->table_order) . " WHERE id=:id AND weid=:weid LIMIT 1", array(':id' => $id, ':weid' => $this->_weid));
                $this->sendOrderNotice($order, $store, $setting);
                message('确认订单付款操作成功！', referer(), 'success');
            }
            if (!empty($_GPC['close'])) {
                pdo_update($this->table_order, array('status' => -1), array('id' => $id, 'weid' => $weid));

                $order = pdo_fetch("SELECT * FROM " . tablename($this->table_order) . " WHERE id=:id AND weid=:weid LIMIT 1", array(':id' => $id, ':weid' => $this->_weid));
                $this->sendOrderNotice($order, $store, $setting);
                message('订单关闭操作成功！', referer(), 'success');
            }
            if (!empty($_GPC['open'])) {
                pdo_update($this->table_order, array('status' => 0), array('id' => $id, 'weid' => $weid));
                message('开启订单操作成功！', referer(), 'success');
            }

            $item = pdo_fetch("SELECT * FROM " . tablename($this->table_order) . " WHERE id = :id", array(':id' => $id));

            $goods = pdo_fetchall("SELECT a.goodsid,a.price, a.total,b.thumb,b.title,b.id,b.credit FROM " . tablename($this->table_order_goods) . " a INNER JOIN " . tablename($this->table_goods) . " b ON a.goodsid=b.id WHERE a.orderid = :id", array(':id' => $id));
            if ($item['dining_mode'] == 1) {
                $tablesid = intval($item['tables']);
                $table = pdo_fetch("SELECT * FROM " . tablename($this->table_tables) . " where weid = :weid AND id=:id LIMIT 1", array(':weid' => $weid, ':id' => $tablesid));
                if (empty($table)) {
//                    exit('餐桌不存在！');
                } else {
                    $tablezones = pdo_fetch("SELECT * FROM " . tablename($this->table_tablezones) . " where weid = :weid AND id=:id LIMIT 1", array(':weid' => $weid, ':id' => $table['tablezonesid']));
                    if (empty($tablezones)) {
//                        exit('餐桌类型不存在！');
                    }
                    $table_title = $tablezones['title'] . '-' . $table['title'];
                }
            }
            if ($item['dining_mode'] == 3) {
                $tablezones = pdo_fetch("SELECT * FROM " . tablename($this->table_tablezones) . " where weid = :weid AND id=:id LIMIT 1", array(':weid' => $weid, ':id' => $item['tablezonesid']));
            }
        } else if ($operation == 'delete') {
            $id = $_GPC['id'];
            pdo_delete($this->table_order, array('id' => $id, 'weid' => $weid));
            pdo_delete($this->table_order_goods, array('orderid' => $id, 'weid' => $weid));
            message('删除成功！', $this->createWebUrl('order', array('op' => 'display', 'storeid' => $storeid)), 'success');
        } else if ($operation == 'print') {
            $id = $_GPC['id'];//订单id
            $order = pdo_fetch("SELECT * FROM " . tablename($this->table_order) . " WHERE id = :id", array(':id' => $id));
            $flag = false;

            $prints = pdo_fetchall("SELECT * FROM " . tablename($this->table_print_setting) . " WHERE weid = :weid AND storeid=:storeid", array(':weid' => $_W['uniacid'], ':storeid' => $order['storeid']));

            if (empty($prints)) {
                message('请先添加打印机或者开启打印机！');
            }

            foreach ($prints as $key => $value) {
                if ($value['print_status'] == 1 && $value['type'] == 'hongxin') {
                    $data = array(
                        'weid' => $_W['uniacid'],
                        'orderid' => $id,
                        'print_usr' => $value['print_usr'],
                        'print_status' => -1,
                        'dateline' => TIMESTAMP
                    );
                    pdo_insert('weisrc_dish_print_order', $data);
                }
            }
            $this->feiyinSendFreeMessage($id);
            message('操作成功！', $this->createWebUrl('order', array('op' => 'display', 'storeid' => $order['storeid'])), 'success');
        }
        include $this->template('allorder');
    }

    protected function exportexcel($data = array(), $title = array(), $filename = 'report')
    {
        header("Content-type:application/octet-stream");
        header("Accept-Ranges:bytes");
        header("Content-type:application/vnd.ms-excel");
        header("Content-Disposition:attachment;filename=" . $filename . ".xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        //导出xls 开始
        if (!empty($title)) {
            foreach ($title as $k => $v) {
                $title[$k] = iconv("UTF-8", "GB2312", $v);
            }
            $title = implode("\t", $title);
            echo "$title\n";
        }
        if (!empty($data)) {
            foreach ($data as $key => $val) {
                foreach ($val as $ck => $cv) {
                    $data[$key][$ck] = iconv("UTF-8", "GB2312", $cv);
                }
                $data[$key] = implode("\t", $data[$key]);

            }
            echo implode("\n", $data);
        }
    }

    //设置订单积分
    public function setOrderCredit($orderid, $add = true)
    {
        $order = pdo_fetch("SELECT * FROM " . tablename($this->table_order) . " WHERE id=:id LIMIT 1", array(':id' => $orderid));
        if (empty($order)) {
            return false;
        }

        $ordergoods = pdo_fetchall("SELECT goodsid, total FROM " . tablename($this->table_order_goods) . " WHERE orderid = :orderid", array(':orderid' => $orderid));
        if (!empty($ordergoods)) {
            $credit = 0.00;
            $sql = 'SELECT `credit` FROM ' . tablename($this->table_goods) . ' WHERE `id` = :id';
            foreach ($ordergoods as $goods) {
                $goodsCredit = pdo_fetchcolumn($sql, array(':id' => $goods['goodsid']));
                $credit += $goodsCredit * floatval($goods['total']);
            }
        }

        //增加积分
        if (!empty($credit)) {
            load()->model('mc');
            load()->func('compat.biz');
            $uid = mc_openid2uid($order['from_user']);
            $fans = fans_search($uid, array("credit1"));
            if (!empty($fans)) {
                $uid = intval($fans['uid']);
                $remark = $add == true ? '域顺微点餐积分奖励 订单ID:' . $orderid : '域顺微点餐积分扣除 订单ID:' . $orderid;
                $log = array();
                $log[0] = $uid;
                $log[1] = $remark;
                mc_credit_update($uid, 'credit1', $credit, $log);
            }
        }
        pdo_update($this->table_order, array('credit' => $credit), array('id' => $orderid));
        return true;
    }

    /*
    ** 设置切换导航
    */
    public function set_tabbar($action, $storeid)
    {
        $actions_titles = $this->actions_titles;
        $html = '<ul class="nav nav-tabs">';
        foreach ($actions_titles as $key => $value) {
            $url = $this->createWebUrl($key, array('op' => 'display', 'storeid' => $storeid));
            $html .= '<li class="' . ($key == $action ? 'active' : '') . '"><a href="' . $url . '">' . $value . '</a></li>';
        }
        $html .= '</ul>';
        return $html;
    }

    //入口设置
    public function doWebSetRule()
    {
        global $_W;
        $rule = pdo_fetch("SELECT id FROM " . tablename('rule') . " WHERE module = 'weisrc_dish' AND weid = '{$_W['uniacid']}' order by id desc");
        if (empty($rule)) {
            header('Location: ' . $_W['siteroot'] . create_url('rule/post', array('module' => 'weisrc_dish', 'name' => '域顺微点餐')));
            exit;
        } else {
            header('Location: ' . $_W['siteroot'] . create_url('rule/post', array('module' => 'weisrc_dish', 'id' => $rule['id'])));
            exit;
        }
    }

    function uploadFile($file, $filetempname, $array)
    {
        //自己设置的上传文件存放路径
        $filePath = '../addons/weisrc_dish/upload/';

        //require_once '../addons/weisrc_dish/plugin/phpexcelreader/reader.php';
        include 'plugin/phpexcelreader/reader.php';

        $data = new Spreadsheet_Excel_Reader();
        $data->setOutputEncoding('utf-8');

        //$filepath = './source/modules/iteamlotteryv2/data_' . $weid . '.xls';
        //$tmp = $_FILES['fileexcel']['tmp_name'];

        //注意设置时区
        $time = date("y-m-d-H-i-s"); //去当前上传的时间
        $extend = strrchr($file, '.');
        //上传后的文件名
        $name = $time . $extend;
        $uploadfile = $filePath . $name; //上传后的文件名地址

        //$filetype = $_FILES['fileexcel']['type'];

        if (copy($filetempname, $uploadfile)) {
            if (!file_exists($filePath)) {
                echo '文件路径不存在.';
                return;
            }
            if (!is_readable($uploadfile)) {
                echo("文件为只读,请修改文件相关权限.");
                return;
            }
            $data->read($uploadfile);
            error_reporting(E_ALL ^ E_NOTICE);
            $count = 0;
            for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) { //$=2 第二行开始
                //以下注释的for循环打印excel表数据
                for ($j = 1; $j <= $data->sheets[0]['numCols']; $j++) {
                    //echo "\"".$data->sheets[0]['cells'][$i][$j]."\",";
                }

                $row = $data->sheets[0]['cells'][$i];
                //message($data->sheets[0]['cells'][$i][1]);

                if ($array['ac'] == "category") {
                    $count = $count + $this->upload_category($row, TIMESTAMP, $array);
                } else if ($array['ac'] == "goods") {
                    $count = $count + $this->upload_goods($row, TIMESTAMP, $array);
                } else if ($array['ac'] == "store") {
                    $count = $count + $this->upload_store($row, TIMESTAMP, $array);
                }
            }
        }
        if ($count == 0) {
            $msg = "导入失败！";
        } else {
            $msg = 1;
        }

        return $msg;
    }

    private function checkUploadFileMIME($file)
    {
        // 1.through the file extension judgement 03 or 07
        $flag = 0;
        $file_array = explode(".", $file ["name"]);
        $file_extension = strtolower(array_pop($file_array));

        // 2.through the binary content to detect the file
        switch ($file_extension) {
            case "xls" :
                // 2003 excel
                $fh = fopen($file ["tmp_name"], "rb");
                $bin = fread($fh, 8);
                fclose($fh);
                $strinfo = @unpack("C8chars", $bin);
                $typecode = "";
                foreach ($strinfo as $num) {
                    $typecode .= dechex($num);
                }
                if ($typecode == "d0cf11e0a1b11ae1") {
                    $flag = 1;
                }
                break;
            case "xlsx" :
                // 2007 excel
                $fh = fopen($file ["tmp_name"], "rb");
                $bin = fread($fh, 4);
                fclose($fh);
                $strinfo = @unpack("C4chars", $bin);
                $typecode = "";
                foreach ($strinfo as $num) {
                    $typecode .= dechex($num);
                }
                echo $typecode . 'test';
                if ($typecode == "504b34") {
                    $flag = 1;
                }
                break;
        }

        // 3.return the flag
        return $flag;
    }

    function upload_goods($strs, $time, $array)
    {
        global $_W;
        $insert = array();

        if (empty($strs[1])) {
            return 0;
        }

        //类别处理
        $category = pdo_fetch("SELECT id FROM " . tablename('weisrc_dish_category') . " WHERE name=:name AND weid=:weid AND storeid=:storeid", array(':name' => $strs[2], ':weid' => $_W['uniacid'], ':storeid' => $array['storeid']));
        $insert['pcate'] = empty($category) ? 0 : intval($category['id']);
        $insert['title'] = $strs[1];
        $insert['thumb'] = $strs[3];
        $insert['unitname'] = $strs[4];
        $insert['description'] = $strs[5];
        $insert['taste'] = $strs[6];
        $insert['isspecial'] = $strs[7];
        $insert['marketprice'] = $strs[8];
        $insert['productprice'] = $strs[9];
        $insert['subcount'] = $strs[10];
        $insert['credit'] = $strs[11];
        $insert['dateline'] = TIMESTAMP;
        $insert['status'] = 1;
        $insert['recommend'] = 0;
        $insert['ccate'] = 0;
        $insert['storeid'] = $array['storeid'];
        $insert['weid'] = $_W['uniacid'];

        $goods = pdo_fetch("SELECT * FROM " . tablename('weisrc_dish_goods') . " WHERE title=:title AND weid=:weid AND storeid=:storeid", array(':title' => $strs[1], ':weid' => $_W['uniacid'], ':storeid' => $array['storeid']));

        if (empty($goods)) {
            return pdo_insert('weisrc_dish_goods', $insert);
        } else {
            return 0;
        }
    }

    function upload_category($strs, $time, $array)
    {
        global $_W;

        if (empty($strs[1])) {
            return 0;
        }

        $insert = array();
        $insert['name'] = $strs[1];
        $insert['parentid'] = 0;
        $insert['displayorder'] = 0;
        $insert['enabled'] = 1;
        $insert['storeid'] = $array['storeid'];
        $insert['weid'] = $_W['uniacid'];

        $category = pdo_fetch("SELECT * FROM " . tablename('weisrc_dish_category') . " WHERE name=:name AND weid=:weid AND storeid=:storeid", array(':name' => $strs[1], ':weid' => $_W['uniacid'], ':storeid' => $array['storeid']));

        if (empty($category)) {
            return pdo_insert('weisrc_dish_category', $insert);
        } else {
            return 0;
        }
    }

    function upload_store($strs, $time, $array)
    {
        global $_W;

        if (empty($strs[1])) {
            return 0;
        }

        $insert = array();
        $insert['weid'] = $_W['uniacid'];
        $insert['title'] = $strs[1];
        $insert['info'] = $strs[2];
        $insert['logo'] = $strs[3];
        $insert['content'] = $strs[4];
        $insert['tel'] = $strs[5];
        $insert['address'] = $strs[6];
        $insert['place'] = $strs[6];
        $insert['hours'] = $strs[7];
        $insert['location_p'] = $strs[8];
        $insert['location_c'] = $strs[9];
        $insert['location_a'] = $strs[10];
        $insert['password'] = '';
        $insert['recharging_password'] = '';
        $insert['is_show'] = 1;
        $insert['areaid'] = 0;
        $insert['lng'] = '0.000000000';
        $insert['lat'] = '0.000000000';
        $insert['enable_wifi'] = 1;
        $insert['enable_card'] = 1;
        $insert['enable_room'] = 1;
        $insert['enable_park'] = 1;
        $insert['updatetime'] = TIMESTAMP;
        $insert['dateline'] = TIMESTAMP;

        $store = pdo_fetch("SELECT * FROM " . tablename('weisrc_dish_stores') . " WHERE title=:title AND weid=:weid LIMIT 1", array(':title' => $strs[1], ':weid' => $_W['uniacid']));

        if (empty($store)) {
            return pdo_insert('weisrc_dish_stores', $insert);
        } else {
            return 0;
        }
    }

    public function doWebUploadExcel()
    {
        global $_GPC, $_W;

        if ($_GPC['leadExcel'] == "true") {
            $filename = $_FILES['inputExcel']['name'];
            $tmp_name = $_FILES['inputExcel']['tmp_name'];

            $flag = $this->checkUploadFileMIME($_FILES['inputExcel']);
            if ($flag == 0) {
                message('文件格式不对.');
            }

            if (empty($tmp_name)) {
                message('请选择要导入的Excel文件！');
            }

            $msg = $this->uploadFile($filename, $tmp_name, $_GPC);

            if ($msg == 1) {
                message('导入成功！', referer(), 'success');
            } else {
                message($msg, '', 'error');
            }
        }
    }

    public function message($error, $url = '', $errno = -1)
    {
        $data = array();
        $data['errno'] = $errno;
        if (!empty($url)) {
            $data['url'] = $url;
        }
        $data['error'] = $error;
        echo json_encode($data);
        exit;
    }

    public function checkStoreHour($begintime, $endtime)
    {
        global $_W, $_GPC;
        $nowtime = intval(date("Hi"));
        $begintime = intval(str_replace(':', '', $begintime));
        $endtime = intval(str_replace(':', '', $endtime));
        if ($endtime > $begintime) {
            if ($nowtime >= $begintime && $nowtime <= $endtime) {
                return 1;
            }
        } else { //
            if (($nowtime >= $begintime && $nowtime <= 2400) || ($nowtime <= $endtime && $nowtime >= 0)) {
                return 1;
            }
        }
        return 0;
    }

    //----------------------以下是接口定义实现，第三方应用可根据具体情况直接修改----------------------------
    function sendFreeMessage($msg)
    {
        $msg['reqTime'] = number_format(1000 * time(), 0, '', '');
        $content = $msg['memberCode'] . $msg['msgDetail'] . $msg['deviceNo'] . $msg['msgNo'] . $msg['reqTime'] . $this->feyin_key;
        $msg['securityCode'] = md5($content);
        $msg['mode'] = 2;

        return $this->sendMessage($msg);
    }

    function sendFormatedMessage($msgInfo)
    {
        $msgInfo['reqTime'] = number_format(1000 * time(), 0, '', '');
        $content = $msgInfo['memberCode'] . $msgInfo['customerName'] . $msgInfo['customerPhone'] . $msgInfo['customerAddress'] . $msgInfo['customerMemo'] . $msgInfo['msgDetail'] . $msgInfo['deviceNo'] . $msgInfo['msgNo'] . $msgInfo['reqTime'] . $this->feyin_key;

        $msgInfo['securityCode'] = md5($content);
        $msgInfo['mode'] = 1;

        return $this->sendMessage($msgInfo);
    }


    function sendMessage($msgInfo)
    {
        $client = new HttpClient(FEYIN_HOST, FEYIN_PORT);
        if (!$client->post('/api/sendMsg', $msgInfo)) { //提交失败
            return 'faild';
        } else {
            return $client->getContent();
        }
    }

    function queryState($msgNo)
    {
        $now = number_format(1000 * time(), 0, '', '');
        $client = new HttpClient(FEYIN_HOST, FEYIN_PORT);
        if (!$client->get('/api/queryState?memberCode=' . $this->member_code . '&reqTime=' . $now . '&securityCode=' . md5($this->member_code . $now . $this->feyin_key . $msgNo) . '&msgNo=' . $msgNo)) { //请求失败
            return 'faild';
        } else {
            return $client->getContent();
        }
    }

    function listDevice()
    {
        $now = number_format(1000 * time(), 0, '', '');
        $client = new HttpClient(FEYIN_HOST, FEYIN_PORT);
        if (!$client->get('/api/listDevice?memberCode=' . $this->member_code . '&reqTime=' . $now . '&securityCode=' . md5($this->member_code . $now . $this->feyin_key))) { //请求失败
            return 'faild';
        } else {
            /***************************************************
             * 解释返回的设备状态
             * 格式：
             * <device id="4600006007272080">
             * <address>广东**</address>
             * <since>2010-09-29</since>
             * <simCode>135600*****</simCode>
             * <lastConnected>2011-03-09  19:39:03</lastConnected>
             * <deviceStatus>离线 </deviceStatus>
             * <paperStatus></paperStatus>
             * </device>
             **************************************************/

            $xml = $client->getContent();
            $sxe = new SimpleXMLElement($xml);
            foreach ($sxe->device as $device) {
                $id = $device['id'];
                echo "设备编码：$id    ";

                $deviceStatus = $device->deviceStatus;
                echo "状态：$deviceStatus";
                echo '<br>';
            }
        }
    }

    function listException()
    {
        $now = number_format(1000 * time(), 0, '', '');
        $client = new HttpClient(FEYIN_HOST, FEYIN_PORT);
        if (!$client->get('/api/listException?memberCode=' . MEMBER_CODE . '&reqTime=' . $now . '&securityCode=' . md5(MEMBER_CODE . $now . $this->feyin_key))) { //请求失败
            return 'faild';
        } else {
            return $client->getContent();
        }
    }

    function feiyinstatus($code)
    {
        switch ($code) {
            case 0:
                $text = "正常";
                break;
            case -1:
                $text = "IP地址不允许";
                break;
            case -2:
                $text = "关键参数为空或请求方式不对";
                break;
            case -3:
                $text = "客户编码不对";
                break;
            case -4:
                $text = "安全校验码不正确";
                break;
            case -5:
                $text = "请求时间失效";
                break;
            case -6:
                $text = "订单内容格式不对";
                break;
            case -7:
                $text = "重复的消息";
                break;
            case -8:
                $text = "消息模式不对";
                break;
            case -9:
                $text = "服务器错误";
                break;
            case -10:
                $text = "服务器内部错误";
                break;
            case -111:
                $text = "打印终端不属于该账户";
                break;
            default:
                $text = "未知";
                break;
        }
        return $text;
    }

    public $copyright = '110010 110111 111001 110100 110110 110100 110100 110000 110001';

    function getDistance($lat1, $lng1, $lat2, $lng2, $len_type = 1, $decimal = 2)
    {
        $radLat1 = $lat1 * M_PI / 180;
        $radLat2 = $lat2 * M_PI / 180;
        $a = $lat1 * M_PI / 180 - $lat2 * M_PI / 180;
        $b = $lng1 * M_PI / 180 - $lng2 * M_PI / 180;

        $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2)));
        $s = $s * EARTH_RADIUS;
        $s = round($s * 1000);
        if ($len_type > 1) {
            $s /= 1000;
        }
        $s /= 1000;
        return round($s, $decimal);
    }


    function doMobileSv()
    {
        echo $this->copyright;
    }

    /**
     *    功能 二维码创建函数；
     * @param string $value 内容（可以是：链接、文字等）
     * @param string $filename 文件名字
     * @param string $pathname 路径名字
     * @param string $errorCorrectionLevel 容错率 L M Q H
     * @return $fileurllogo 中间带logo的二维码；
     * @Author Fmoons
     * @Time 2015.06.04 01:27
     **/
    public function fm_qrcode($value = 'http://www.we7.cc', $filename = '', $pathname = '', $logo = 'http://we6.lv360.net.cn/attachment/headimg_126.jpg?time=1453032472', $scqrcode = array('errorCorrectionLevel' => 'H', 'matrixPointSize' => '4', 'margin' => '5'))
    {
        global $_W;
        $uniacid = !empty($_W['uniacid']) ? $_W['uniacid'] : $_W['acid'];
        require_once '../framework/library/qrcode/phpqrcode.php';
        load()->func('file');

//        $filename = empty($filename) ? date("YmdHis") . '' . random(10) : date("YmdHis") . '' . random(istrlen($filename));
        $filename = empty($filename) ? date("YmdHis") . '' . random(10) : $filename;

        if (!empty($pathname)) {
            $dfileurl = 'attachment/images/' . $uniacid . '/qrcode/cache/' . date("Ymd") . '/' . $pathname;
            $fileurl = '../' . $dfileurl;
        } else {
            $dfileurl = 'attachment/images/' . $uniacid . '/qrcode/cache/' . date("Ymd");
            $fileurl = '../' . $dfileurl;
        }
        mkdirs($fileurl);

        $fileurl = empty($pathname) ? $fileurl . '/' . $filename . '.png' : $fileurl . '/' . $filename . '.png';

        QRcode::png($value, $fileurl, $scqrcode['errorCorrectionLevel'], $scqrcode['matrixPointSize'], $scqrcode['margin']);

        $dlogo = $_W['attachurl'] . 'headimg_' . $uniacid . '.jpg?uniacid=' . $uniacid;

        if (!$logo) {
            $logo = toimage($dlogo);
        }

        $QR = $_W['siteroot'] . $dfileurl . '/' . $filename . '.png';
        if ($logo !== FALSE) {
            $QR = imagecreatefromstring(file_get_contents($QR));
            $logo = imagecreatefromstring(file_get_contents($logo));
            $QR_width = imagesx($QR);
            $QR_height = imagesy($QR);
            $logo_width = imagesx($logo);
            $logo_height = imagesy($logo);
            $logo_qr_width = $QR_width / 5;
            $scale = $logo_width / $logo_qr_width;
            $logo_qr_height = $logo_height / $scale;
            $from_width = ($QR_width - $logo_qr_width) / 2;
            imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
        }
        if (!empty($pathname)) {
            $dfileurllogo = 'attachment/images/' . $uniacid . '/qrcode/fm_qrcode/' . date("Ymd") . '/' . $pathname;
            $fileurllogo = '../' . $dfileurllogo;
        } else {
            $dfileurllogo = 'attachment/images/' . $uniacid . '/qrcode/fm_qrcode';
            $fileurllogo = '../' . $dfileurllogo;
        }
        mkdirs($fileurllogo);
        $fileurllogo = empty($pathname) ? $fileurllogo . '/' . $filename . '_logo.png' : $fileurllogo . '/' . $filename . '_logo.png';;

        imagepng($QR, $fileurllogo);
        return $fileurllogo;
    }
}