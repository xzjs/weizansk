<?php

/**

 * [yushunbox System] Copyright (c) 2015 yushunbox.com

 * yushunbox is NOT a free software, it under the license terms, visited http://www.yushunbox.com/ for more details.

 */

$_W['page']['title'] = '系统';



load()->model('cloud');



$cloud_registered = cloud_prepare();

$cloud_registered = $cloud_registered === true ? true : false;



template('system/welcome');

