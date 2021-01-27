<?php


namespace Eadmin\component\form\field;


use Eadmin\component\form\Field;

/**
 * 开关
 * Class Switchs
 * @link https://element-plus.gitee.io/#/zh-CN/component/switch
 * @method $this loading(bool $value=true) 是否显示加载中
 * @method $this width(int $num) switch 的宽度（像素）
 * @method $this activeIconClass(string $class) switch 打开时所显示图标的类名，设置此项会忽略 active-text
 * @method $this inactiveIconClass(string $class) switch 关闭时所显示图标的类名，设置此项会忽略 inactive-text
 * @method $this activeText(string $text) switch 打开时的文字描述
 * @method $this inactiveText(string $text) switch 关闭时的文字描述
 * @method $this activeValue($value) switch 打开时的值
 * @method $this inactiveValue($value) switch 关闭时的值
 * @method $this activeColor(string $value) switch 打开时的背景色
 * @method $this inactiveColor(string $value) switch 关闭时的背景色
 * @method $this validateEvent(bool $value=true) 改变 switch 状态时是否触发表单的校验
 * @method $this url(string $value) 请求url
 * @method $this params(array $value)  请求数据
 * @method $this field(string $value)  请求值字段名称
 * @package Eadmin\component\form\field
 */
class Switchs extends Field
{
    protected $name = 'EadminSwitch';
    public function __construct($field = null, $value = '')
    {
        parent::__construct($field, $value);
        $this->state([1 => ''], [0 => '']);
    }

    /**
     * 设置状态
     * @param array $active 开启状态 [1=>'开启']
     * @param array $inactive 关闭状态 [0=>'关闭]
     * @return $this
     */
    public function state(array $active, array $inactive)
    {
        $activeText = current($active);
        $inactiveText = current($inactive);
        $this->activeText(current($active));
        $this->activeValue(key($active));
        $this->inactiveText(current($inactive));
        $this->inactiveValue(key($inactive));
        return $this;
    }
}
