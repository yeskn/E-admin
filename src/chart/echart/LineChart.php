<?php

namespace Eadmin\chart\echart;

use Eadmin\chart\EchartAbstract;
use Eadmin\chart\EchartInterface;
use Eadmin\chart\Gradual;
use Eadmin\View;
use mapleRegion\common\Enum;

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
            'title' => [
                'text' => '',
            ],
            'tooltip' => [
                'trigger' => 'axis'
            ],
            'xAxis' => [
                'data' => [],
                'type' => 'category',
                'boundaryGap' => false,
            ],
            'grid' => [
                'left' => '3%',
                'right' => '4%',
                'bottom' => '3%',
                'containLabel' => true
            ],
            'yAxis' => [
                'type' => 'value'
            ],
            'legend' => [
                'data' => [],
            ],
            'series' => []
        ];
        $this->type = $type;
    }


    /**
     * 设置数据源
     * @param string $name
     * @param array $data
     */
    public function series(string $name, array $data)
    {
        $colors = $this->getColors();
        $this->legend[] = $name;
        $this->series[] = [
            'name' => $name,
            'type' => $this->type,
            'symbolSize' => 8,
            'color' => [
                'type' => 'linear',
                'x' => 0,
                'y' => 0,
                'x2' => 0,
                'y2' => 1,
                'colorStops' => [
                    [
                        'offset' => 0,
                        'color' => $colors[0],
                    ],
                    [
                        'offset' => 1,
                        'color' => $colors[1],
                    ]
                ],
                'globalCoord'=>true
            ],
            'data' => $data,
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
