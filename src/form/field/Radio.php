<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-05-19
 * Time: 21:50
 */

namespace Eadmin\form\field;

use Eadmin\form\Field;
class Radio extends Field
{
    protected $attrs = [
        'data',
        'disabled',
    ];
    protected $optionHtml;
    protected $eventJs = null;
    protected $vertical = false;

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
        $optionHtml = "
        <el-radio
          v-if=\"typeof({$model}) == 'string'\"
          :key='item.value'
          :label=\"'' + item.value\"
          >
          <span v-html='item.label'></span>
        </el-radio>
         <el-radio
          v-else-if=\"typeof({$model}) == 'number'\"
          :key='item.value'
          :label=\"(('' + item.code).trim() == '')?'':parseInt(item.value)\"
          >
          <span v-html='item.label'></span>
        </el-radio>
        <el-radio
          v-else-if=\"typeof({$model}) == 'object' && typeof({$model}[0]) == 'string'\"
          :key='item.value'
          :label=\"'' + item.value\"
          >
          <span v-html='item.label'></span>
        </el-radio>  
        <el-radio
          v-else-if=\"typeof({$model}) == 'object' && typeof({$model}[0]) == 'number'\"
          :key='item.value'
          :label=\"(('' + item.value) . trim() == '') ? '' : parseInt(item.value)\"
          >
          <span v-html='item.label'></span>
        </el-radio>
         <el-radio
          v-else
          :key='item.value'
          :label='item.value'
          >
          <span v-html='item.label'></span>
        </el-radio>";
        if($this->vertical){
            $optionHtml = "<div style='margin-top: 10px'>{$optionHtml}</div>";
        }
        $this->optionHtml = "<template v-for='item in radioData{$this->varMark}'>{$optionHtml}</template>";
        $this->setAttr('data', $options);
        return $this;
    }
    /**
     * 按钮样式
     */
    public function themeButton(){
        $this->optionHtml  = str_replace('el-radio','el-radio-button',$this->optionHtml);
        return $this;
    }
    /**
     * 带边框样式
     */
    public function themeBorder(){
        $this->optionHtml  = str_replace('<el-radio','<el-radio border',$this->optionHtml);
        return $this;
    }
    public function render()
    {
        list($attrStr, $tableScriptVar) = $this->parseAttr();
        $html = "<el-radio-group {$attrStr}>{$this->optionHtml}</el-radio-group>";
        return $html;
    }

}
