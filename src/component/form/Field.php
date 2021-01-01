<?php


namespace Eadmin\component\form;


use Eadmin\component\Component;
use think\helper\Str;

abstract class Field extends Component
{
    public function __construct($field = null, $value = '')
    {
        $this->value($field, $value);
    }

    /**
     * 创建
     * @param string $field 字段
     * @param mexid $value 值
     * @return static
     */
    public static function create($field = null, $value = '')
    {
        return new static($field, $value);
    }

    /**
     * 双向绑定值
     * @param string $field 字段
     * @param mexid $value 值
     */
    public function value($field = null, $value = '')
    {
        empty($field) ? $field = Str::random(10, 3) : $field;
        $this->bind($field, $value);
        $this->bindAttr('modelValue',$field);
    }
}
