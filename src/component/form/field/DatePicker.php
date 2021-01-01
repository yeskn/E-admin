<?php


namespace Eadmin\component\form\field;


use Eadmin\component\form\Field;

/**
 * 日期选择器
 * Class DatePicker
 * @link https://element-plus.gitee.io/#/zh-CN/component/date-picker
 * @method $this editable(bool $bool) 文本框可输入
 * @method $this readonly(bool $bool) 完全只读
 * @method $this clearable(bool $bool) 是否显示清除按钮
 * @method $this size(string $size) 输入框尺寸 	large / medium / small / mini
 * @method $this startPlaceholder(string $text) 范围选择时开始日期的占位内容
 * @method $this endPlaceholder(string $text) 范围选择时开始日期的占位内容
 * @method $this type(string $type) 显示类型 year / month / date / dates / week / datetime / datetimerange / daterange / monthrange
 * @method $this format(string $format) 显示在输入框中的格式 YYYY-MM-DD
 * @method $this align(string $align) 对齐方式 left / center / right
 * @method $this popperClass(string $align) DatePicker 下拉框的类名
 * @method $this rangeSeparator(string $align) 选择范围时的分隔符
 * @method $this defaultValue($date) 可选，选择器打开时默认显示的时间 可被new Date()解析
 * @method $this defaultTime($date) 范围选择时选中日期所使用的当日内具体时刻 数组，长度为 2，第一项指定开始日期的时刻，第二项指定结束日期的时刻，不指定会使用时刻 00:00:00
 * @method $this placeholder(string $text) 非范围选择时的占位内容
 * @method $this prefixIcon(string $icon) 自定义头部图标的类名
 * @method $this clearIcon(string $icon) 自定义清空图标的类名
 * @method $this unlinkPanels(bool $bool) 在范围选择器里取消两个日期面板之间的联动
 * @method $this validateEvent(bool $bool) 输入时是否触发表单的校验
 * @method $this shortcuts(bool $bool) 设置快捷选项，需要传入数组对象 object[{ text: string, value: Date }]
 * @package Eadmin\component\form\field
 */
class DatePicker extends Field
{
    protected $name = 'ElDatePicker';
}