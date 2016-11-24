<?php

class LoginController extends BaseController {

	var $_title = '用户登录';
	
	public function indexAction()
	{
		$this->view();
	}
	
	public function _queryAction()
	{
		Yaf_Session::getInstance();
		var_dump($_SESSION['test']);
	}
	
	public function _logoutAction()
	{
		setcookie(session_name(),null,-1,'/');
		session_start();
		session_destroy();
		$this->_result['ack'] = 1;
		$this->_result['data']['url'] = '/login/index';
		$this->result();
	}
}
