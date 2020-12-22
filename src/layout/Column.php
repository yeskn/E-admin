<?php


namespace Eadmin\layout;


use Eadmin\facade\Component;
use Eadmin\form\Form;
use Eadmin\grid\Detail;
use Eadmin\grid\Grid;
use Eadmin\grid\Table;
use Eadmin\View;

class Column extends View
{
    protected $html = '';
    protected $span = 24;
    protected $component = [];
    protected $row = [];
    protected $clickLink = null;
    /**
     * 添加一行
     * @param $content
     */
    public function row($content){
        $row = new Row();
        if($content instanceof \Closure){
            call_user_func($content,$row);
        }else{
            $row->column($content);
        }
        $this->row[] = $row;
        return $row;
    }
    /**
     * 添加一行组件
     * @param $component 组件
     * @param $span 栅格占据的列数,默认24
     */
    public function rowComponent($component,$span = 24){
        $row = new Row();
        $row->columnComponent($component,$span);
        $this->row[] = $row;
        return $row;
    }
    /**
     * 添加一行组件
     * @param $url 组件url
     * @param $span 栅格占据的列数,默认24
     */
    public function rowComponentUrl($url,$span = 24){
        $row = new Row();
        $row->columnComponentUrl($url,$span);
        $this->row[] = $row;
        return $row;
    }
    public function getComponents(){
        return $this->component;
    }

    /**
     * 添加内容
     * @param $html
     */
    public function content($html){
        if($html instanceof Grid || $html instanceof Form || $html instanceof Detail || $html instanceof Table){
            $view = $html->view();
            $html = "<component :is=\"resolve => {
          resolve(this.\$splitCode(decodeURIComponent('".rawurlencode($view)."'),this.\$route.name))
        }\" v-bind=\"\$attrs\" v-on=\"\$listeners\"></component>";
        }
        $this->html = $html;
    }
    /**
     * 栅格占据的列数
     * @param $num 默认24
     */
    public function span($num = 24){
        $this->setAttr(':span',$num);
        if($num == 8 || $num == 6 || $num == 12){
            $this->setAttr(':xs',24);
            $this->setAttr(':sm',24);
            $this->setAttr(':md',$num);
        }
    }
    /**
     * 栅格左侧的间隔格数
     * @param $num
     */
    public function offset($num){
        $this->setAttr(':offset',$num);
    }
    /**
     * 栅格向右移动格数
     * @param $num 默认24
     */
    public function push($num){
        $this->setAttr(':push',$num);
    }
    /**
     * 栅格向左移动格数
     * @param $num
     */
    public function pull($num){
        $this->setAttr(':pull',$num);
    }

    /**
     * 点击切换组件
     * @param $url 链接
     * @param $name 组件名称标记
     */
    public function clickLink($url,$name){
        $this->clickLink = [$url,$name];
    }
    public function render(){

        foreach ($this->row as $row){
            $this->html .= $row->render();
            $this->component = array_merge($this->component,$row->getComponents());
        }
        list($attrStr, $scriptVar) = $this->parseAttr();
        if(!is_null($this->clickLink)){
            list($url,$name) = $this->clickLink;
            $this->html = "<span @click='linkComponent(\"{$url}\",\"{$name}\")'>{$this->html}</span>";
        }
        return "<el-col $attrStr>{$this->html}</el-col>";
    }
}
