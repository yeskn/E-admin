<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-06-19
 * Time: 23:16
 */

namespace Eadmin\form\field;

use Eadmin\form\Field;
use Eadmin\tools\Data;

/**
 * 级联选择器
 * Class Cascader
 * @package Eadmin\form
 */
class Cascader extends Field
{

    protected $attrs = [
        'options',
        'props',
        'clearable',
        'disabled',
        'filterable',
    ];
    protected $props = [];
    protected $loadUrl = null;
    protected $optionHtml = "<el-cascader %s></el-cascader>";
    protected $relationMany = null;
    public function __construct($field, $label, $arguments = [])
    {
        parent::__construct($field, $label, $arguments);
        $this->setAttr('clearable', true);
        $this->props('expandTrigger','hover');
        $this->setAttr('placeholder', '请选择' . $label);
    }
    /**
     * panel样式
     */
    public function themePanel(){
        $this->optionHtml  = str_replace('el-cascader','el-cascader-panel',$this->optionHtml);
        return $this;
    }

    /**
     * 父子节点取消关联
     * @return $this
     */
    public function checkStrictly(){
        $this->props('checkStrictly',true);
        return $this;
    }
    /**
     * 联动加载
     * @param $url 请求url
     * @return $this
     */
    public function load($url){
        $this->loadUrl = $url;
        $this->themePanel();
        $this->props('lazy',true);
        $this->props('lazyLoad','lazyLoadMethod');
        return $this;
    }
    /**
     * 设置选项数据
     * @param array $datas
     */
    public function options(array $datas)
    {
        $options = Data::generateTree($datas,'value');
        $this->setAttr('options', $options);
        return $this;
    }

    /**
     * 配置选项
     * @param $attribute 属性
     * @param $value 值
     */
    public function props($attribute,$value){
        $this->props[$attribute] = $value;
        return $this;
    }
    public function getRelation(){
        return $this->relationMany;
    }
    /**
     * 多选
     * @param $relationMethod 一对多关联方法
     * @return $this
     */
    public function multiple($relationMethod=''){
        $this->relationMany = $relationMethod;
        $this->props('multiple',true);
        return $this;
    }
    public function render()
    {
        $this->setAttr('props',$this->props);
        $this->setAttr('filterable',true);
        list($attrStr, $tableScriptVar) = $this->parseAttr();
        $loadLevel = count($this->fields) - 1;
        $js = <<<EOT
        lazyLoad (node, resolve) {
            const level = node.level
            let value = 0
            if(node.value){
                value = node.value
            }
            _self.\$request({
                 url: '{$this->loadUrl}?value='+value,
             }).then(response=>{
                 if(response.code == 200){
                    const nodes = response.data
                    nodes.forEach(item => {
                        item.leaf = level >= {$loadLevel}
                    })
                    resolve(nodes)   
                 }
             })
            
        }
EOT;
        foreach ($this->scriptVar as &$val){
            $val = str_replace('"lazyLoad":','',$val);
            $val = str_replace('"lazyLoadMethod"',$js,$val);
        }
        $html = sprintf($this->optionHtml ,$attrStr);
        return $html;
    }
}
