<?php
class UserModel extends Db_medoo
{
	public $table = 'z_user'; // 表名

	public function __construct($pool = 'm0')
	{
		parent::__construct( Yaf_Registry::get( 'config' )->database->$pool->toArray() );
	}
}