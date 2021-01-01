<?php


namespace Eadmin\component\form\field;


use Eadmin\component\form\Field;

/**
 * 多选框组
 * Class CheckboxGroup
 * @link https://element-plus.gitee.io/#/zh-CN/component/checkbox
 * @method $this size(string $size) 多选框组尺寸，仅对按钮形式的 Checkbox 或带有边框的 Checkbox 有效 medium / small / mini
 * @method $this min(int $num) 可被勾选的 checkbox 的最小数量
 * @method $this max(int $num) 可被勾选的 checkbox 的最大数量
 * @method $this textColor(string $color) 按钮形式的 Checkbox 激活时的文本颜色
 * @method $this fill(string $color) 按钮形式的 Checkbox 激活时的填充色和边框色
 * @package Eadmin\component\form\field
 */
class CheckboxGroup extends Field
{
    protected $name = 'ElCheckboxGroup';
}