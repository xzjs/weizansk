<?php

/**

 * [yushunbox System] Copyright (c) 2014 yushunbox.com

 * yushunbox is NOT a free software, it under the license terms, visited http://www.yushunbox.com/ for more details.

 */

if ($do == 'oauth' || $action == 'credit' || $action == 'passport' || $action == 'uc') {

	define('FRAME', 'setting');

} else {

	define('FRAME', 'members');

}



$frames = buildframes(array(FRAME));

$frames = $frames[FRAME];

