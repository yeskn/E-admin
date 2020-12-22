<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-06-18
 * Time: 22:02
 */

namespace Eadmin\form\field;

use Eadmin\form\Field;

/**
 * 滑块
 * Class Slider
 * @package Eadmin\form
 */
class Slider extends Field
{
    protected $attrs = [
        'disabled',
        'show-input',
        'show-input-controls',
        'show-stops',
        'show-tooltip',
        'range',
        'vertical',
        'min',
        'max',
    ];
    public function __construct($field, $label, $arguments = [])
    {
        parent::__construct($field, $label, $arguments);
        $this->defaultValue = 0;
    }


    /**
     * 设置步长
     * @param $num 值
     * @return $this
     */
    public function step($num){
        $this->setAttr(':step',$num);
        return $this;
    }
    /**
     * 设置最大值
     * @param $num
     * @return $this
     */
    public function max($num)
    {
        $this->setAttr('max', $num);
        return $this;
    }
    /**
     * 竖向模式
     * @param int $height 高度
     */
    public function vertical($height=100){
        $this->setAttr('vertical', true);
        $this->setAttr('height', $height.'px');
    }
    /**
     * 设置最小值
     * @param $num
     * @return $this
     */
    public function min($num)
    {
        $this->setAttr('min', $num);
        return $this;
    }
    /**
     * 显示间断点
     * @return $this
     */
    public function showStops(){
        $this->setAttr('show-stops',true);
        return $this;
    }
    /**
     * 显示输入框
     * @return $this
     */
    public function showInput(){
        $this->setAttr('show-input',true);
        return $this;
    }
    public function render()
    {
        list($attrStr, $tableScriptVar) = $this->parseAttr();
        $html = "<el-slider {$attrStr}></el-slider>";
        return $html;
    }
}
