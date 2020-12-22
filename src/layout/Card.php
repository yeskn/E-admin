<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-06-22
 * Time: 21:15
 */

namespace Eadmin\layout;


use Eadmin\View;

/**
 * 卡片
 * Class Card
 * @package Eadmin\layout
 */
class Card extends View
{
    protected $html = '';
    /**
     * 头部
     * @param $html
     */
    public function header($html){
        $this->html = "<div slot='header'>{$html}</div>" . $this->html;
        return $this;
    }

    /**
     * 鼠标悬浮时显示阴影
     */
    public function hover(){
        $this->setAttr('shadow','hover');
        return $this;
    }

    /**
     * 从不显示显示阴影
     */
    public function never(){
        $this->setAttr('shadow','never');
        return $this;
    }

    /**
     * 总是显示阴影
     */
    public function always(){
        $this->setAttr('shadow','always');
        return $this;
    }
    /**
     * 内容区
     * @param $html
     */
    public function body($html){
        $this->html .= $html;
        return $this;
    }
   
    public function render(){
        list($attrStr, $scriptVar) = $this->parseAttr();
        return "<el-card $attrStr>{$this->html}</el-card>";
    }
}
