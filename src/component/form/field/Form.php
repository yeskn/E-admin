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
 * @method \Eadmin\component\form\field\Input text($field, $label) 文本输入框
 * @method \Eadmin\component\form\field\Input hidden($field) 隐藏输入框
 * @method \Eadmin\component\form\field\Input textarea($field, $label) 多行文本输入框
 * @method \Eadmin\component\form\field\Input password($field, $label) 密码输入框
 * @method \Eadmin\component\form\field\Number number($field, $label) 数字输入框
 * @method \Eadmin\component\form\field\Select select($field, $label) 下拉选择器
 * @method \Eadmin\component\form\field\RadioGroup radio($field, $label) 单选框
 * @method \Eadmin\component\form\field\CheckboxGroup checkbox($field, $label) 多选框
 * @method \Eadmin\component\form\field\Switchs switch ($field, $label) switch开关
 * @method \Eadmin\component\form\field\DatePicker datetime($field, $label) 日期时间
 * @method \Eadmin\component\form\field\DatePicker datetimeRange($startFiled, $endField, $label) 日期时间范围时间
 * @method \Eadmin\component\form\field\DatePicker dateRange($startFiled, $endField, $label) 日期范围时间
 * @method \Eadmin\component\form\field\DatePicker date($field, $label) 日期
 * @method \Eadmin\component\form\field\DatePicker dates($field, $label) 多选日期
 * @method \Eadmin\component\form\field\DatePicker year($field, $label) 年
 * @method \Eadmin\component\form\field\DatePicker month($field, $label) 月
 * @method \Eadmin\component\form\field\TimePicker timeRange($startFiled, $endField, $label) 日期范围时间
 * @method \Eadmin\component\form\field\TimePicker time($field, $label) 时间
 * @method \Eadmin\component\form\field\Slider slider($field, $label) 滑块
 * @method \Eadmin\component\form\field\Color color($field, $label) 颜色选择器
 * @method \Eadmin\component\form\field\Rate rate($field, $label) 评分组件
 * @method \Eadmin\component\form\field\Tree tree($field, $label) 树形
 * @method \Eadmin\component\form\field\File file($field, $label) 文件上传
 * @method \Eadmin\component\form\field\File image($field, $label) 图片上传
 * @method \Eadmin\component\form\field\Editor editor($field, $label) 富文本编辑器
 * @method \Eadmin\component\form\field\Cascader cascader(...$field, $label) 级联选择器
 * @method \Eadmin\component\form\field\Transfer transfer($field, $label) 穿梭框
 * @method \Eadmin\component\form\field\Icon icon($field, $label) 图标选择器
 * @method \Eadmin\component\form\field\IframeTag iframeTag($field, $label) 弹窗选择框
 * @method \Eadmin\component\form\field\Map map($lng, $lat, $address, $label) 高德地图
 * @method \Eadmin\component\form\field\TableText tableText($field, $label) 表格编辑
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
        $this->labelWidth('100px');
    }

    /**
     * 表单验证规则
     * @param array $value
     * @return $this
     */
    public function rules(array $value){
        $this->attr(__FUNCTION__,$bool);
        return $this;
    }
    /**
     * 是否在输入框中显示校验结果反馈图标
     * @param bool $value
     * @return $this
     */
    public function statusIcon(bool $value){
        $this->attr(__FUNCTION__,$bool);
        return $this;
    }
    /**
     * 是否显示必填字段的标签旁边的红色星号
     * @param bool $value
     * @return $this
     */
    public function hideRequiredAsterisk(bool $value){
        $this->attr(__FUNCTION__,$bool);
        return $this;
    }
    /**
     * 行内表单模式
     * @param bool $bool
     * @return $this
     */
    public function inline(bool $bool){
        $this->attr(__FUNCTION__,$bool);
        return $this;
    }
    /**
     * 表单域标签的后缀
     * @param string $value
     * @return $this
     */
    public function labelSuffix(string $value){
        $this->attr(__FUNCTION__,$value);
        return $this;
    }
    /**
     * 表单域标签的宽度
     * @param string $value 例如 '50px'
     * @return $this
     */
    public function labelWidth(string $value){
        $this->attr(__FUNCTION__,$value);
        return $this;
    }
    /**
     * 表单域标签的宽度
     * @return $this right/left/top
     */
    public function labelPosition(){
        $this->attr(__FUNCTION__,$value);
        return $this;
    }
    /**
     * 尺寸
     * @param string $value medium / small / mini
     * @return $this
     */
    public function size(string $value){
        $this->attr(__FUNCTION__,$value);
        return $this;
    }

    /**
     * 设置提交url
     * @param string $sumbitUrl 提交url
     * @return $this
     */
    public function submitUrl(string $sumbitUrl){
        $this->attr(__FUNCTION__,$sumbitUrl);
        return $this;
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
            $this->valueModel($content);
        }
        return parent::content($content,$name);
    }

    /**
     * 绑定值到form
     * @param $component 组件
     */
    private function valueModel($component){
        $field = $component->bindAttr('modelValue');
        $value = $component->bind($field);
        $component->removeBind($field);
        $component->bindAttr('modelValue', $this->bindAttr('model') . '.' . $field);
        $this->bind[$this->bindAttr('model')][$field] = $value;
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
        $this->formItem = [];
        $this->itemBool = false;
        call_user_func_array($closure,[$this,$this->tab]);
        $this->itemBool = true;
        $tabPane = new TabPane();
        $tabPane->label($title);
        foreach ($this->formItem as $item){
            $tabPane->content($item);
        }
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

    /**
     * 列布局
     * @param int $span  栅格占据的列数,占满一行24
     * @param \Closure $closure
     * @return $this
     */
    public function column(int $span,\Closure $closure){
        $this->formItem = [];
        $row = new Row();
        $this->itemBool = false;
        call_user_func($closure, $this);
        $this->itemBool = true;
        foreach ($this->formItem as $item){
            $row->column($item,$span);
        }
        $this->content($row);
        return $this;
    }
    /**
     * 添加一行布局
     * @param string $title
     * @param \Closure $closure
     * @return $this
     */
    public function row(string $title,\Closure $closure){
        $this->formItem = [];
        $row = new Row();
        $this->itemBool = false;
        call_user_func($closure, $this);
        $this->itemBool = true;
        $row->content("<h4 style='font-size:16px;color: #666666'>{$title}</h4>");
        foreach ($this->formItem as $item){
            $row->column($item);
        }
        $this->content($row);
        return $this;
    }
    /**
     * 一对多添加
     * @param $realtion 关联方法|字段
     * @param $title 标题
     * @param \Closure $closure
     */
    public function hasMany($realtion,$title,\Closure $closure){
        $this->formItem = [];
        $manyItem =  FormMany::create($realtion,[]);
        $manyItem->attr('field',$realtion);
        $manyItem->attr('title',$title);
        $originItemComponent = $this->itemComponent;
        $this->itemComponent = [];
        $this->itemBool = false;
        call_user_func_array($closure,[$this]);
        $this->itemBool = true;
        $itemComponent = $this->itemComponent;
        $fieldValues = [];
        foreach ($itemComponent as $component){
            $field = $component->bindAttr('modelValue');
            $value = $component->getValue();
            if(!is_null($value)){
                $fieldValues[$field] = $value;
            }
        }
        $data = [['aa'=>123,'bb'=>1],['aa'=>'3','bb'=>1]];
        foreach ($data as &$val){
            $val = array_merge($val,$fieldValues);
        }
        $manyItem->value($data);
        $this->itemComponent = $originItemComponent;
        foreach ($this->formItem as $item){
            $manyItem->content($item);
        }
        $this->content($manyItem);
        return $manyItem;
    }
   
    protected function formItem($name, $field, $arguments)
    {
        $label = array_pop($arguments);
        $label = $label ?? '';
        $class = "Eadmin\\component\\form\\field\\";
        $inputs = [
            'text',
            'textarea',
            'password',
            'hidden',
        ];
        $dates = [
            'date',
            'dates',
            'year',
            'month',
            'datetime',
            'datetimeRange',
            'dateRange',
        ];
        $times = [
            'time',
            'timeRange',
        ];
        if (in_array($name, $inputs)) {
            $class .= 'Input';
        } elseif (in_array($name, $dates)) {
            $class .= 'DatePicker';
        }  elseif (in_array($name, $times)) {
            $class .= 'TimePicker';
        }elseif ($name == 'radio') {
            $class .= 'RadioGroup';
        } elseif ($name == 'checkbox') {
            $class .= 'CheckboxGroup';
        } elseif ($name == 'switch') {
            $class .= 'Switchs';
        }else {
            $class .= ucfirst($name);
        }
        $component = $class::create($field);
        $compenentArr = array_merge($inputs,$dates,$times);
        if (in_array($name, $compenentArr)) {
            $component->type($name);
        }
        if ($name == 'hidden') {
            $this->content($component);
        } else {
            $this->item($field, $label)->content($component);
        }
        return $component;
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
    public function __call($name, $arguments)
    {
        return $this->formItem($name, $arguments[0], array_slice($arguments, 1));
    }
    /**
     * 解析组件
     */
    protected function parseComponent(){
        foreach ($this->itemComponent as $component){
            //各个组件绑定值赋值
            $this->valueModel($component);
        }
    }
    public function jsonSerialize()
    {
        $this->parseComponent();
        $this->actions->render();
        return parent::jsonSerialize(); // TODO: Change the autogenerated stub
    }
}
