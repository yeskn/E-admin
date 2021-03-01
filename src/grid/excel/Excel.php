<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-06-15
 * Time: 22:12
 */

namespace Eadmin\grid\excel;


use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class Excel extends AbstractExporter
{
    protected $excel;

    protected $sheet;

    protected $callback = null;

    protected $mapCallback = null;

    //合并行字段条件
    protected $mergeCondtionField = null;
    //合并列字段
    protected $mergeRowFields = [];

    public function __construct()
    {
        $this->excel = new Spreadsheet();
        $this->sheet = $this->excel->getActiveSheet();
    }

    private function getLetter($i)
    {

        $letter = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
        if ($i > count($letter) - 1) {
            if ($i > 51) {
                $num = ceil($i / 25);
            } else {
                $num = round($i / 25);
            }
            $j = $i % 26;

            $str = $letter[$num - 1] . $letter[$j];

            return $str;
        } else {
            return $letter[$i];
        }
    }

    /**
     * @param \Closure $closure
     */
    public function callback(\Closure $closure)
    {
        $this->callback = $closure;
    }

    public function export()
    {
        if (is_callable($this->callback)) {
            call_user_func($this->callback, $this);
        }
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $this->filterColumns();
        $rowCount = count($this->data) + 1;
        $letter   = $this->getLetter(count($this->columns) - 1);
        $this->sheet->getStyle("A1:{$letter}{$rowCount}")->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER
            ],
        ]);
        $i = 0;
        foreach ($this->columns as $field => $val) {
            $values = array_column($this->data, $field);
            $str    = $val;
            foreach ($values as $v) {
                if (mb_strlen($v, 'utf-8') > mb_strlen($str, 'utf-8')) {
                    $str = $v;
                }
            }
            $width = ceil(mb_strlen($str, 'utf-8') * 2);
            $this->sheet->getColumnDimension($this->getLetter($i))->setWidth($width);
            $i++;
        }
        $i            = 0;
        $fieldCellArr = [];
        foreach ($this->columns as $field => $val) {
            $i++;
            $this->sheet->setCellValueByColumnAndRow($i, 1, $val);
            $fieldCellArr[$field] = $this->getLetter($i - 1);
        }
        $i                = 1;
        $tmpMergeCondition = '';
        $tmpMergeIndex    = 2;
        $rowIndex         = 1;
        foreach ($this->data as $key => &$val) {
            $rowIndex++;
            if ($this->mapCallback instanceof \Closure) {
                $val = call_user_func($this->mapCallback, $val, $this->sheet);
            }
            foreach ($this->columns as $fkey => $fval) {
                $this->sheet->setCellValueByColumnAndRow($i, $key + 2, $val[$fkey]);
                $i++;
            }
            if (!is_null($this->mergeCondtionField)) {
                if ($tmpMergeCondition != $val[$this->mergeCondtionField] || $rowIndex == $rowCount) {
                    if (!empty($tmpMergeCondition)) {
                        foreach ($this->mergeRowFields as $field) {
                            $letter = $fieldCellArr[$field];
                            if ($rowIndex == $rowCount) {
                                if ($tmpMergeCondition != $val[$this->mergeCondtionField]) {
                                    break;
                                }
                                $mergeIndex = $rowIndex;
                            } else {
                                $mergeIndex = $rowIndex - 1;
                            }

                            $this->sheet->mergeCells("{$letter}{$tmpMergeIndex}:{$letter}{$mergeIndex}");
                        }
                    }
                    $tmpMergeCondition = $val[$this->mergeCondtionField];
                    $tmpMergeIndex    = $rowIndex;
                }
            }
            $i = 1;
        }
        ob_end_clean();
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $this->fileName . '.xls"');
        header('Cache-Control: max-age=0');
        $writer = IOFactory::createWriter($this->excel, 'Xls');
        $writer->save('php://output');
        exit;
    }

    public function map(\Closure $closure)
    {
        $this->mapCallback = $closure;
    }

    /**
     * 合并行
     * @param string $conditionField 条件字段(判断重复合并行)
     * @param array $fields 合并字段列
     */
    public function mergeRow(string $conditionField, array $fields)
    {
        $this->mergeCondtionField = $conditionField;
        $this->mergeRowFields     = $fields;
    }
}
