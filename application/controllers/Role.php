<?php
class RoleController extends BaseController
{
	public $_title = '角色管理';
	public function init()
	{
		parent::init();
		if ( $this->_api ) $this->user = new RoleModel(); // 加载 实例化访问数据模型的对象
	}
	
	public function indexAction()
	{
		$this->view();
	}
	
	public function _addAction()
	{
		
	}
	
	public function _queryAction()
	{
		
	}
	
	public function _updateAction()
	{
		
	}
}