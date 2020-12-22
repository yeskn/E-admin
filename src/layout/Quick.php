<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-06-22
 * Time: 21:49
 */

namespace Eadmin\layout;


use Eadmin\View;

/**
 * 快捷入口
 * Class Quick
 * @package Eadmin\layout
 */
class Quick extends View
{
    public function create($title,$icon,$iconColor=''){
        $this->html = "<div style='display: flex;height: 100px;justify-content: center;flex-direction:column;align-items: center'>
<i class='$icon' style='font-size:32px;color: {$iconColor}'></i><div style='margin-top:10px;color: #515A6E;font-size: 14px;'>{$title}</div></div>";
        return $this;
    }
    public function href($url){
        $this->html = "<eadmin-link url='{$url}'>{$this->html}</eadmin-link>";
        return $this;
    }
    public function render()
    {
        return "<el-card shadow='hover' :body-style='{padding: \"0px\"}' style='cursor: pointer;'>{$this->html}</el-card>";
    }
}
