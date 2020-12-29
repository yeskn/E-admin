<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-05-18
 * Time: 20:10
 */

namespace Eadmin\form\field;

use Eadmin\form\Field;
class DateTime extends Field
{
    protected $attrs = [
        'disabled',
        'readonly',
        'editable',
        'clearable',
        'arrow-control',
        'picker-options',
        'is-range',
    ];
    protected $type = 'date';
    protected $html = '<el-date-picker %s></el-date-picker>';
    public function __construct($field, $label,$arguments = [])
    {
        parent::__construct($field, $label,$arguments);
        $this->setAttr('placeholder', '请选择' . $label);
        $this->setAttr('editable', false);
        $this->setAttr('value-format', 'yyyy-MM-dd');
    }

    /**
     * 设置成范围选择器
     * @param string $startPlaceholder 范围选择时开始日期的占位内容
     * @param string $endPlaceholder 范围选择时结束日期的占位内容
     * @return $this
     */
    public function range($startPlaceholder='',$endPlaceholder=''){

        switch ($this->type){
            case 'time':
                $this->setAttr('is-range', true);
                break;
            case 'date':
                $this->setType('daterange');
                break;
            case 'datetime':
                $this->setType('datetimerange');
                break;
        }
        if(empty($startPlaceholder)){
            $this->setAttr('start-placeholder', '请选择开始' . $this->label);
        }else{
            $this->setAttr('start-placeholder', $startPlaceholder);
        }
        if(empty($endPlaceholder)){
            $this->setAttr('end-placeholder', '请选择结束' . $this->label);
        }else{
            $this->setAttr('end-placeholder', $endPlaceholder);
        }
        return $this;
    }
    /**
     * 显示类型
     * @param string $type year/month/date/week/time/datetime datetimerange/daterange
     */
    public function setType($type){
        $this->type = $type;
        switch ($this->type){
            case 'time':
                $this->setAttr('value-format', 'HH:mm:ss');
                break;
            case 'daterange':
                $this->setAttr('value-format', 'yyyy-MM-dd');
                break;
            case 'datetimerange':
                $this->setAttr('value-format', 'yyyy-MM-dd HH:mm:ss');
                break;
            case 'date':
                $this->setAttr('value-format', 'yyyy-MM-dd');
                break;
            case 'datetime':
                $this->setAttr('value-format', 'yyyy-MM-dd HH:mm:ss');
                break;
        }
        if($type == 'time'){
            $this->html = '<el-time-picker %s></el-time-picker>';
        }else{
            $this->setAttr('type',$type);
        }
        return $this;
    }
    /**
     * 设置显示日期格式
     * @param string $format
     */
    public function format($format){
        $this->setAttr('value-format', $format);
        if($this->type == 'time'){
            $this->setAttr('picker-options',['format'=>$format]);
        }else{
            $this->setAttr('format',$format);
        }
        return $this;
    }
    public function render(){
        list($attrStr, $tableScriptVar) = $this->parseAttr();
        $html = sprintf($this->html,$attrStr);
        return $html;
    }
}
