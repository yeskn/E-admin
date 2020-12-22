<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-05-19
 * Time: 21:50
 */

namespace Eadmin\form\field;

use Eadmin\form\Field;
class Checkbox extends Field
{
    protected $attrs = [
        'data',
        'disabled',
    ];
    protected $vertical = false;
    protected $optionHtml = '<el-checkbox %s>{{item.label}}</el-checkbox>';
    public function __construct($field, $label, array $arguments = [])
    {
        parent::__construct($field, $label, $arguments);
        $this->default([]);
    }
    /**
     * 竖排
     */
    public function vertical(){
        $this->vertical = true;
        return $this;
    }
    /**
     * 设置选项数据
     * @param array $datas
     */
    public function options(array $datas)
    {
        $model = $this->getAttr('v-model');
        $options = [];
        foreach ($datas as $value => $label) {
            $options[] = [
                'value' => $value,
                'label' => $label,
            ];
        }
        $optionHtml = "<el-checkbox   v-if=\"typeof({$model}) == 'object' && typeof({$model}[0]) == 'string'\" :label=\"'' + item.value\">{{item.label}}</el-checkbox>
            <el-checkbox   v-else-if=\"typeof({$model}) == 'object' && typeof({$model}[0]) == 'number'\" :label=\"(('' + item.value) . trim() == '') ? '' : parseInt(item.value)\">{{item.label}}</el-checkbox>
            <el-checkbox   v-else :label=\"item.value\">{{item.label}}</el-checkbox>";
        if($this->vertical){
            $optionHtml = "<div>{$optionHtml}</div>";
        }
        $this->optionHtml = "<template v-for='item in checkboxData{$this->varMark}'>{$optionHtml}</template>";
        $this->setAttr('data', $options);
        return $this;

    }

    /**
     * 按钮样式
     */
    public function themeButton(){
        $this->optionHtml  = str_replace('el-checkbox','el-checkbox-button',$this->optionHtml);
        return $this;
    }
    /**
     * 带边框样式
     */
    public function themeBorder(){
        $this->optionHtml  = str_replace('<el-checkbox','<el-checkbox border',$this->optionHtml);
        return $this;
    }
    public function render()
    {
        list($attrStr, $tableScriptVar) = $this->parseAttr();
        $html = "<el-checkbox-group {$attrStr}>
    {$this->optionHtml}
  </el-checkbox-group>";
        return $html;
    }
}
