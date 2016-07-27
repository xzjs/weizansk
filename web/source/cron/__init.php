<?php

/**

 * [yushunbox System] Copyright (c) 2014 yushunbox.Com

 * yushunbox is NOT a free software, it under the license terms, visited http://www.yushunbox.com/ for more details.

 */

if($action != 'entry') {

	define('FRAME', 'setting');

	$frames = buildframes(array(FRAME));

	$frames = $frames[FRAME];

}

