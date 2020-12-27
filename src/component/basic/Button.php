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
 */
class Button extends Component
{
    protected $name = 'ElButton';

    public function __construct($content)
    {
        $this->content = $content;
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
    /**
     * 是否禁用状态
     * @param bool $value
     * @return $this
     */
    public function disabled(bool $value){
        return $this->attr(__FUNCTION__, $value);
    }
    /**
     * 是否加载中状态
     * @param bool $value
     * @return $this
     */
    public function loading(bool $value){
        return $this->attr(__FUNCTION__, $value);
    }
    /**
     * 是否圆形按钮
     * @param bool $value
     * @return $this
     */
    public function circle(bool $value){
        return $this->attr(__FUNCTION__, $value);
    }
    /**
     * 是否圆角按钮
     * @param bool $value
     * @return $this
     */
    public function round(bool $value){
        return $this->attr(__FUNCTION__, $value);
    }

    /**
     * 是否朴素按钮
     * @param bool $bool
     * @return $this
     */
    public function plain(bool $value){
        return $this->attr(__FUNCTION__, $value);
    }
    /**
     * 类型
     * @param string $value primary / success / warning / danger / info / text
     * @return $this
     */
    public function type(string $value){
        return $this->attr(__FUNCTION__, $value);
    }

    /**
     * 原生 type 属性
     * @param $value button / submit / reset
     * @return $this
     */
    public function nativeType($value){
        return $this->attr(__FUNCTION__, $value);
    }
    /**
     * 尺寸
     * @param string $value medium / small / mini
     * @return $this
     */
    public function size(string $value)
    {
        return $this->attr(__FUNCTION__, $value);
    }
    /**
     * 图标
     * @param string $value
     * @return $this
     */
    public function icon(string $value)
    {
        return $this->attr(__FUNCTION__, $value);
    }
}