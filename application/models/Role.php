<?php
class RoleModel extends Db\Medoo
{
	public $table = 'y_role'; // 表名

	public function __construct($pool ='m0')
	{
		parent::__construct(Yaf\Registry::get('config')->database->$pool->toArray());
	}
	
	/**
	 * 获取角色列表关系数组
	 * return array()
	 */
	public function getRoleIdNameList()
	{
		$roleList = $this->select(['id', 'name']);
		$data = array();
		if (!empty($roleList)) 
		{
			foreach($roleList as $k => $v)
			{
				$data[$v['id']] = $v['name'];
			}
		}
		return $data;
	}
}