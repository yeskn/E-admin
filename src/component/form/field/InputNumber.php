<?php


namespace Eadmin\component\form\field;


use Eadmin\component\form\Field;

/**
 * 计数器
 * Class InputNumber
 * @link https://element-plus.gitee.io/#/zh-CN/component/input-number
 * @method $this min(int $num) 设置计数器允许的最小值
 * @method $this max(int $num) 设置计数器允许的最大值
 * @method $this step(int $num) 计数器步长
 * @method $this stepStrictly(bool $bool) 是否只能输入 step 的倍数
 * @method $this precision(int $num) 数值精度
 * @method $this size(string $size) 计数器尺寸 large / medium / small / mini
 * @method $this controls(bool $bool) 是否使用控制按钮
 * @method $this controlsPosition(string $position) 控制按钮位置 right
 * @method $this placeholder(string $text) 输入框默认 placeholder right
 * @package Eadmin\component\form\field
 */
class InputNumber extends Field
{
    protected $name = 'ElInputNumber';
}