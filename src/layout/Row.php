<?php


namespace Eadmin\layout;


use Eadmin\View;

class Row extends View
{
    protected $html = '';
    protected $gutter = 0;
    protected $component = [];
    protected $column = [];
    protected $clickLink = null;
    /**
     * 添加列
     * @param Closure|String $content 内容
     * @param $span 栅格占据的列数,占满一行24,默认24
     */
    public function column($content,$span = 24){
        $column = new Column();
        $column->span($span);
        if($content instanceof \Closure){
            call_user_func($content,$column);
        }else{
            $column->content($content);
        }
        $this->column[] = $column;
        return $column;

    }
    /**
     * 添加列组件
     * @param $component 组件
     * @param $span 栅格占据的列数,占满一行24,默认24
     * @param $name 组件名称标记
     */
    public function columnComponent($component,$span = 24,$name=''){
        $column = new Column();
        $column->span($span);
        if(empty($name)){
            $componentKey = 'component'.mt_rand(10000,99999);
        }else{
            $componentKey = $name;
        }

        $this->component[$componentKey] = "() => new Promise(resolve => {
                            resolve(this.\$splitCode(decodeURIComponent('".rawurlencode($component)."'),this.\$route.name))
                        })";
        $column->content('<component :is="'.$componentKey.'" />');
        $this->column[] = $column;
        return $column;
    }
    /**
     * 添加列组件
     * @param $url 组件
     * @param $span 栅格占据的列数,占满一行24,默认24
     * @param $name 组件名称标记
     */
    public function columnComponentUrl($url,$span = 24,$name=''){
        $column = new Column();
        $column->span($span);
        if(empty($name)){
            $componentKey = 'component'.mt_rand(10000,99999);
        }else{
            $componentKey = $name;
        }
        $component = "<template><div></div></template>";
        $this->component[$componentKey] = "() => new Promise(resolve => {
                            resolve(this.\$splitCode(decodeURIComponent('".rawurlencode($component)."'),this.\$route.name))
                            this.\$request({
                                url: '{$url}',
                                params:{
                                    eadmin_component:true
                                }
                            }).then(res=>{
                                    this.{$componentKey} = () => new Promise(resolve => {
                                        resolve(this.\$splitCode(res.data))
                                    })
                            })
                        })";
        $column->content('<component :is="'.$componentKey.'" />');
        $this->column[] = $column;
        return $column;
    }
    public function getComponents(){
        return $this->component;
    }
    /**
     * flex布局
     * @param $justify flex 布局下的水平排列方式
     * @param $align flex 布局下的垂直排列方式
     */
    public function flex($justify,$align){
        $this->setAttr(':type','flex');
        $this->setAttr(':justify',$justify);
        $this->setAttr(':align',$align);
    }
    /**
     * 设置栅格间隔
     * @param $number
     */
    public function gutter($number){
        $this->setAttr(':gutter',$number);
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
        foreach ($this->column as $column){
            $this->html .= $column->render();
            $this->component = array_merge($this->component,$column->getComponents());
        }
        list($attrStr, $scriptVar) = $this->parseAttr();
        if(!is_null($this->clickLink)){
            list($url,$name) = $this->clickLink;
            $this->html = "<span @click='linkComponent(\"{$url}\",\"{$name}\")'>{$this->html}</span>";
        }
        return "<el-row $attrStr>{$this->html}</el-row>";
    }
}
