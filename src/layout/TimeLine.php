<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-06-22
 * Time: 21:15
 */

namespace Eadmin\layout;


use Eadmin\View;

/**
 * 时间线
 * Class Timeline
 * @package Eadmin\layout
 */
class TimeLine extends View
{
    protected $html = '';
    protected $placement = '';
    protected $datas = [];
    protected $timeField = 'time';
    protected $contentField = 'content';
    public function __construct($timeField='time',$contenField='content')
    {
        $this->contentField = $contenField;
        $this->timeField = $timeField;
    }
    /**
     * 创建
     * @param array $datas
     * @return TimeLine
     */
    public function create(array $datas,$timeField,$contenField){
        $self = new self($timeField,$contenField);
        $self->data($datas);
        return $self;
    }
    /**
     * 设置数据源
     * @param array $datas
     */
    public function data(array $datas){
        $this->datas = $datas;
    }

    /**
     * 时间戳置于内容之上
     * @return $this
     */
    public function timeTop(){
        $this->placement = "placement='top'";
        return $this;
    }
    /**
     * 小到大排序
     */
    public function asc(){
        $this->setAttr(':reverse','true');
        return $this;
    }
    public function render(){
        foreach ($this->datas as $value){
            $this->html .= "<el-timeline-item {$this->placement} timestamp='{$value[$this->timeField]}'>{$value[$this->contentField]}</el-timeline-item>";
        }
        list($attrStr, $scriptVar) = $this->parseAttr();
        return "<el-timeline $attrStr>{$this->html}</el-timeline>";
    }
}
