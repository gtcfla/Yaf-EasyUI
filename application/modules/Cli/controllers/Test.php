<?php
class TestController extends CliController{
    public function init(){
        parent::init();
    }
    /**
     * 运行测试程序
     */
    public function runAction(){
        $params = $this->getRequest()->getParams();
        $name = isset($params['name'])?$params['name']:'';
        if(!$name) exit('Enter your name.'.PHP_EOL);
        echo date('Y-m-d H:i:s').': Hello '.$name.PHP_EOL;
        exit;
    }
}
