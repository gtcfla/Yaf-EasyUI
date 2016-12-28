<?php
class UserModel extends Db\Medoo
{
	public $table = 'y_user'; // 表名

	public function __construct($pool = 'm0')
	{
		parent::__construct(Yaf\Registry::get( 'config' )->database->$pool->toArray());
	}
}