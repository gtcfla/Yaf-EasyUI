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
		if ($data)
		{
			$data['create_time'] = date('Y-m-d H:i:s');
			$this->role->insert($data);
			$this->_result['ack'] = 1;
		}
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
		if ($id && $data) $this->_result['ack'] = $this->role->update($data, ['id' => $id]);
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
	
	/**
	 * 角色之间权限复制
	 */
	public function _copyPrevilegeAction()
	{
		$source_frid = $this->_req->getPost('source_role_id');
		$target_frid = $this->_req->getPost('target_role_id');
		 
		$roleMenu = new RoleMenuModel();
		$arr_row = $roleMenu->select(['menu_id'], ['role_id' => $source_frid]); // 删除角色下的原有权限
		if ($arr_row)
		{
			$arr_role_menu = array();
			foreach ($arr_row as $v)
			{
				$arr_role_menu[] = array( // 数据结构
					'role_id'  => $target_frid, // 角色ID
					'menu_id'  => $v['menu_id'], // 菜单ID
				);
			}
			if (!empty($arr_role_menu))
			{
				//删除目标角色下的原有权限
				$roleMenu->delete(['role_id' => $target_frid]) ;
				//将新的权限复制给目标角色
				$roleMenu->insert($arr_role_menu); // 批量
			}
			$this->_result['ack'] = 1; // 设置返回状态&错误信息
		}
		$this->result();
		 
	}

	public function _updateRoleMenuAction()
	{
		$frid = $this->_req->getPost('role_id');
		$fmid = $this->_req->getPost('menu_id');
		if (!empty( $frid ))
		{
			$roleMenu = new RoleMenuModel();
			$rows = $roleMenu->select(['menu_id'], ['role_id' => $frid]) ; // 删除角色下的原有权限
			$curFmid = array();
			foreach ( $rows as $v )
			{
				$curFmid[] = $v['menu_id'];
			}
			$delFmid = array_diff($curFmid, $fmid); //删除
			$addFmid = array_diff($fmid, $curFmid); //添加
			if ($delFmid)
			{
				$roleMenu->delete(['AND' => ['role_id' => $frid, 'menu_id[IN]' => $delFmid]]) ; // 删除角色下的原有权限
			}
			if ($addFmid)
			{
				$datas = array();
				foreach ($addFmid as $v)
				{
					$datas[] = [
						'role_id' => $frid, // 角色ID
						'menu_id' => $v, // 菜单ID
					];
				}
				if (!empty( $datas))
				{
					$roleMenu->insert($datas); // 批量
				}
			}
			$this->_result['ack'] = 1;
		}
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