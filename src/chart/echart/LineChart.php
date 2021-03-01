<?php

namespace Eadmin\chart\echart;

use Eadmin\chart\EchartAbstract;
use Eadmin\chart\EchartInterface;
use Eadmin\View;

/**
 * 折线图表
 * Class LineChart
 * @package Eadmin\chart
 */
class LineChart extends EchartAbstract
{
    protected $type = 'line';

    public function __construct($height = '350px', $width = '100%', $type = 'line')
    {
        parent::__construct($height, $width);
        $this->options = [
            'title'   => [
                'text' => '',
            ],
            'tooltip' => [
                'trigger' => 'axis'
            ],
            'xAxis'   => [
                'data'        => [],
                'type'        => 'category',
                'boundaryGap' => false,
            ],
            'grid'    => [
                'left'         => '3%',
                'right'        => '4%',
                'bottom'       => '3%',
                'containLabel' => true
            ],
            'yAxis'   => [
                'type' => 'value'
            ],
            'legend'  => [
                'data' => [],
            ],
            'series'  => []
        ];
        $this->type    = $type;
    }


    /**
     * 设置数据源
     * @param string $name
     * @param array $data
     */
    public function series(string $name, array $data)
    {
        $this->legend[] = $name;
        $this->series[] = [
            'name'       => $name,
            'type'       => $this->type,
            'symbolSize' => 8,
            'data'       => $data,
        ];
        return $this;
    }

    /**
     * 设置X轴数据
     * @param array $data
     */
    public function xAxis(array $data)
    {
        $this->options['xAxis']['data'] = $data;
        return $this;
    }
}
