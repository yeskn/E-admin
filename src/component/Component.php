<?php

namespace Eadmin\component;

use Eadmin\Admin;
use Eadmin\component\basic\Dialog;
use Eadmin\component\basic\Drawer;
use Eadmin\component\layout\Column;
use Eadmin\form\Form;
use think\helper\Str;

abstract class Component implements \JsonSerializable
{
    use Where, ForMap;

    //组件名称
    protected $name;
    //属性
    protected $attribute = [];
    //内容
    protected $content = [];
    //绑定值
    protected $bind = [];
    //属性绑定
    protected $bindAttribute = [];
    //事件
    protected $event = [];

    /**
     * 设置属性
     * @param string $name 属性名
     * @param null $value 值
     * @return $this|mixed
     */
    public function attr(string $name, $value = null)
    {
        if (is_null($value)) {
            return $this->attribute[$name] ?? null;
        } else {
            $this->attribute[$name] = $value;
            return $this;
        }
    }

    public function removeAttr($name)
    {
        unset($this->attribute[$name]);
        return $this;
    }

    public function removeBind($name)
    {
        unset($this->bind[$name]);
    }

    public function removeAttrBind($name)
    {
        unset($this->bindAttribute[$name]);
    }

    /**
     * 绑定属性对应绑定字段
     * @param string $name 属性名称
     * @param string $field 绑定字段名称
     */
    public function bindAttr(string $name, $field = null)
    {
        if (is_null($field)) {
            return $this->bindAttribute[$name] ?? null;
        } else {
            $this->bindAttribute[$name] = $field;
            return $this;
        }
    }

    /**
     * 绑定值
     * @param string $name 字段名称
     * @param mixed $value 值
     * @return $this
     */
    public function bind(string $name, $value = null)
    {
        if (is_null($value)) {
            return $this->bind[$name] ?? null;
        } else {
            $this->bind[$name] = $value;
            return $this;
        }
    }

    /**
     * 绑定属性值
     * @param $name
     * @param $value
     * @return string
     */
    protected function bindAttValue($name, $value)
    {
        $field = Str::random(15, 3);
        $this->bind($field, $value);
        $this->bindAttr($name, $field);
        return $field;
    }

    public function __call($name, $arguments)
    {
        if (empty($arguments)) {
            return $this->attr($name, true);
        } else {
            return $this->attr($name, ...$arguments);
        }

    }

    public function event($name, array $value)
    {
        $name = ucfirst($name);
        if (isset($this->event[$name])) {
            $this->event[$name] = array_merge($this->event[$name], $value);
        } else {
            $this->event[$name] = $value;
        }
        return $this;
    }
    /**
     * 插槽内容
     * @param mixed $content 内容
     * @param string $name 插槽名称
     * @return $this
     */
    public function content($content, $name = 'default')
    {
        if(is_null($content)){
            return $this;
        }
        if(!($content instanceof Component)){
            $content = Admin::dispatch($content);
        }
        if($content instanceof Form && ($this instanceof Dialog || $this instanceof Drawer)){
            $field = $this->bindAttr('modelValue');
            $content->eventSuccess([$field=>false]);
        }
        $this->content[$name][] = $content;
        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'name' => $this->name,
            'where' => $this->where,
            'map' => $this->map,
            'bind' => $this->bind,
            'attribute' => $this->attribute,
            'bindAttribute' => $this->bindAttribute,
            'content' => $this->content,
            'event' => $this->event
        ];
    }
}
