<?php


namespace Eadmin\component\form;


use Eadmin\component\Component;
use think\helper\Str;

abstract class Field extends Component
{
    /**
     * 双向绑定值
     * @param mexid $value 值
     * @param string $field 字段
     */
    public function value($value, $field = null)
    {
        $this->attr('modelValue', $value);
        empty($field) ? $field = Str::random(10, 3) : $field;
        $this->attr('modelField', $field);
    }
}
