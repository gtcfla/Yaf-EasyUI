<?php

class MenuController extends BaseController {

	var $_title = '菜单管理';

	public function init()
	{
		parent::init();
	}
	
	public function indexAction()
	{
	}

	public function _queryAction()
	{
		
	}
	
	public function _treeAction()
	{
		header( 'Content-type: application/json' );
		header( 'Connection: close' );
		$menu = [
				['id' => 1, 'pid' => 0, 'name' => '一级菜单'],
				['id' => 2, 'pid' => 1, 'name' => '二级菜单', 'url' => 'menu/_query'],
				['id' => 3, 'pid' => 1, 'name' => '刷新菜单', 'url' => 'menu/_refresh'],
				['id' => 4, 'pid' => 0, 'name' => '一级菜单'],
				['id' => 5, 'pid' => 4, 'name' => '2二级菜单', 'url' => 'login/index'],
		];
		exit(json_encode($menu));
	}
	
	public function _refreshAction()
	{
		$ctrl_path = Yaf_Registry::get('config')->application->directory;
	    $ctrl_path .= DIRECTORY_SEPARATOR.'controllers';
	    
		$ctrl_files = Util::directory_map($ctrl_path, '[Base.php|Error.php]');
		$arr_ctrl = [];
		foreach ($ctrl_files as $file)
		{
			if (file_exists($file))
			{
				require_once $file;
				$basename = basename($file,".php");
				$ctrl = $basename."Controller";
				$class = new ReflectionClass($ctrl);
				$arr_method = $class->getMethods(ReflectionMethod::IS_PUBLIC);;
				foreach ($arr_method as $method)
				{
					if (strpos($method->name, 'Action') > 1) $arr_data[$basename][] = strstr($method->name, 'Action', true);
				}
			}
		}
		echo '<pre>';
		print_r($arr_data);
		echo '</pre>';
		exit;
	}
}
