<?php

class MenuController extends BaseController
{
	public $_title = '菜单管理';
	public $menu;

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
		if ($id = $this->_req->getPost('id')) $this->_result['ack'] = $this->menu->delete(["id" => $id]);
		$this->result();
	}

	public function _queryAction()
	{
		header( 'Content-type: application/json' );
		header( 'Connection: close' );
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
			if (isset($_POST[$field])) $data[$field] = !strlen( $this->_req->getPost($field)) ? null : $this->_req->getPost($field); //参数获取(post)
		}
		if ($id && $data && $this->menu->updateMenu($data, $id)) $this->_result['ack'] = 1; // 设置返回状态&错误信息
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
		exit(json_encode($this->menu->getMenu()));
	}
	
	public function _refreshAction()
	{
		$ctrl_path = Yaf_Registry::get('config')->application->directory;
	    $ctrl_path .= DIRECTORY_SEPARATOR.'controllers';
	    
		$ctrl_files = Util::directoryMap($ctrl_path, '[Base.php|Error.php|T.php|Login.php|Index.php]');
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
		foreach ($arr_data as $k => $ad)
		{
			list($data['controller'], $data['name']) = explode('_', $k);
			$data['action'] = 'index';
			$last_id = $this->menu->insert($data);
			if (!$last_id) $last_id = $this->menu->select('id', ['AND' => $data])[0];
			foreach ($ad as $d)
			{
				if ($d === 'index') continue;
				$child = [
					'pid' => $last_id,
					'name' => $d,
					'controller' => $data['controller'],
					'action' => $d
				];
				$this->menu->insert($child);
			}
		}
		$this->_result['ack'] = 1;
		$this->result();
	}
}
