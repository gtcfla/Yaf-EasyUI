<?php

class BaseController extends Yaf_Controller_Abstract
{
	var $_config; //全局配置
	var $_title; //模块标题
	var $_view = null; //模板视图对象
	var $_api = false; //是否接口
	var $_req = null; //请求对象
	var $_c; //当前控制器
	var $_a; //当前操作
	var $_tpl = ''; //渲染指定模板
	
	public function init()
	{
		$this->_config = Yaf_Registry::get('config'); // 获取全局的配置
		$this->_req = $this->getRequest();
		$this->_c = $this->_req->getControllerName();
		$this->_a = $this->_req->getActionName();
		if (preg_match('/^_/', $this->_a))
		{
			$this->_api = true;
		}
		else 
		{
			$this->_view = $this->getView();
		}
	}
	
	public function view()
	{
		$this->_view->assign('title', $this->_title ); // 设置标题
		$this->_view->display($this->_tpl ? $this->_tpl : "{$this->_c}.phtml" );
	}
	
	public function __destruct()
	{
// 		$this->log();
	}
	
	public function log()
	{
		Seaslog::info();
	}
}