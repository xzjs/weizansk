<?php

/**

 * [yushunbox System] Copyright (c) 2014 yushunbox.com

 * yushunbox is NOT a free software, it under the license terms, visited http://www.yushunbox.com/ for more details.

 */

defined('IN_IA') or exit('Access Denied');

		global $_W, $_GPC;

		load()->func('tpl');

        $dos = array('list', 'post');

		$do = in_array($do, $dos) ? $do : 'list';

        $mid    = $_GPC['mid'];

        $id     = $_GPC['id'];

        $pindex = max(1, intval($_GPC['page']));

        $psize  = 2;

        if (!empty($_GPC['keyword'])) {

            $condition .= " and title LIKE '%" . $_GPC['keyword'] . "%'";

        }

        if ($do == 'list') {

            $modules = pdo_fetchall("SELECT * FROM " . tablename('modules') . " where 1" . $condition, array(), 'name');

            $total   = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('modules') . " WHERE 1" . $condition);

            $pager   = pagination($total, $pindex, $psize);

        } elseif ($do == 'post') {

            $modules = pdo_fetch("SELECT * FROM " . tablename('modules') . "where mid=:mid", array(

                ':mid' => $mid

            ), 'name');

            $items   = pdo_fetch("SELECT * FROM " . tablename('buymod_modules') . "where module=:module", array(

                ':module' => $modules['name']

            ));

        }

        if (checksubmit('submit')) {

            $data = array(

                'weid' => $_W['uniacid'],

                'mid' => $_GPC['mid'],

                'name' => $_GPC['name'],

                'module' => $_GPC['module'],

                'price' => $_GPC['price'],

                'outLink' => $_GPC['outLink']

            );

            if (empty($items)) {

                pdo_insert('buymod_modules', $data);

                pdo_update('modules', array(

                    'title' => $_GPC['name']

                ), array(

                    'name' => $items['module']

                ));

            } else {

                pdo_update('buymod_modules', $data, array(

                    'module' => $items['module']

                ));

                pdo_update('modules', array(

                    'title' => $_GPC['name']

                ), array(

                    'name' => $items['module']

                ));

            }

            message('设置成功！', url('members/module', array(

                'op' => 'list'

            )), 'success');

        }

        template('members/plug/module');

	