<?php
/*
|---------------------------------------------------------------
|  Copyright (c) 2016
|---------------------------------------------------------------
| 文件名称：全局公共函数
| 功能 :功能函数
| 作者：ZEN
| 联系：gtcfla@gmail.com
| 版本：V1.0
| 日期：2016/12/26
|---------------------------------------------------------------
*/

/**
 * 调试用输出格式化
 */
if (! function_exists('dump')) {
    function dump($var){
        if (ini_get('html_errors'))
        {
            $content = "<pre>\n";
            $content .= htmlspecialchars(print_r($var, true));
            $content .= "\n</pre>\n";
        }
        else
        {
            $content = "\n";
            $content .= print_r($var, true) . "\n";
        }
        echo $content;
        return null;
    }
}