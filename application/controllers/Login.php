<?php

class LoginController extends BaseController {

	var $_title = '用户登录';
	
	public function indexAction()
	{
		$this->view();
	}
	
	public function _queryAction()
	{
		echo 123;exit;
	}
}
