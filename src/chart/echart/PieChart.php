<?php

namespace Eadmin\chart\echart;


use Eadmin\chart\EchartAbstract;

/**
 * 饼图
 * Class PieChart
 * @package Eadmin\chart
 */
class PieChart extends EchartAbstract
{
    public function __construct($height = '350px', $width = '100%')
    {
        parent::__construct($height, $width);
        $this->options = [
            'title'   => [
                'text' => '',
            ],
            'tooltip' => [
                'trigger'   => 'item',
                'formatter' => '{a} <br/>{b} => {c} ({d}%)'
            ],

            'legend' => [
                'left'   => 'center',
                'bottom' => '0',
                'data'   => [],
            ],
            'series' => []
        ];
    }

    /**
     * 设置数据源
     * @param string $name
     * @param array $data
     */
    public function series(string $name, array $data)
    {
        $names        = array_column($data, 'name');
        $this->legend = array_merge($this->legend, $names);
        $length       = count($this->series);
        $start        = $length * 30 + 10;
        $end          = ($length + 1) * 20;

        $this->series[] = [
            'label'             => [
                'formatter'       => '{b|{b}：}{c}  {per|{d}%}',
                'backgroundColor' => '#fff',
                'borderColor'     => '#ededed',
                'borderWidth'     => 1,
                'borderRadius'    => 4,
                'rich'            => [
                    'a'   => [
                        'color'      => '#999',
                        'lineHeight' => 22,
                        'align'      => 'center',

                    ],
                    'hr'  => [
                        'borderColor' => '#ededed',
                        'width'       => '100%',
                        'borderWidth' => 0.5,
                        'height'      => 0
                    ],
                    'b'   => [
                        'fontSize'   => 14,
                        'lineHeight' => 33,
                        'padding'    => [10, 10],
                    ],
                    'per' => [
                        'color'           => '#eee',
                        'backgroundColor' => '#334455',
                        'padding'         => [10, 10],
                        'borderRadius'    => 2
                    ]

                ]
            ],
            'name'              => $name,
            'type'              => 'pie',
            'roseType'          => 'radius',
            'radius'            => [$start, $start + $end],
            'center'            => ['50%', '38%'],
            'animationEasing'   => 'cubicInOut',
            'animationDuration' => 2600,
            'symbolSize'        => 8,
            'data'              => $data,
        ];
        return $this;
    }

    /**
     * 返回视图
     * @return string
     */
    public function render()
    {
        if (count($this->series) == 1) {
            $this->series[0]['radius'] = [0, 100];
        }
        return parent::render();
    }
}
