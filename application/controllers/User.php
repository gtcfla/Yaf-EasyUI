<?php
class UserController extends BaseController
{
	public $_title = '用户管理';
	public function init()
	{
		parent::init();
		if ($this->_api) $this->user = new UserModel(); // 加载 实例化访问数据模型的对象
	}
	
	public function indexAction()
	{
		$this->view();
	}
	
	public function _loginAction()
	{
		$uname = $this->_req->getPost('uname');
		$pwd = $this->_req->getPost('pwd');
		try
		{
			if ($uname && $pwd)
			{
				$_user = $this->user->select(['id', 'role_id'], ['AND' => ['name' => $uname, 'password' => sha1(md5($pwd))]]);
				if ($_user[0])
				{
					$this->setLoginState($_user[0]);
					throw new Exception('登录成功!', 1);
				}
				else
				{
					throw new Exception('帐号或密码不正确!', 0);
				}
			}
			else
			{
				throw new Exception( '帐号或密码无效!', 0 );
			}
		}
		catch (Exception $e)
		{
			$this->_result['ack'] = $e->getCode();
			$this->_result['msg'] = $e->getMessage();
		}
		$this->result();
	}
	
	private function setLoginState($_user)
	{
		$_user['menutree'] = $_user['acl'] = $_user['ca'] = [];
	
		$roleMemuModel = new RoleMenuModel();
		$roleMenuList = $roleMemuModel->getRoleMenuPidList($_user['role_id']); // 找到角色ID的菜单权限
		if (!empty($roleMenuList))
		{
			foreach ($roleMenuList as $k => $v)
			{
				$arr_tmp[$v['id']] = $v;
			}
			foreach ($roleMenuList as $k => &$v)
			{
				$permission[trim(strtolower($v['controller'].'/'. $v['action'] ), '/')] = 1; // 设置权限
				if ( isset( $arr_tmp[$v['pid']] ) ) {
					$v['pname'] = $arr_tmp[$v['pid']]['name'];
				}
				$arr_ca[trim(strtolower($v['controller'].'/'. $v['action'] ), '/')] = $v;
				if (!$v['display']) unset($roleMenuList[$k]); // 设置显示菜单
			}
			$menu_list = array('menus' => array());
		}
// 		Util::session('_user', $_user);
	}
	
	public function _queryAction()
	{
		$data = $options = $where = array();
		foreach (['id', 'realname', 'state', 'name', 'create_time', 'role_id'] as $field)
		{
			if (!empty($this->_req->getQuery($field))) $data[$field] = $this->_req->getQuery($field);
		}
		$result = $this->selectCommon(['id','role_id', 'realname', 'name', 'state', 'type', 'create_time'], $data, $this->user);
		$roleModel = new RoleModel();
		$roleList = $roleModel->getRoleIdNameList();
		if ( !empty( $this->_result['data'] ) && is_array($this->_result['data']))
		{
			foreach( $this->_result['data'] as $k => $v )
			{
				$this->_result['data'][$k]['rolename'] = $roleList[$v['role_id']];
			}
		}
		$this->result();
	}
	
	public function _addAction()
	{
		foreach (['realname', 'password', 'name', 'type', 'state'] as $field)
		{
			if ($this->_req->getPost($field)) $data[$field] = $this->_req->getPost($field); //参数获取(post)
		}
		if (!empty( $data['password'])) {
			$data['password'] = SHA1(MD5($data['password']));
		}
		
		if ($data && $this->user->insert($data)) $this->_result['ack'] = 1; // 设置返回状态&错误信息
		$this->result();
	}
	
	public function _updateAction()
	{
		$id = $this->_req->getPost('id'); //参数获取(post)
		$data = array();
		foreach ( array('realname', 'name', 'type', 'state', 'password' ) as $field )
		{
			if (!empty($this->_req->getPost($field))) $data[$field] = $this->_req->getPost($field); //参数获取(post)
		}
		if ($data['password']) $data['password'] = SHA1(MD5($data['password']));
		if ( $id && $data)
		{
			$this->_result['ack'] = $this->user->update($data, ['id' => $id]);
			// 如果是修改本人的信息则更新session
// 			if ( isset( $data['FRID'] ) ) {
// 				$_user = SQ::session( '_USER' );
// 				$uid = $this->_req->getPost( 'ID' );
// 				$user_info = $this->user->getUserById( $uid );
// 				if ( $_user['UNAME'] == $user_info['UNAME'] && $_user['FRID'] != $data['FRID'] ) {
// 					$_user['FRID'] = $data['FRID'];
// 					$this->setLoginState( $_user );
// 				}
// 			}
		}
		$this->result();
	}
	
	public function _updatePassWordAction()
	{
		$oldPassword = $this->_req->getPost('oldPassword'); //原用户密码
		$newPassword = $this->_req->getPost('newPassword'); //新设密码
		$data = array();
		if (!empty($oldPassword) && !empty($newPassword))
		{
			$this->_result['ack'] = 1;
		}
		$this->result();
	}
}