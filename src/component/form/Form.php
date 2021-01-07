<?php


namespace Eadmin\component\form;


/**
 * Class Form
 * @package Eadmin\component\form
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
 * @property \Eadmin\component\form\field\Form $form
 */
class Form implements \JsonSerializable
{
    protected $form;

    public function __construct()
    {
        $this->form = \Eadmin\component\form\field\Form::create();
        $this->form->labelWidth('100px');
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
            $this->form->content($component);
        } else {
            $this->form->item($field, $label)->content($component);
        }
        return $component;
    }

    /**
     * 一对多添加
     * @param $realtion 关联方法|字段
     * @param $title 标题
     * @param \Closure $closure
     */
    public function hasMany($realtion,$title,\Closure $closure){
        $manyItem = $this->form->manyItem($realtion,$title,[['text'=>1]]);
        $originItemComponent = $this->form->getItemComponent();
        $this->form->itemComponent([]);
        $this->form->itemBool(false);
        call_user_func_array($closure,[$this]);
        $this->form->itemBool(true);
        $itemComponent = $this->form->getItemComponent();
        $fieldValues = [];
        foreach ($itemComponent as $component){
            $field = $component->bindAttr('modelValue');
            $value = $component->bind($field);
            $fieldValues[$field] = $value;
        }
        $manyItem->value($fieldValues);
        $this->form->itemComponent($originItemComponent);
        foreach ($this->form->getFormItem() as $item){
            $manyItem->content($item);
        }
    }
    public function __call($name, $arguments)
    {
        if(method_exists($this->form,$name)){
            return call_user_func_array([$this->form, $name], $arguments);
        }else{
            return $this->formItem($name, $arguments[0], array_slice($arguments, 1));
        }
    }
    public function jsonSerialize()
    {
        return $this->form;
    }
}
