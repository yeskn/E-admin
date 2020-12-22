<?php

namespace Eadmin\layout;
use Eadmin\View;

class Content extends View
{
    protected $html = '';
    protected $component = [];
    public function __construct()
    {
        $this->template = 'content';
    }

    /**
     * 标题
     * @param string $title
     */
    public function title(string $title){
        $this->setVar('title',$title);
        return $this;
    }
    public function body($content){
        $this->row($content);
        return $this;
    }
    /**
     * 添加一行
     * @param Closure|String  $content 内容
     * @param $span 栅格占据的列数,默认24
     */
    public function row($content,$span = 24){
        $row = new Row();
        if($content instanceof \Closure){
            call_user_func($content,$row);
        }else{

            $row->column($content,$span);
        }
        $this->html .= $row->render();
        $this->component = array_merge($this->component,$row->getComponents());
    }
    /**
     * 添加一行组件
     * @param $component 组件
     * @param $span 栅格占据的列数,默认24
     * @param $name 组件名称标记
     */
    public function rowComponent($component,$span = 24,$name=''){
        $row = new Row();
        $row->columnComponent($component,$span,$name);
        $this->html .= $row->render();
        $this->component = array_merge($this->component,$row->getComponents());
    }
    /**
     * 添加一行组件
     * @param $url 组件url
     * @param $span 栅格占据的列数,默认24
     * @param $name 组件名称标记
     */
    public function rowComponentUrl($url,$span = 24,$name=''){
        $row = new Row();
        $row->columnComponentUrl($url,$span,$name);
        $this->html .= $row->render();
        $this->component = array_merge($this->component,$row->getComponents());
    }
    public function view(){
        list($attrStr, $scriptVar) = $this->parseAttr();
        $scriptVar = '';
        foreach ($this->component as $key=>$value){
            $scriptVar .= "$key:$value,";
        }
        $this->setVar('scriptVar',$scriptVar);
        $this->setVar('html',$this->html);
        return $this->render();
    }
}
