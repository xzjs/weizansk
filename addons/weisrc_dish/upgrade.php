<?php
$sql = "
CREATE TABLE IF NOT EXISTS `ims_weisrc_dish_area` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `weid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属帐号',
    `name` varchar(50) NOT NULL COMMENT '区域名称',
    `parentid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上级分类ID,0为第一级',
    `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
    `dateline` int(10) unsigned NOT NULL DEFAULT '0',
    `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态',
    PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ims_weisrc_dish_print_setting` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `weid` int(10) unsigned NOT NULL,
    `storeid` int(10) unsigned NOT NULL,
    `print_status` tinyint(1) NOT NULL,
    `print_type` tinyint(1) NOT NULL,
    `print_usr` varchar(50) DEFAULT '',
    `print_nums` tinyint(3) DEFAULT '1',
    `print_top` varchar(40) DEFAULT '',
    `print_bottom` varchar(40) DEFAULT '',
    `dateline` int(10) DEFAULT '0',
    PRIMARY KEY (`id`)
)ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ims_weisrc_dish_print_order` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `weid` int(10) unsigned NOT NULL,
    `orderid` int(10) unsigned NOT NULL,
    `print_usr` varchar(50) DEFAULT '',
    `print_status` tinyint(1) DEFAULT '-1',
    `dateline` int(10) DEFAULT '0',
    PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ims_weisrc_dish_sms_checkcode` (
        `id` int(10) NOT NULL AUTO_INCREMENT,
        `weid` int(10) unsigned NOT NULL,
        `from_user` varchar(100) DEFAULT '' COMMENT '用户ID',
        `tel` varchar(30) NOT NULL DEFAULT '' COMMENT '手机',
        `checkcode` varchar(100) DEFAULT '' COMMENT '验证码',
        `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态 0未使用1已使用',
        `dateline` int(10) DEFAULT '0' COMMENT '创建时间',
        PRIMARY KEY (`id`)
    )  ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ims_weisrc_dish_address` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `from_user` varchar(50) NOT NULL,
  `realname` varchar(20) NOT NULL,
  `mobile` varchar(11) NOT NULL,
  `address` varchar(300) NOT NULL,
  `dateline` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=403 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS  `ims_weisrc_dish_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属帐号',
  `name` varchar(50) NOT NULL COMMENT '类型名称',
  `parentid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上级分类ID,0为第一级',
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ims_weisrc_dish_collection` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `from_user` varchar(50) NOT NULL,
  `storeid` int(10) unsigned NOT NULL,
  `dateline` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=39 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS  `ims_weisrc_dish_tables` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属帐号',
  `storeid` int(10) unsigned NOT NULL DEFAULT '0',
  `tablezonesid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(200) NOT NULL DEFAULT '' COMMENT '名字(桌台号)',
  `user_count` int(10) NOT NULL DEFAULT '0' COMMENT '可供就餐人数',
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS  `ims_weisrc_dish_tables_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属帐号',
  `tablesid` int(10) unsigned NOT NULL DEFAULT '0',
  `storeid` int(10) unsigned NOT NULL DEFAULT '0',
  `from_user` varchar(200) NOT NULL DEFAULT '',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS  `ims_weisrc_dish_tablezones` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属帐号',
  `storeid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(200) NOT NULL DEFAULT '',
  `limit_price` int(10) unsigned NOT NULL DEFAULT '0',
  `reservation_price` int(10) unsigned NOT NULL DEFAULT '0',
  `table_count` int(10) NOT NULL DEFAULT '0' COMMENT '餐桌数量',
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ims_weisrc_dish_template` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `weid` int(10) NOT NULL DEFAULT '0',
  `template_name` varchar(50) NOT NULL DEFAULT 'style1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS  `ims_weisrc_dish_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属帐号',
  `name` varchar(50) NOT NULL COMMENT '类型名称',
  `parentid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上级分类ID,0为第一级',
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS  `ims_weisrc_dish_reservation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属帐号',
  `storeid` int(10) unsigned NOT NULL DEFAULT '0',
  `tablezonesid` int(10) unsigned NOT NULL DEFAULT '0',
  `time` varchar(200) NOT NULL DEFAULT '',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS  `ims_weisrc_dish_queue_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属帐号',
  `queueid` int(10) unsigned NOT NULL DEFAULT '0',
  `storeid` int(10) unsigned NOT NULL DEFAULT '0',
  `from_user` varchar(200) NOT NULL DEFAULT '',
  `num` varchar(100) NOT NULL DEFAULT '',
  `mobile` varchar(30) NOT NULL DEFAULT '',
  `usercount` int(10) unsigned NOT NULL DEFAULT '0',
  `isnotify` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS  `ims_weisrc_dish_queue_setting` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属帐号',
  `storeid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(200) NOT NULL DEFAULT '',
  `limit_num` int(10) unsigned NOT NULL DEFAULT '0',
  `prefix` varchar(50) NOT NULL,
  `starttime` varchar(50) NOT NULL DEFAULT '00:00' COMMENT '开始时间',
  `endtime` varchar(50) NOT NULL DEFAULT '23:59' COMMENT '结束时间',
  `notify_number` int(10) NOT NULL DEFAULT '0' COMMENT '通知人数',
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ims_weisrc_dish_fans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `weid` int(11) DEFAULT '0',
  `from_user` varchar(50) DEFAULT '' COMMENT '用户ID',
  `nickname` varchar(50) DEFAULT '',
  `headimgurl` varchar(500) DEFAULT '',
  `username` varchar(50) DEFAULT '',
  `mobile` varchar(50) DEFAULT '',
  `address` varchar(200) DEFAULT '',
  `sex` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '性别',
  `lat` decimal(18,10) NOT NULL DEFAULT '0.0000000000' COMMENT '经度',
  `lng` decimal(18,10) NOT NULL DEFAULT '0.0000000000' COMMENT '纬度',
  `status` tinyint(1) DEFAULT '1',
  `dateline` int(10) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `indx_rid` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_weisrc_dish_account` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属帐号',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `pwd` varchar(50) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `email` varchar(20) NOT NULL,
  `from_user` varchar(100) NOT NULL DEFAULT '',
  `storeid` varchar(1000) NOT NULL,
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ims_weisrc_dish_ad` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL,
  `title` varchar(100) NOT NULL DEFAULT '',
  `url` varchar(200) NOT NULL DEFAULT '',
  `thumb` varchar(500) NOT NULL DEFAULT '',
  `position` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1:首页,2:商家页',
  `starttime` int(10) NOT NULL DEFAULT '0' COMMENT '开始时间',
  `endtime` int(10) NOT NULL DEFAULT '0' COMMENT '结束时间',
  `displayorder` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否显示',
  `dateline` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_weisrc_dish_address` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `from_user` varchar(50) NOT NULL,
  `realname` varchar(20) NOT NULL,
  `mobile` varchar(11) NOT NULL,
  `address` varchar(300) NOT NULL,
  `dateline` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
pdo_run($sql);

if(!pdo_fieldexists('weisrc_dish_stores', 'areaid')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_stores')." ADD `areaid` int(10) NOT NULL DEFAULT '0' COMMENT '区域id';");
}

if(!pdo_fieldexists('weisrc_dish_order', 'print_sta')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_order')." ADD `print_sta` tinyint(1) DEFAULT '-1' COMMENT '打印状态';");
}
if(!pdo_fieldexists('weisrc_dish_order', 'tables')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_order')." ADD `tables` varchar(10) NOT NULL DEFAULT '' COMMENT '桌号';");
}
if(!pdo_fieldexists('weisrc_dish_order', 'dining_mode')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_order')." ADD `dining_mode` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '用餐类型 1:到店 2:外卖';");
}
if(!pdo_fieldexists('weisrc_dish_order', 'address')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_order')." ADD `address` varchar(250) NOT NULL DEFAULT '' COMMENT '地址';");
}
if(!pdo_fieldexists('weisrc_dish_order', 'sign')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_order')." ADD `sign` tinyint(1) NOT NULL DEFAULT '0' COMMENT '-1拒绝，0未处理，1已处理';");
}
if(!pdo_fieldexists('weisrc_dish_order', 'reply')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_order')." ADD `reply` varchar(1000) NOT NULL DEFAULT '' COMMENT '回复';");
}
//ims_weisrc_dish_setting
if(!pdo_fieldexists('weisrc_dish_setting', 'storeid')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_setting')." ADD `storeid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '默认门店';");
}
if(!pdo_fieldexists('weisrc_dish_setting', 'dining_mode')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_setting')." ADD `dining_mode` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '用餐类型 1:到店 2:外卖';");
}
if(!pdo_fieldexists('weisrc_dish_print_setting', 'title')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_print_setting')." ADD `title` varchar(200) DEFAULT '';");
}
//ims_weisrc_dish_email_setting
if(!pdo_fieldexists('weisrc_dish_email_setting', 'email_host')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_email_setting')." ADD `email_host` varchar(50) DEFAULT '' COMMENT '邮箱服务器';");
}
if(!pdo_fieldexists('weisrc_dish_email_setting', 'email_send')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_email_setting')." ADD `email_send` varchar(20) DEFAULT '' COMMENT '商户发送邮件邮箱';");
}
if(!pdo_fieldexists('weisrc_dish_email_setting', 'email_pwd')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_email_setting')." ADD `email_pwd` varchar(20) DEFAULT '' COMMENT '邮箱密码';");
}
if(!pdo_fieldexists('weisrc_dish_email_setting', 'email_user')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_email_setting')." ADD `email_user` varchar(100) DEFAULT '' COMMENT '发信人名称';");
}

if(!pdo_fieldexists('weisrc_dish_stores', 'is_meal')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_stores')." ADD `is_meal` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否店内点餐';");
}
if(!pdo_fieldexists('weisrc_dish_stores', 'is_delivery')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_stores')." ADD `is_delivery` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否外卖订餐';");
}
if(!pdo_fieldexists('weisrc_dish_stores', 'sendingprice')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_stores')." ADD `sendingprice` varchar(10) NOT NULL DEFAULT '' COMMENT '起送价格';");
}

if(!pdo_fieldexists('weisrc_dish_mealtime', 'storeid')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_mealtime')." ADD `storeid` int(10) unsigned NOT NULL;");
}
if(!pdo_fieldexists('weisrc_dish_mealtime', 'status')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_mealtime')." ADD `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否开启';");
}
if(!pdo_fieldexists('weisrc_dish_mealtime', 'dateline')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_mealtime')." ADD  `dateline` int(10) DEFAULT '0';");
}
if(!pdo_fieldexists('weisrc_dish_order', 'transid')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_order')." ADD  `transid` varchar(30) NOT NULL DEFAULT '0' COMMENT '微信支付单号';");
}
if(!pdo_fieldexists('weisrc_dish_order', 'goodsprice')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_order')." ADD   `goodsprice` decimal(10,2) DEFAULT '0.00';");
}
if(!pdo_fieldexists('weisrc_dish_order', 'dispatchprice')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_order')." ADD  `dispatchprice` decimal(10,2) DEFAULT '0.00';");
}
if(!pdo_fieldexists('weisrc_dish_order', 'isemail')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_order')." ADD  `isemail` tinyint(1) NOT NULL DEFAULT '0';");
}
if(!pdo_fieldexists('weisrc_dish_order', 'issms')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_order')." ADD    `issms` tinyint(1) NOT NULL DEFAULT '0';");
}
if(!pdo_fieldexists('weisrc_dish_order', 'istpl')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_order')." ADD    `istpl` tinyint(1) NOT NULL DEFAULT '0';");
}
if(!pdo_fieldexists('weisrc_dish_print_setting', 'qrcode_status')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_print_setting')." ADD  `qrcode_status` tinyint(1) NOT NULL DEFAULT '0';");
}
if(!pdo_fieldexists('weisrc_dish_print_setting', 'qrcode_url')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_print_setting')." ADD  `qrcode_url` varchar(200) DEFAULT '';");
}

if(!pdo_fieldexists('weisrc_dish_setting', 'istplnotice')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_setting')." ADD     `istplnotice` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否模版通知';");
}
if(!pdo_fieldexists('weisrc_dish_setting', 'tplneworder')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_setting')." ADD    `tplneworder` varchar(200) DEFAULT '' COMMENT '模板id';");
}
if(!pdo_fieldexists('weisrc_dish_setting', 'tpluser')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_setting')." ADD   `tpluser` text COMMENT '通知用户';");
}
if(!pdo_fieldexists('weisrc_dish_stores', 'dispatchprice')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_stores')." ADD    `dispatchprice` decimal(10,2) DEFAULT '0.00';");
}

if(!pdo_fieldexists('weisrc_dish_print_setting', 'type')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_print_setting')." ADD     `type` varchar(50) DEFAULT 'hongxin';");
}
if(!pdo_fieldexists('weisrc_dish_print_setting', 'member_code')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_print_setting')." ADD     `member_code` varchar(100) DEFAULT '' COMMENT '商户代码';");
}
if(!pdo_fieldexists('weisrc_dish_print_setting', 'feyin_key')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_print_setting')." ADD    `feyin_key` varchar(100) DEFAULT '' COMMENT 'api密钥';");
}

if(!pdo_fieldexists('weisrc_dish_setting', 'searchword')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_setting')." ADD     `searchword` varchar(1000) DEFAULT '' COMMENT '搜索关键字';");
}

if(!pdo_fieldexists('weisrc_dish_stores', 'is_hot')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_stores')." ADD     `is_hot` tinyint(1) NOT NULL DEFAULT '0' COMMENT '搜索页显示';");
}
if(!pdo_fieldexists('weisrc_dish_stores', 'freeprice')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_stores')." ADD    `freeprice` decimal(10,2) DEFAULT '0.00';");
}

if(!pdo_fieldexists('weisrc_dish_stores', 'begintime')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_stores')." ADD   `begintime` varchar(20) DEFAULT '09:00' COMMENT '开始时间';");
}

if(!pdo_fieldexists('weisrc_dish_stores', 'announce')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_stores')." ADD   `announce` varchar(1000) NOT NULL DEFAULT '' COMMENT '通知';");
}
if(!pdo_fieldexists('weisrc_dish_stores', 'endtime')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_stores')." ADD    `endtime` varchar(20) DEFAULT '18:00' COMMENT '结束时间';");
}
if(!pdo_fieldexists('weisrc_dish_stores', 'consume')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_stores')." ADD     `consume` varchar(20) NOT NULL COMMENT '人均消费';");
}
if(!pdo_fieldexists('weisrc_dish_stores', 'level')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_stores')." ADD     `level` tinyint(1) NOT NULL DEFAULT '1' COMMENT '级别';");
}
if(!pdo_fieldexists('weisrc_dish_stores', 'is_rest')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_stores')." ADD     `is_rest` tinyint(1) NOT NULL DEFAULT '0';");
}
if(!pdo_fieldexists('weisrc_dish_stores', 'typeid')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_stores')." ADD     `typeid` int(10) NOT NULL DEFAULT '0' COMMENT '商家类型';");
}

if(!pdo_fieldexists('weisrc_dish_stores', 'from_user')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_stores')." ADD      `from_user` varchar(200) NOT NULL DEFAULT '';");
}
if(!pdo_fieldexists('weisrc_dish_stores', 'delivery_within_days')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_stores')." ADD      `delivery_within_days` int(10) NOT NULL DEFAULT '0';");
}
if(!pdo_fieldexists('weisrc_dish_stores', 'delivery_radius')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_stores')." ADD      `delivery_radius` decimal(18,1) NOT NULL DEFAULT '0.0' COMMENT '半径';");
}
if(!pdo_fieldexists('weisrc_dish_stores', 'not_in_delivery_radius')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_stores')." ADD      `not_in_delivery_radius` tinyint(1) NOT NULL DEFAULT '1' COMMENT '在配送半径之外是否允许下单';");
}
if(!pdo_fieldexists('weisrc_dish_stores', 'btn_reservation')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_stores')." ADD      `btn_reservation` varchar(100) NOT NULL DEFAULT '预定' COMMENT '预定按钮';");
}
if(!pdo_fieldexists('weisrc_dish_stores', 'btn_eat')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_stores')." ADD      `btn_eat` varchar(100) NOT NULL DEFAULT '点菜' COMMENT '点菜按钮';");
}
if(!pdo_fieldexists('weisrc_dish_stores', 'btn_delivery')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_stores')." ADD      `btn_delivery` varchar(100) NOT NULL DEFAULT '外卖' COMMENT '外卖按钮';");
}
if(!pdo_fieldexists('weisrc_dish_stores', 'btn_snack')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_stores')." ADD      `btn_snack` varchar(100) NOT NULL DEFAULT '快餐' COMMENT '快餐按钮';");
}
if(!pdo_fieldexists('weisrc_dish_stores', 'btn_queue')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_stores')." ADD      `btn_queue` varchar(100) NOT NULL DEFAULT '排队' COMMENT '排队按钮';");
}
if(!pdo_fieldexists('weisrc_dish_stores', 'btn_intelligent')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_stores')." ADD      `btn_intelligent` varchar(100) NOT NULL DEFAULT '套餐' COMMENT '套餐按钮';");
}
if(!pdo_fieldexists('weisrc_dish_stores', 'is_snack')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_stores')." ADD      `is_snack` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否支持快餐';");
}
if(!pdo_fieldexists('weisrc_dish_stores', 'is_reservation')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_stores')." ADD      `is_reservation` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否支持预定';");
}
if(!pdo_fieldexists('weisrc_dish_stores', 'is_queue')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_stores')." ADD      `is_queue` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否支持排队';");
}
if(!pdo_fieldexists('weisrc_dish_stores', 'is_intelligent')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_stores')." ADD      `is_intelligent` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否支持套餐';");
}
if(!pdo_fieldexists('weisrc_dish_stores', 'coupon_title1')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_stores')." ADD      `coupon_title1` varchar(100) NOT NULL DEFAULT '' COMMENT '优惠名称';");
}
if(!pdo_fieldexists('weisrc_dish_stores', 'coupon_title2')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_stores')." ADD      `coupon_title2` varchar(100) NOT NULL DEFAULT '' COMMENT '优惠名称';");
}
if(!pdo_fieldexists('weisrc_dish_stores', 'coupon_title3')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_stores')." ADD      `coupon_title3` varchar(100) NOT NULL DEFAULT '' COMMENT '优惠名称';");
}
if(!pdo_fieldexists('weisrc_dish_stores', 'coupon_link1')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_stores')." ADD      `coupon_link1` varchar(200) NOT NULL DEFAULT '' COMMENT '优惠名称';");
}
if(!pdo_fieldexists('weisrc_dish_stores', 'coupon_link2')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_stores')." ADD      `coupon_link2` varchar(200) NOT NULL DEFAULT '' COMMENT '优惠名称';");
}
if(!pdo_fieldexists('weisrc_dish_stores', 'coupon_link3')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_stores')." ADD      `coupon_link3` varchar(200) NOT NULL DEFAULT '' COMMENT '优惠名称';");
}
if(!pdo_fieldexists('weisrc_dish_stores', 'qq')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_stores')." ADD      `qq` varchar(20) NOT NULL DEFAULT '';");
}
if(!pdo_fieldexists('weisrc_dish_stores', 'weixin')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_stores')." ADD      `weixin` varchar(20) NOT NULL DEFAULT '';");
}

if(!pdo_fieldexists('weisrc_dish_setting', 'mode')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_setting')." ADD    `mode` tinyint(1) NOT NULL DEFAULT '0' COMMENT '模式';");
}
if(!pdo_fieldexists('weisrc_dish_setting', 'tplnewqueue')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_setting')." ADD   `tplnewqueue` varchar(200) DEFAULT '' COMMENT '模板id';");
}
if(!pdo_fieldexists('weisrc_dish_setting', 'is_notice')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_setting')." ADD   `is_notice` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '开启提醒';");
}
if(!pdo_fieldexists('weisrc_dish_setting', 'sms_enable')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_setting')." ADD   `sms_enable` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '开启短信提醒';");
}
if(!pdo_fieldexists('weisrc_dish_setting', 'sms_username')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_setting')." ADD   `sms_username` varchar(20) DEFAULT '' COMMENT '平台帐号';");
}
if(!pdo_fieldexists('weisrc_dish_setting', 'sms_pwd')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_setting')." ADD   `sms_pwd` varchar(20) DEFAULT '' COMMENT '平台密码';");
}
if(!pdo_fieldexists('weisrc_dish_setting', 'sms_mobile')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_setting')." ADD   `sms_mobile` varchar(20) DEFAULT '' COMMENT '商户接收短信手机';");
}
if(!pdo_fieldexists('weisrc_dish_setting', 'email_enable')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_setting')." ADD   `email_enable` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '开启邮箱提醒';");
}
if(!pdo_fieldexists('weisrc_dish_setting', 'email_host')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_setting')." ADD   `email_host` varchar(50) DEFAULT '' COMMENT '邮箱服务器';");
}
if(!pdo_fieldexists('weisrc_dish_setting', 'email_send')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_setting')." ADD   `email_send` varchar(100) DEFAULT '' COMMENT '商户发送邮件邮箱';");
}
if(!pdo_fieldexists('weisrc_dish_setting', 'email_pwd')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_setting')." ADD   `email_pwd` varchar(20) DEFAULT '' COMMENT '邮箱密码';");
}
if(!pdo_fieldexists('weisrc_dish_setting', 'email_user')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_setting')." ADD   `email_user` varchar(100) DEFAULT '' COMMENT '发信人名称';");
}
if(!pdo_fieldexists('weisrc_dish_setting', 'email')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_setting')." ADD   `email` varchar(100) DEFAULT '' COMMENT '商户接收邮件邮箱';");
}
if(!pdo_fieldexists('weisrc_dish_setting', 'tpltype')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_setting')." ADD   `tpltype` tinyint(1) NOT NULL DEFAULT '1' COMMENT '模版行业类型';");
}
if(!pdo_fieldexists('weisrc_dish_print_setting', 'is_print_all')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_print_setting')." ADD    `is_print_all` tinyint(1) NOT NULL DEFAULT '1';");
}
if(!pdo_fieldexists('weisrc_dish_print_setting', 'print_goodstype')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_print_setting')." ADD     `print_goodstype` varchar(500) DEFAULT '0';");
}

if(!pdo_fieldexists('weisrc_dish_order', 'credit')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_order')." ADD   `credit` varchar(10) NOT NULL DEFAULT '0' COMMENT '赠送积分';");
}
if(!pdo_fieldexists('weisrc_dish_order', 'paydetail')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_order')." ADD   `paydetail` varchar(1000) NOT NULL DEFAULT '' COMMENT '消费详细信息';");
}
if(!pdo_fieldexists('weisrc_dish_order', 'ispay')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_order')." ADD   `ispay` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0,1,2';");
}
if(!pdo_fieldexists('weisrc_dish_order', 'tablezonesid')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_order')." ADD   `tablezonesid` varchar(10) NOT NULL DEFAULT '' COMMENT '桌台类别';");
}

if(pdo_fieldexists('weisrc_dish_goods', 'thumb')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_goods')." CHANGE  `thumb`  `thumb` varchar(500) NOT NULL DEFAULT '';");
}
if(!pdo_fieldexists('weisrc_dish_setting', 'sms_id')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_setting')." ADD `sms_id` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '短信模板ID' ;");
}
if(!pdo_fieldexists('weisrc_dish_setting', 'is_sms')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_setting')." ADD `is_sms` INT(10) NOT NULL DEFAULT '0' COMMENT '是否开启全局短信';");
}