<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-06-26
 * Time: 08:53
 */

namespace Eadmin\chart;


abstract class EchartAbstract
{
    protected $series = [];
    protected $legend = [];
    protected $options = [];
    protected $width;
    protected $height;

    public function __construct($height, $width)
    {
        $this->width = $width;
        $this->height = $height;
    }
    abstract function series(string $name, array $data);
    /**
     * 设置标题
     * @param $text
     */
    public function title($text)
    {
        $this->options['text']['title'] = $text;
        return $this;
    }
    public function setOptions($options)
    {
        $this->options = array_merge($this->options, $options);
    }
    public function getOptions()
    {
        return $this->options;
    }
    /**
     * 返回视图
     * @return string
     */
    public function render()
    {
        if(!empty($this->series)){
            $this->options['series'] = $this->series;
        }
        if(!empty($this->legend)){
            $this->options['legend']['data'] = $this->legend;
        }
        $options = json_encode($this->options);
       
        $html = "<eadmin-chart :options='{$options}' width='{$this->width}' height='{$this->height}' />";
        return $html;
    }
}
