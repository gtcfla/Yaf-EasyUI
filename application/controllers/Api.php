<?php
/**
 * Created by PhpStorm.
 * User: zen
 * Date: 2016/12/25
 * Time: 下午8:14
 */
use api\User;
class ApiController extends yaf\Controller_Abstract{
    public $_title;
    public function userAction(){
        echo User::add();
    }
}