<?php


namespace Eadmin\component\form;


use Eadmin\component\Component;
use think\helper\Str;

/**
 * Class Field
 * @package Eadmin\component\form
 * @method $this disabled(bool $vlaue=true) 禁用
 * @property FormItem $formItem
 */
abstract class Field extends Component
{
    protected $default = null;
    protected $value = null;
    protected $formItem;
    public function __construct($field = null, $value = '')
    {
        $this->bindValue($field, $value);
    }
    /**
     * 表单新增更新验证规则
     * @Author: rocky
     * 2019/8/9 10:50
     * @param array $rule 验证规则
     */
    public function rule(array $rule)
    {
        $this->formItem->rules($rule);
        return $this;
    }

    /**
     * 表单新增验证规则
     * @Author: rocky
     * 2019/8/9 10:50
     * @param array $rule 验证规则
     */
    public function createRule(array $rule)
    {
        $this->formItem->rules($rule,1);
        return $this;
    }

    /**
     * 表单更新验证规则
     * @Author: rocky
     * 2019/8/9 10:50
     * @param array $rule 验证规则
     */
    public function updateRule(array $rule)
    {
        $this->formItem->rules($rule,2);
        return $this;
    }
    /**
     * 是否必填
     * @return $this
     */
    public function required(){
        $this->formItem->required();
        return $this;
    }
    public function setFormItem(FormItem $formItem){
        $this->formItem = $formItem;
    }
    /**
     * 设置缺省默认值
     * @param mixed $value
     */
    public function default($value){
        $this->default = $value;
        return $this;
    }
    /**
     * 获取缺省默认值
     * @return |null
     */
    protected function getDefault(){
        return $this->default;
    }
    /**
     * 获取设置固定值
     * @return |null
     */
    protected function getValue(){
        return $this->value;
    }
    /**
     * 设置值
     * @param mixed $value
     */
    public function value($value){
        $this->value = $value;
        $field = $this->bindAttr('modelValue');
        $this->bind($field,$value);
        return $this;
    }
    /**
     * 创建
     * @param string $field 字段
     * @param mixed $value 值
     * @return static
     */
    public static function create($field = null, $value = '')
    {
        return new static($field, $value);
    }

    /**
     * 双向绑定值
     * @param string $field 字段
     * @param mixed $value 值
     */
    public function bindValue($field = null, $value = '')
    {
        empty($field) ? $field = Str::random(15, 3) : $field;
        $this->bind($field, $value);
        $this->bindAttr('modelValue',$field);
        return $this;
    }
}
