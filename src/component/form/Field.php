<?php


namespace Eadmin\component\form;


use Eadmin\component\Component;
use think\helper\Str;

/**
 * Class Field
 * @method $this disabled(bool $disabled) 是否禁用
 * @package Eadmin\component\form
 */
abstract class Field extends Component
{
    /**
     * 双向绑定值
     * @param mixed $value 值
     * @param string $field 字段
     */
    public function value($value, $field = null)
    {
        $this->attr('modelValue', $value);
        empty($field) ? $field = Str::random(10, 3) : $field;
        $this->attr('modelField', $field);
    }
}
