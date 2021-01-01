<?php


namespace Eadmin\component\form\field;


use Eadmin\component\form\Field;

/**
 * 多选框按钮
 * Class CheckboxButton
 * @link https://element-plus.gitee.io/#/zh-CN/component/checkbox
 * @method $this trueLabel(string $value) 选中时的值
 * @method $this falseLabel(string $value) 没有选中时的值
 * @method $this checked(bool $check) 当前是否勾选
 * @package Eadmin\component\form\field
 */
class CheckboxButton extends Field
{
    protected $name = 'ElCheckboxButton';
}