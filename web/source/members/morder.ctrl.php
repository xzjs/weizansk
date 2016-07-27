<?php

/**

 * [yushunbox System] Copyright (c) 2014 yushunbox.com

 * yushunbox is NOT a free software, it under the license terms, visited http://www.yushunbox.com/ for more details.

 */

 defined('IN_IA') or exit('Access Denied');

        global $_W, $_GPC;

        $weid     = $_W['uniacid'];

        $modulebs = $_GPC['module'];

        $dos = array('list', 'post');

		$do = in_array($do, $dos) ? $do : 'post';

        if ($do == 'post') {

            $items     = pdo_fetch("SELECT * FROM " . tablename('buymod_modules') . "where module=:module", array(

                ':module' => $modulebs

            ));

            $member    = pdo_fetch("SELECT * FROM " . tablename('buymod_members') . " where uid=:uid", array(

                ':uid' => $_W['uid']

            ));

            $module    = pdo_fetch("SELECT * FROM " . tablename('buymod_mbuy') . " where module=:module and weid=:weid", array(

                ':module' => $modulebs,

                ':weid' => $weid

            ));

            $buymodule = pdo_fetch("SELECT * FROM " . tablename('uni_group') . " WHERE uniacid = :uniacid", array(

                ':uniacid' => $_W['uniacid']

            ));

            if (empty($items)) {

                message('你做购买的模块或套餐不存在，请联系管理员', referer, 'warning');

                exit;

            }

        }

        if (checksubmit('submit')) {

            $year  = $_GPC['time'];

            $price = $items['price'] * $year;

            if (empty($module)) {

                $starttime = TIMESTAMP;

                $star      = TIMESTAMP;

            } else {

                $starttime = $module['starttime'];

                if ($module['status'] == '2') {

                    $star = TIMESTAMP;

                } else {

                    $star = $module['endtime'];

                }

            }

            $endtime = date('Y', $star) + $year . '-' . date('m-d H:i:s');

            $record  = array(

                'weid' => $weid,

                'uid' => $_W['uid'],

                'module' => $modulebs,

                'price' => $price,

                'name' => $items['name'],

                'starttime' => TIMESTAMP,

                'endtime' => strtotime($endtime)

            );

            $data    = array(

                'weid' => $weid,

                'uid' => $_W['uid'],

                'module' => $modulebs,

                'price' => $price,

                'status' => '1',

                'name' => $items['name'],

                'starttime' => $starttime,

                'endtime' => strtotime($endtime)

            );

            if (empty($buymodule)) {

                $modules[] = $modulebs;

            } else {

                $moduleall = unserialize($buymodule['modules']);

                $i         = 0;

                foreach ($moduleall as $m) {

                    $modules[] .= $m;

                    $i = $i++;

                }

                $modules[] .= $modulebs;

            }

            $data1 = array(

                'modules' => iserializer($modules),

                'uniacid' => $_W['uniacid'],

                'name' => ''

            );

            if ($price > $member['credit']) {

                message('您的积分不足，请先充值', url('members/muser/post', array(

                    'op' => 'post'

                )), 'warning');

                die();

            }

            $credit = $member['credit'] - $price;

            if (empty($module)) {

                if (empty($buymodule)) {

                    pdo_insert('uni_group', $data1);

                } else {

                    pdo_update('uni_group', $data1, array(

                        'id' => $buymodule['id']

                    ));

                }

                pdo_insert('buymod_mbuy', $data);

            } else {

                if ($module['status'] == '1') {

                    pdo_update('buymod_mbuy', $data, array(

                        'module' => $modulebs

                    ));

                } else {

                    pdo_update('uni_group', $data1, array(

                        'id' => $buymodule['id']

                    ));

                    pdo_update('buymod_mbuy', $data, array(

                        'id' => $module['id']

                    ));

                }

            }

            cache_delete("unisetting:{$weid}");

            cache_delete("unimodules:{$weid}:1");

            cache_delete("unimodules:{$weid}:");

            cache_delete("uniaccount:{$weid}");

            load()->model('module');

            module_build_privileges();

            pdo_insert('buymod_record', $record);

            pdo_update('buymod_members', array(

                'credit' => $credit

            ), array(

                'uid' => $_W['uid']

            ));

            message('购买成功！', referer, 'sucess');

        }

        template('members/plug/order');