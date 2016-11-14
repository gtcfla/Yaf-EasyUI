<?php

class Util
{
	/**
	 * 取得目录下及所有子孙目录的文件路径，并且以$dir参数为根目录名
	 *
	 * @author liaofuqian
	 * @param String $dir 路径名
	 * @param String $filter 正则表达式，过滤掉文件名不匹配该表达式的文件
	 * @return array
	 */
	public static function directory_map($dir, $filter = null)
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
}