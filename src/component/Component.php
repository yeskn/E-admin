<?php

namespace Eadmin\component;

abstract class Component implements \JsonSerializable
{
    //组件名称
    protected $name;
    //属性
    protected $attribute = [];
    //内容
    protected $content;

    /**
     * 设置属性
     * @param string $name 属性名
     * @param string|bool $value 值
     * @return $this
     */
    public function attr(string $name,$value){
        $this->attribute[$name] = $value;
        return $this;
    }
    public function __call($name, $arguments)
    {
        return $this->attr($name,$arguments);
    }
    public function jsonSerialize(){
        return [
            'name' => $this->name,
            'attribute' => $this->attribute,
            'content' => $this->content,
        ];
    }
}
