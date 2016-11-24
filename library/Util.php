<?php

class Util
{
	/**
	 * 取得目录下及所有子孙目录的文件路径，并且以$dir参数为根目录名
	 *
	 * @param String $dir 路径名
	 * @param String $filter 正则表达式，过滤掉文件名不匹配该表达式的文件
	 * @return array
	 */
	public static function directoryMap($dir, $filter = null)
	{
		
		$dirpath = realpath($dir);
		
		$arr_file = [];
		$filenames = scandir($dir, 1);
		
		for ($i = 0, $count = count($filenames); $i < $count; $i++)
		{
			$filename = $filenames[$i];
			if ($filename == '.' || $filename == '..' || $filename == '.svn' || ( !empty($filter) && preg_match($filter, $filename))) continue;
		
			$file = $dirpath . DIRECTORY_SEPARATOR . $filename;
			if (is_dir($file))
			{
				$arr_file = array_merge( $arr_file, self::directory_map($file));
			}
			else
			{
				$arr_file[] = $file;
			}
		}
		return $arr_file;
	}
	
	/**
	 * 无极分类
	 * @param array $items
	 * @return return_type
	 * @author Mr.Z <gtcfla@gmail.com> 2016年11月16日
	 */
	public static function getTree($items)
	{
		$tree = array();
	    foreach($items as $item)
	    {
	        if(isset($items[$item['pid']]))
	        {
	        	$items[$item['pid']]['children'][] = &$items[$item['id']];
	        }
	        else
	        {
	        	$tree[] = &$items[$item['id']];
	        }
	    }
	    return $tree;
	}
	
	/**
	 * 递归把树形数组按指定字段进行排序
	 * author zsw
	 *
	 * @param array $menuTree 要排序树形的数组
	 * @param array $field 排序用到的字段 id：每组数据中的唯一值，children：下层数组，sort：排序的字段
	 * @return array
	 */
	static function treeArySort( &$menuTree = array(), $field = array( 'id' => '', 'children' => '', 'sort' => '' ) )
	{
		$sortTmp = $valTmp = $tmp = array();
		$children = $field['children'];
		$id = $field['id'];
		$sort = $field['sort'];
		foreach( $menuTree as $key => &$value ) {
			if ( isset( $value[$children] ) && is_array( $value[$children] ) && !empty( $value[$children] ) ) {
				self::treeArySort( $value[$children], $field );
			}
			$sortTmp[$value[$id]] = ( int )$value[$sort];
			$valTmp[$value[$id]] = $value;
		}
		asort( $sortTmp );
		foreach( $sortTmp as $dataId => $children ) {
			$tmp[] = $valTmp[$dataId];
		}
		$menuTree = $tmp;
	}
	
	/**
	 * SESSION 操作(读/写)
	 *
	 * @param  $ <type> $var1 读写键值
	 * @param  $ <type> $var2 写入值
	 * @return <type>
	 */
	static function session()
	{
		isset($_SESSION) || session_start();
		$numargs = func_num_args();
		if (1 === $numargs)
		{ // get
			return isset($_SESSION[func_get_arg(0)]) ? $_SESSION[func_get_arg(0)] : null;
		}
		if (2 <= $numargs)
		{ // set
			$_SESSION[func_get_arg(0)] = func_get_arg(1);
			session_commit();
		}
	}
}