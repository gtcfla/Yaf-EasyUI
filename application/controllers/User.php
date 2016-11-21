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
	
	public function _queryAction()
	{
		$data = $options = $where = array();
		foreach (['id', 'realname', 'state', 'name', 'create_time'] as $field)
		{
			if (!empty($this->_req->getQuery($field))) $data[$field] = $this->_req->getQuery($field);
		}
		$result = $this->selectCommon(['id', 'realname', 'name', 'state', 'type', 'create_time'], $data, $this->user);
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
		
		if ($data) $this->_result['ack'] = $this->user->insert($data); // 设置返回状态&错误信息
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
}