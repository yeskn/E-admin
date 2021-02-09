<?php


namespace Eadmin\component\grid;


use Eadmin\component\basic\Html;
use Eadmin\component\basic\Tag;
use Eadmin\component\Component;
use Eadmin\component\form\field\Rate;
use Eadmin\component\form\field\Switchs;
use Eadmin\component\layout\Content;

/**
 * Class Column
 * @link https://element-plus.gitee.io/#/zh-CN/component/table
 * @package Eadmin\component\grid
 * @method $this dataIndex(string $value) 对应列内容的字段名
 * @method $this align(string $value)    left/center/right
 * @method $this headerAlign(string $value)    left/center/right
 * @method $this fixed(string $value) true, left, right
 * @method $this width(int $value) 对应列的宽度
 * @method $this ellipsis(bool $bool=true) 超过宽度将自动省略
 */
class Column extends Component
{
    protected $name = 'ElTableColumn';
    protected $prop;
    protected $closure = null;
    //内容颜射
    protected $usings = [];
    //映射标签颜色
    protected $tagColor = [];
    //映射标签颜色主题
    protected $tagTheme = 'light';
    protected $tag = null;
    protected $grid;
    protected $exportClosure = null;
    protected $exportData;
    public function __construct($prop, $label,$grid)
    {
        $this->attr('slots',['title'=>$prop,'customRender'=>'default']);
        if (!empty($prop)) {
            $this->prop = $prop;
            $this->prop($prop);
            $this->dataIndex($prop);
        }
        if (!empty($label)) {
            $this->label($label);
        }
        $this->grid = $grid;
    }
    /**
     * 评分显示
     * @param int $max 最大长度
     * @return $this
     */
    public function rate($max = 5)
    {
        $this->display(function ($val) use ($max) {
            return Rate::create(null,$val)->max($max)->disabled();
        });
        return $this;
    }

    /**
     * 自定义导出
     * @param \Closure $closure
     */
    public function export(\Closure $closure){
        $this->exportClosure = $closure;
        return $this;
    }
    /**
     * 关闭当前列导出
     * @return $this
     */
    public function closeExport(){
        $this->attr('closeExport',true);
        return $this;
    }
    /**
     * 开启排序
     * @return $this
     */
    public function sortable(){
        $this->attr('sorter',true);
        return $this;
    }
    /**
     * 标签显示
     * @param $color 标签颜色：success，info，warning，danger
     * @param $theme 主题：dark，light，plain
     * @param $size 尺寸:medium，small，mini
     */
    public function tag($color = '', $theme = 'dark', $size = 'mini')
    {
        $this->tag = Tag::create()->type($color)->size($size)->effect($theme);
        return $this;
    }
    public function getField(){
        return $this->prop;
    }
    public function getUsing(){
        return $this->usings;
    }
    /**
     * 获取当前列字段数据
     * @param $data 行数据
     * @return string
     */
    private function getData($data){
        $prop = $this->attr('prop');
        $fields = explode('.',$prop);
        foreach ($fields as $field){
            if (isset($data[$field])) {
                $data = $data[$field];
            } else {
                $data = null;
            }
        }

        return $data;
    }
    /**
     * 解析每行数据
     * @param $data 数据
     * @return Html
     */
    public function row($data)
    {
        //获取当前列字段数据
        $originValue = $this->getData($data);
        if(is_null($originValue)){
            //空默认占位符
            $value = '--';
        }else{
            $value = $originValue;
        }
        $this->exportData = $value;
        //映射内容颜色处理
        if (isset($this->tagColor[$value])) {
            $this->tag($this->tagColor[$value], $this->tagTheme);
        }
        //映射内容处理
        if (count($this->usings) > 0 && isset($this->usings[$value])) {
            $value = $this->usings[$value];
            $this->exportData = $value;
        }
        //是否显示标签
        if(!is_null($this->tag)){
            $value = $this->tag->content($value);
        }
        //自定义内容显示处理
        if (!is_null($this->closure)) {
            $value = call_user_func_array($this->closure, [$originValue, $data]);
            if(is_string($value)){
                $this->exportData = $value;
            }
        }
        //自定义导出
        if (!is_null($this->exportClosure)) {
            $value = call_user_func_array($this->exportClosure, [$originValue, $data]);
            $this->exportData = $value;
        }
        return Html::create()->content($value);
    }
    public function getExportData(){
        return $this->exportData;
    }
    /**
     * 显示的标题
     * @param string $label
     * @return $this
     */
    public function label(string $label)
    {
        $this->attr('label',$label);
        $this->attr('header', Html::create()->content($label));
        return $this;
    }



    /**
     * 内容映射
     * @param array $usings 映射内容
     * @param array $tagColor 标签颜色
     * @param tagTheme 标签颜色主题：dark，light，plain
     */
    public function using(array $usings, array $tagColor = [], $tagTheme = 'light')
    {
        $this->tagColor = $tagColor;
        $this->tagTheme = $tagTheme;
        $this->usings = $usings;
        return $this;
    }
    /**
     * switch开关
     * @param array $active 开启状态 [1=>'开启']
     * @param array $inactive 关闭状态 [0=>'关闭']
     */
    public function switch(array $active = [1 => '开启'] , array $inactive = [0 => '关闭'])
    {
        $this->display(function ($val, $data) use ($active, $inactive) {
            $params = $this->grid->getCallMethod();
            $params['eadmin_ids'] = [$data[$this->grid->drive()->getPk()]];
            $switch =  Switchs::create(null,$val)
                ->state($active, $inactive)
                ->url('/eadmin/batch.rest')
                ->field($this->prop)
                ->params($params);
            return $switch;

        });
        return $this;
    }
    /**
     * 追加前面
     * @param $append
     * @return Column
     */
    public function prepend($prepend){
        return $this->display(function ($val) use($prepend){
            return $prepend.$val;
        });
    }
    /**
     * 追加末尾
     * @param $append
     * @return Column
     */
    public function append($append){
        return $this->display(function ($val) use($append){
           return $val.$append;
        });
    }
    /**
     * 自定义显示
     * @param \Closure $closure
     * @return $this
     */
    public function display(\Closure $closure)
    {
        $this->closure = $closure;
        return $this;
    }
}
