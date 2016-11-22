<?php
class MenuModel extends Db_medoo
{
	public $table = 'y_menu'; // 表名
	
	public function __construct($pool = 'm0')
	{
		parent::__construct( Yaf_Registry::get( 'config' )->database->$pool->toArray() );
	}
	
	public function insertMenu($data)
	{
		if (!empty( $data['sort']))
		{
			return $this->insert($this->bNodeSortMenu($data));
		}
		return $this->insert($data);
	}
	
	public function getMenuTreeList()
	{
		return $this->query("SELECT id, pid, name as text, display, controller, action, sort FROM {$this->table} ORDER BY sort DESC;")->fetchAll(PDO::FETCH_ASSOC);
	}
	
	public function getMenu()
	{
		return $this->query("SELECT id, pid, name, CONCAT(controller, '/', action) AS url, sort FROM {$this->table} WHERE display=1 ORDER BY sort DESC;")->fetchAll(PDO::FETCH_ASSOC);
	}
	
	public function updateMenuById($data=[], $ids=[])
	{
		if (isset($data['pid'] ))
		{
			return $this->update($this->bNodeSortMenu($data), ['id' => $ids]);
		}
		return $this->update($data, ['id' => $ids]);
	}
	
	public function updateMenuList($data)
	{
		$new = [];
		foreach ($data as $k => $d)
		{
			list($new['controller'], $new['name']) = explode('_', $k);
			$new['action'] = 'index';
			$new['display'] = 1;
			$last_id = $this->insert($new) ?: $this->select('id', ['AND' => $new])[0]; // php7三元运算写法
			unset($d['index']); // 去掉index
			$menu = $this->select(['controller', 'action'], ["pid" => $last_id]);
			$old = $del = [];
			foreach ($menu as $m)
			{
				$old[] = $m['action'];
			}
			foreach ($d as $v)
			{
				$key = $new['controller'].'_'.$v;
				if (in_array($key, $old)) continue;
				$this->insert([
					'pid' => $last_id,
					'name' => $v,
					'controller' => $new['controller'],
					'action' => $v
				]);
			}
			// 删除不存在的CA
			$del = array_diff($old, $d);
			foreach ($del as $de)
			{
				$this->delete(['AND' => ['controller' => $new['controller'], 'action' => $de]]);
			}
		}
		return true;
	}
	
	private function bNodeSortMenu($data = [])
	{
		$rs = $this->select(["id", "pid", "display", "sort"], ["pid" => $data['pid']]);
		$refSort = ['new' => $data['sort'] + 0.1]; //输入的序号可能已经存在，+0.1是为了排序到已经存在的序号的后面，
		if (!empty( $rs ))
		{
			foreach ($rs as $val)
			{
				$refSort[$val['id']] = $val['sort'];
			}
		}
		asort($refSort); //排序数组
		foreach (array_keys( $refSort ) as $sort => $fid)
		{
			// 重置数组 key为sort，value为id
			$sort = $sort + 1; //整理之后的新序号
			if ($fid == 'new')
			{
				$data['sort'] = $sort;
				continue;
			}
			$this->update(["sort" => $sort], ["id" => $fid]);
		}
		return $data;
	}
}
