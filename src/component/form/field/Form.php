<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2021-01-01
 * Time: 09:22
 */

namespace Eadmin\component\form\field;


use Eadmin\component\basic\TabPane;
use Eadmin\component\basic\Tabs;
use Eadmin\component\form\Field;
use Eadmin\component\layout\Row;

/**
 * 表单
 * Class Form
 * @link https://element-plus.gitee.io/#/zh-CN/component/form
 * @package Eadmin\component\form\field
 * @method $this submitUrl(string $sumbitUrl) 提交url
 * @method $this rules(array $value) 表单验证规则
 * @method $this size(string $value) 尺寸 medium / small / mini
 * @method $this labelPosition(string $value) 表单域标签的宽度 right/left/top
 * @method $this labelSuffix(string $value) 表单域标签的后缀
 * @method $this labelWidth(string $value) 表单域标签的宽度，例如 '50px'
 * @method $this inline(bool $value) 行内表单模式
 * @method $this hideRequiredAsterisk(bool $value) 是否显示必填字段的标签旁边的红色星号
 * @method $this showMessage(bool $value) 是否显示校验错误信息
 * @method $this inlineMessage(bool $value) 是否以行内形式展示校验信息
 * @method $this statusIcon(bool $value) 是否在输入框中显示校验结果反馈图标
 * @method $this validateOnRuleChange(bool $value) 是否在 rules 属性改变后立即触发一次验证
 */
class Form extends Field
{
    protected $name = 'EadminForm';
    protected $actions;
    protected $tab;

    //用于控制回调布局是否追加到from元素
    protected $itemBool = true;

    protected $itemComponent = [];
    protected $formItem = [];
    public function __construct($field = null, $data = [])
    {
        empty($field) ? $field = Str::random(10, 3) : $field;
        $this->bind($field, $data);
        $this->bindAttr('model',$field);
        $this->actions = new FormAction($this);
    }


    /**
     * 创建
     * @param string $field 字段
     * @param array $data 值
     * @return static
     */
    public static function create($field = 'form',$data = [])
    {
        return new static($field, $data);
    }
    /**
     * 添加内容
     * @param mixed $content
     * @param string $name 插槽名称默认即可default
     * @return static
     */
    public function content($content,$name='default')
    {
        if($content instanceof Field && $content->bindAttr('modelValue')){
            $field = $content->bindAttr('modelValue');
            $value = $content->bind($field);
            $content->removeBind($field);
            $content->bindAttr('modelValue', $this->bindAttr('model') . '.' . $field);
            $this->bind[$this->bindAttr('model')][$field] = $value;
        }
        return parent::content($content,$name);
    }
    /**
     * 选项卡布局
     * @param $title 标题
     * @param \Closure $closure
     * @return $this
     */
    public function tab($title,\Closure $closure){
        if(!$this->tab){
            $this->tab = Tabs::create();
            $this->content($this->tab);
        }
        $content = $this->content['default'];
        $this->content['default'] = [];
        call_user_func_array($closure,[$this,$this->tab]);
        $tabPane = new TabPane();
        $tabPane->label($title);
        foreach ($this->content['default'] as $slot){
            $tabPane->content($slot);
        }
        $this->content['default'] = $content;
        $this->tab->content($tabPane);
        return $this;
    }
    /**
     * 添加item
     * @param string $prop 字段
     * @param string $label 标签
     * @return FormItem
     */
    public function item($prop='',$label=''){
        $item = FormItem::create($prop,$label,$this);
        if($this->itemBool){
            $this->content($item);
        }
        $this->formItem[] = $item;
        return $item;
    }
    public function getFormItem(){
        return $this->formItem;
    }
    /**
     * 添加一行布局
     * @param $content
     * @return $this
     */
    public function row($content){
        $row = new Row();
        if ($content instanceof \Closure) {
            $this->itemBool = false;
            call_user_func($content, $row);
            $this->itemBool = true;
        } else {
            $row->column($content, $span);
        }
        $this->content($row);
        return $this;
    }
    /**
     * 一对多添加
     * @param string $field 字段
     * @param string $title 标题
     * @param array $data 数据
     * @return FormMany
     */
    public function manyItem($field,$title='',array $data = []){
        $this->formItem = [];
        $many =  FormMany::create($field,$data);
        $many->attr('field',$field);
        $many->attr('title',$title);
        if($this->itemBool){
            $this->content($many);
        }
        return $many;
    }

    /**
     * 用于控制回调布局是否追加到from元素
     * @param bool $bool
     */
    public function itemBool($bool = null){
        if(!is_null($bool)){
            $this->itemBool = $bool;
        }
        return $this->itemBool;
    }
    /**
     * 表单操作定义
     * @param \Closure $closure
     */
    public function actions(\Closure $closure){
        call_user_func_array($closure,[$this->actions]);
    }
    public function setItemComponent($component){
        $this->itemComponent[] = $component;
    }
    public function itemComponent($component){
        $this->itemComponent = $component;
    }
    public function getItemComponent(){
        return $this->itemComponent;
    }
    /**
     * 解析组件
     */
    protected function parseComponent(){
        foreach ($this->itemComponent as $component){
            //各个组件绑定值赋值
            $field = $component->bindAttr('modelValue');
            $value = $component->bind($field);
            $component->removeBind($field);
            $component->bindAttr('modelValue', $this->bindAttr('model') . '.' . $field);
            $this->bind[$this->bindAttr('model')][$field] = $value;
        }
    }
    public function jsonSerialize()
    {
        $this->parseComponent();
        $this->actions->render();
        return parent::jsonSerialize(); // TODO: Change the autogenerated stub
    }
}
