<?php
class RoleController extends BaseController
{
	public $_title = '角色管理';
	public function init()
	{
		parent::init();
		if ( $this->_api ) $this->role = new RoleModel(); // 加载 实例化访问数据模型的对象
	}
	
	public function indexAction()
	{
		$this->view();
	}
	
	public function _addAction()
	{
		$data = [];
		foreach (['name', 'pid', 'sort', 'create_time', 'update_time'] as $field )
		{
			if ($this->_req->getPost( $field )) $data[$field] = $this->_req->getPost( $field );
		}
		if ($data && $this->role->insert($data)) $this->_result['ack'] = 1;
		$this->result();
	}
	
	public function _queryAction()
	{
		$data = $options = $where = [];
		foreach (['id', 'name'] as $field) if ($this->_req->getQuery($field)) $data[$field] = $this->_req->getQuery( $field ); //参数获取(get)
		$this->selectCommon(['id', 'name(text)', 'pid', 'sort', 'create_time', 'update_time'], $data, $this->role); // 默认显示字段
		$this->result();
	}
	
	public function _updateAction()
	{
		$id = $this->_req->getPost('id'); //参数获取(post)
		$data = array();
		foreach (['name', 'pid', 'sort', 'create_time', 'update_time'] as $field )
		{
			if ($this->_req->getPost($field)) $data[$field] = $this->_req->getPost($field); //参数获取(post)
		}
		if ($id && $data && $this->role->update($data, ['id' => $id])) $this->_result['ack'] = 1; // 设置返回状态&错误信息
		$this->result();
	}
	
	/**
	 * 查询角色对应的菜单权限
	 */
	public function _queryRoleMenuAction()
	{
		$roleMenu = new RoleMenuModel();
		$this->selectCommon(['menu_id'], ['role_id' => $this->_req->getQuery('role_id')], $roleMenu); // 默认显示字段
		$data = [];
		if ( !empty( $this->_result['data'] ) && is_array( $this->_result['data'] ) ) {
			foreach( $this->_result['data'] as $k => $v ) {
				$data[] = $v['FMID'];
			}
		}
		$this->_result['data'] = $data;
		$this->_pfmt = 'jd';
		$this->result();
	}
	
	public function _roleTreeAction()
	{
		$roleList = $this->role->select(['id','pid','name(text)','sort','create_time', 'update_time']);
		$data = [];
		foreach ($roleList as $r)
		{
			$data[$r['id']] = $r;
		}
		$this->_result['data'] = Util::getTree($data);
		$this->_pfmt = 'jd';
		$this->result();
	}
}