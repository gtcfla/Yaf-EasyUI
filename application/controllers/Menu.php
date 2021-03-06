<?php

class MenuController extends BaseController
{
	public $menu;
	public $_title = '菜单管理';
	public function init()
	{
		parent::init();
		if ($this->_api) $this->menu = new MenuModel();
	}
	
	public function indexAction()
	{
		$this->view();
	}
	
	public function _addAction()
	{
		$data = [];
		foreach (['pid', 'name', 'controller', 'action', 'sort', 'display'] as $field)
		{
			if (isset($_POST[$field])) $data[$field] = !strlen( $this->_req->getPost($field)) ? null : $this->_req->getPost($field); //参数获取(post)
		}
		if ($data && $this->menu->insertMenu($data)) $this->_result['ack'] = 1;
		$this->result();
	}
	
	public function _deleteAction()
	{
		if ($id = $this->_req->getPost('id')) 
		{
			$this->menu->delete(['id' => $id]);
			$this->menu->update(['pid' => 0], ['pid' => $id]);
			$this->_result['ack'] = 1;
			$this->result();
		}
	}

	public function _queryAction()
	{
		header('Content-type: application/json');
		header('Connection: close');
		if ($id = $this->_req->getQuery('id'))
		{
			$data = $this->menu->select("*", ["id" => $id]);
			exit(json_encode(['rows' => $data]));
		}
	}
	
	public function _updateAction()
	{
		$id = $this->_req->getPost( 'id' ); //参数获取(post)
		$data = [];
		foreach (['pid', 'name', 'controller', 'action', 'sort', 'display'] as $field)
		{
			if ($this->_req->getPost($field)) $data[$field] = $this->_req->getPost($field); //参数获取(post)
		}
		if ($id && $data && $this->menu->updateMenuById($data, $id)) $this->_result['ack'] = 1; // 设置返回状态&错误信息
		$this->result();
	}
	
	public function _getTreeAction()
	{
		header( 'Content-type: application/json' );
		header( 'Connection: close' );
		$menuTree = $this->menu->getMenuTreeList();
		$data = [];
		foreach ($menuTree as $mt)
		{
			$data[$mt['id']] = $mt;
		}
		exit(json_encode(Util::getTree($data)));
	}
	
	public function _treeAction()
	{
		header( 'Content-type: application/json' );
		header( 'Connection: close' );
		exit(json_encode($this->_user['menutree']));
	}
	
	public function _refreshAction()
	{
		$ctrl_path = $this->_config->application->directory;
	    $ctrl_path .= DIRECTORY_SEPARATOR.'controllers';
	    
		$ctrl_files = Util::directoryMap($ctrl_path, '[Base.php|Error.php|T.php|Login.php|Index.php|Api.php]');
		$arr_data = [];
		foreach ($ctrl_files as $file)
		{
			if (file_exists($file))
			{
				require_once $file;
				$basename = basename($file,".php");
				$ctrl = $basename."Controller";
				$class = new ReflectionClass($ctrl);
				$arr_method = $class->getMethods(ReflectionMethod::IS_PUBLIC);
				$arr_name = $class->getDefaultProperties();
				foreach ($arr_method as $method)
				{
					$action = strstr($method->name, 'Action', true);
					if ($action) $arr_data[$basename.'_'.$arr_name['_title']][$action] = $action;
				}
			}
		}
		$this->_result['ack'] = $this->menu->updateMenuList($arr_data);
		$this->result();
	}
	
	public function _queryTreeAction()
	{
		$pid = $this->_req->getQuery('pid');
		$frid = $this->_req->getQuery('role_id');
		$menuList = $this->menu->getMenuTreeList();
		$roleMenuId = [];
		if (!empty($menuList) && $frid)
		{
			$roleMenu = new RoleMenuModel();
			$roleMenuId = $roleMenu->getRoleMenuIdList( $frid );
		}
		foreach( $menuList as $k => $v )
		{
			$data[$v['id']] = $v;
			$data[$v['id']]['checked'] = in_array( $v['id'], $roleMenuId ) ? true : false;
		}
		$this->_result['data'] = Util::getTree($data);
		$this->_pfmt = 'jd';
		$this->result();
	}
}
