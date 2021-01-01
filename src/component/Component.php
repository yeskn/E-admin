<?php

namespace Eadmin\component;

use think\helper\Str;

abstract class Component implements \JsonSerializable
{
    use Where;
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
     * @param null $value 值
     * @return $this|mixed
     */
    public function attr(string $name, $value = null)
    {
        if(is_null($value)) {
            return $this->attribute[$name];  
        }else{
            $this->attribute[$name] = $value;
            return $this;
        }
    }

    public function __call($name, $arguments)
    {
        return $this->attr($name, ...$arguments);
    }

    /**
     * 插槽内容
     * @param $content 内容
     * @param string $name 插槽名称
     * @return $this
     */
    public function content($content, $name = 'default')
    {
        $this->content[$name][] = $content;
        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'name' => $this->name,
            'where' => $this->where,
            'component' => $this->isComponent,
            'attribute' => $this->attribute,
            'content' => $this->content,
        ];
    }
}
