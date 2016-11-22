<?php

class RoleMenuModel extends Db_medoo
{
	public $table = 'y_role_menu'; // 表名

	public function __construct($pool = 'm0')
	{
		parent::__construct( Yaf_Registry::get( 'config' )->database->$pool->toArray() );
	}

	/**
	 * 根据角色ID获取菜单列表
	 */
	public function getRoleMenuPidList($roleId)
	{
	} 

	/**
	 * 查找角色拥有所属的菜单权限
	 */
	public function getRoleMenuIdList($roleId)
	{
		$result = $this->select(['menu_id'], ['role_id' => $roleId]);
		$data = [];
			foreach( $result as $k => $v ) $data[] = $v['menu_id'];
		return $data;
	} 
} 
