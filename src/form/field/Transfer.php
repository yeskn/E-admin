<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-06-21
 * Time: 10:51
 */

namespace Eadmin\form\field;

use Eadmin\form\Field;

/**
 * 穿梭框
 * Class Transfer
 * @package Eadmin\form
 */
class Transfer extends Field
{
    protected $attrs = [
        'data',
        'filterable',
        'titles',
        'button-texts',
        'left-default-checked',
        'right-default-checked',
    ];
    protected $disabledData = [];
    protected $leftFoolter = "";
    protected $rightFoolter = "";
    protected $options = [];

    public function __construct($field, $label, $arguments = [])
    {
        parent::__construct($field, $label, $arguments);

        $this->setAttr('filterable', true);
    }
    /**
     * 禁用选项数据
     * @param array $data 禁用数据
     */
    public function disabledData(array $data){
        $this->disabledData = $data;
    }
    /**
     * 左边底部追加内容
     * @param $html
     */
    public function leftFoolter($html)
    {
        $this->leftFoolter = "<span slot=\"left-footer\">{$html}</span>";
        return $this;
    }

    /**
     * 右边底部追加内容
     * @param $html
     */
    public function rightFoolter($html)
    {
        $this->leftFoolter = "<span slot=\"right-footer\">{$html}</span>";
        return $this;
    }

    /**
     * 默认选中值
     * @param array $leftChecked 左边选中
     * @param array $rightChecked 右边选中
     */
    public function checked(array $leftChecked, array $rightChecked = [])
    {
        $this->setAttr('left-default-checked', $leftChecked);
        $this->setAttr('right-default-checked', $rightChecked);
        return $this;
    }

    /**
     * 设置标题
     * @param $leftTitle 左边标题
     * @param $rightTitle 右边标题
     */
    public function titles($leftTitle, $rightTitle)
    {
        $titles = [$leftTitle, $rightTitle];
        $this->setAttr('titles', $titles);
        return $this;
    }

    /**
     * 设置按钮文字
     * @param $leftButton 左边标题
     * @param $rightButton 右边标题
     */
    public function buttonText($leftButton, $rightButton)
    {
        $text = [$leftButton, $rightButton];
        $this->setAttr('button-texts', $text);
        return $this;
    }

    /**
     * 设置选项数据
     * @param array $datas
     */
    public function options(array $datas)
    {
        $this->options = $datas;
        return $this;

    }

    protected function parseOptions()
    {
        $options = [];
        foreach ($this->options as $value => $label) {
            if (in_array($value, $this->disabledData)) {
                $disabled = true;
            } else {
                $disabled = false;
            }
            $options[] = [
                'key' => $value,
                'label' => $label,
                'disabled' => $disabled,
            ];
        }
        $this->setAttr('data', $options);

    }

    public function render()
    {
        $this->parseOptions();
        list($attrStr, $tableScriptVar) = $this->parseAttr();
        $html = "<el-transfer {$attrStr}>{$this->leftFoolter}{$this->rightFoolter}</el-transfer>";
        return $html;
    }
}
