<?php
/**
 * Created by PhpStorm.
 * User: zen
 * Date: 2016/12/24
 * Time: 下午5:25
 */
class CliController extends Yaf\Controller_Abstract{
    protected $logFile=null;

    protected $execTime = 0;

    public function init(){
        if(!$this->getRequest()->isCli()){
            exit('forbid accessing!');
        }
        $this->execTime = time();
    }

    /**
     * 显示错误
     */
    protected  function displayError(){
        ini_set('display_errors', 1);
        error_reporting(E_ALL);
    }
    /**
     * 设置内存使用限制
     * @param  $size
     */
    protected function setMemoryLimit($size){
        ini_set('memory_limit', $size);
    }
    /**
     * 设置运行时间限制
     * @param  $seconds
     */
    protected function setTimeLimt($seconds){
        set_time_limit($seconds);
    }
}