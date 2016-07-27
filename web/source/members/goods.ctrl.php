<?php

/**

 * [yushunbox System] Copyright (c) 2014 yushunbox.com

 * yushunbox is NOT a free software, it under the license terms, visited http://www.yushunbox.com/ for more details.

 */

defined('IN_IA') or exit('Access Denied');

global $_W,$_GPC;

if(!$_W['isfounder']) {

    message('不能访问, 需要创始人权限才能访问.');

}



include $this->template('members/goods');

