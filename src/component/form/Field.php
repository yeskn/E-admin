<?php


namespace Eadmin\component\form;


use Eadmin\component\Component;
use think\helper\Str;

abstract class Field extends Component
{
    protected $default = null;
    protected $value = null;
    public function __construct($field = null, $value = '')
    {
        $this->bindValue($field, $value);
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
     * 获取设置固定值
     * @return |null
     */
    public function getValue(){
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
        empty($field) ? $field = Str::random(10, 3) : $field;
        $this->bind($field, $value);
        $this->bindAttr('modelValue',$field);
        return $this;
    }
}
