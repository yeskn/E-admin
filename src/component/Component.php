<?php

namespace Eadmin\component;

use think\helper\Str;

abstract class Component implements \JsonSerializable
{
    //组件名称
    protected $name;
    //属性
    protected $attribute = [];
    //内容
    protected $content = [];
    //是否是组件
    protected $isComponent = true;
    /**
     * 设置属性
     * @param string $name 属性名
     * @param string|bool $value 值
     * @return $this
     */
    public function attr(string $name, $value)
    {
        $this->attribute[$name] = $value;
        return $this;
    }

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

    public function __call($name, $arguments)
    {
        return $this->attr($name, ...$arguments);
    }

    protected function slot($content, $name = 'default')
    {
        $this->content[$name] = $content;
        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'name' => $this->name,
            'component' => $this->isComponent,
            'attribute' => $this->attribute,
            'content' => $this->content,
        ];
    }
}
