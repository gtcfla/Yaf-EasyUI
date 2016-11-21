<?php

class BaseController extends Yaf_Controller_Abstract
{
	public $_config; //全局配置
	public $_title; //模块标题
	public $_view = null; //模板视图对象
	public $_api = false; //是否接口
	public $_req = null; //请求对象
	public $_c; //当前控制器
	public $_a; //当前操作
	public $_tpl = ''; //渲染指定模板
	public $m = []; // 模型对象数组
	public $_pfmt = 'json'; // 打印数据格式(json:json,dg:DataGrid)
	public $_result = ['ack' => 0, 'msg' => '', 'data' => [], 'total' => 0]; //返回数据格式
	public $_msg = [0 => '失败', 1 => '成功', 2 => '你没有此操作的权限,请联系管理员!', 3 => '你还未登录,请先登录!', 4 => '退出成功']; //错误码对应消息
	
	public function init()
	{
		$this->_config = Yaf_Registry::get('config'); // 获取全局的配置
		$this->_req = $this->getRequest();
		$this->_c = $this->_req->getControllerName();
		$this->_a = $this->_req->getActionName();
		if (preg_match('/^_/', $this->_a))
		{
			$this->_api = true;
		}
		else 
		{
			$this->_view = $this->getView();
		}
	}
	
	public function view()
	{
		$this->_view->assign('title', $this->_title ); // 设置标题
		$this->_view->display($this->_tpl ? $this->_tpl : strtolower($this->_c.'/'.$this->_a).'.phtml');
	}
	
	/**
	 * 客户端调试用输出格式化
	 * @param 变量 $var
	 * @return return_type
	 * @author Mr.Z <gtcfla@gmail.com> 2016年11月17日
	 */
	public static function dump($var)
	{
		if (ini_get('html_errors'))
		{
			$content = "<pre>\n";
			$content .= htmlspecialchars(print_r($var, true));
			$content .= "\n</pre>\n";
		}
		else
		{
			$content = "\n";
			$content .= print_r($var, true) . "\n";
		}
		echo $content;
		return null;
	}
	
	/**
	 * 接口通用类 用于查询接口使用的通用函数
	 *
	 * @param  $ <type> $defField
	 * @param  $ <type> $options
	 * @param  $ <type> $db
	 * @return <type>
	 */
	public function selectCommon($columns, $where=[], &$db = null )
	{
		$page = $this->_req->getQuery('page', 1); // 接收翻页行数
		$rows = $this->_req->getQuery('rows', 10); // 接收查询数据库条数
		$this->_result['total'] = $db->count($where);
		$where["LIMIT"] = [($page-1)*$rows, $rows];
		$this->_pfmt = 'dg';
		$this->_result['data'] = $db->select($columns, $where);
	}
	
	public function result()
	{
		header('Content-type: application/json');
		header('Connection: close');
		$this->_result['msg'] = $this->_msg[$this->_result['ack']];
		switch ( $this->_pfmt ) {
			case 'dg':
				$this->_PrintDG();
				break;
			case 'jd':
				$this->_PrintArray();
				break;
			default:
				$this->_PrintResult();
		}
	}
	
	private function _PrintArray()
	{
		$result = $this->_result['data'] ?: [];
		exit(json_encode($result));
	}
	
	private function _PrintResult()
	{
		$this->_result['msg'] ?: $this->_result['msg'][$this->_result['ack']];
		exit(json_encode($this->_result));
	}
	
	private function _PrintDG()
	{
		$result = $this->_result['data'] ?: [];
		$footer = $this->_result['footer'] ?: []; 
		$chart  = $this->_result['chart'] ?: '';
		$chart_avg  = $this->_result['chart_avg'] ?: '';
		exit( json_encode(['total' => $this->_result['total'], 'rows' => $result , 'footer' => $footer, 'chart'=> $chart, 'chart_avg'=>$chart_avg]));
	}
	
	
	public function __unset($name)
	{
		if (substr($name, -5) === 'Model' && isset($this->m[$name])) unset($this->m[$name]);
	}
	
	public function __get($name)
	{
		if (substr($name, -5) === 'Model')
		{
			if (isset($this->m[$name])) return $this->m[$name];
			if (class_exists($name)) return ($this->m[$name] = new $name);
		}
	}
	
	public function __destruct()
	{
// 		$this->log();
	}
	
	public function log()
	{
// 		Seaslog::info();
	}
}