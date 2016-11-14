<?php

class MenuModel {
	public $_pk = 'ID'; // 主键
	public $_table = 'adm_menu'; // 表名
	public function __construct( $pool = 'm0' )
	{
		echo 123;
	} 

	public static function buildWhere( $data = array() )
	{
		$where = array();
		foreach ( $data as $field => $value ) {
			switch ( $field ) {
				default:
					$where[$field] = array( 'eq', $value );
			} 
		} 
		return $where;
	} 
} 
