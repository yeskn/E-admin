<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-12-27
 * Time: 20:39
 */

namespace Eadmin\component\basic;


use Eadmin\component\Component;
/**
 * Class Button
 * @package Eadmin\component\basic
 * @method $this disabled(bool $value) 是否禁用状态
 * @method $this loading(bool $value) 是否加载中状态
 * @method $this circle(bool $value) 是否圆形按钮
 * @method $this round(bool $value) 是否圆角按钮
 * @method $this plain(bool $value) 是否朴素按钮
 * @method $this type(string $value) 类型 primary / success / warning / danger / info / text
 * @method $this nativeType(string $value) 原生type属性 button / submit / reset
 * @method $this size(string $value) 尺寸 medium / small / mini
 * @method $this icon(string $value) 图标
 */
class Button extends Component
{
    protected $name = 'ElButton';
    public function __construct($content)
    {
        $this->content($content);
    }
    /**
     * 创建一个按钮
     * @param string $content 按钮内容
     * @return Button
     */
    public static function create(string $content)
    {
        return new self($content);
    }
}
