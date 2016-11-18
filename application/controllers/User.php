<?php
class UserController extends BaseController
{
	public $_title = '用户管理';
	public function init()
	{
		parent::init();
		if ( $this->_api ) $this->user = new UserModel(); // 加载 实例化访问数据模型的对象
	}
	
	public function indexAction()
	{
		$this->view();
	}
	
	public function _addAction()
	{
		
	}
	
}