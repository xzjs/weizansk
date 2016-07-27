<?php



/**

 * [yushunbox System] Copyright (c) 2014 yushunbox.com

 * yushunbox is NOT a free software, it under the license terms, visited http://www.yushunbox.com/ for more details.

 */



!defined('IN_UC') && exit('Access Denied');



class cachecontrol extends base {



	function __construct() {

		$this->cachecontrol();

	}



	function cachecontrol() {

		parent::__construct();

	}



	function onupdate($arr) {

		$this->load("cache");

		$_ENV['cache']->updatedata();

	}



}



?>