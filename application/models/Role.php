<?php
class RoleModel extends Db_medoo
{
	public $table = 'y_role'; // 表名

	public function __construct($pool = 'm0')
	{
		parent::__construct( Yaf_Registry::get( 'config' )->database->$pool->toArray() );
	}
}