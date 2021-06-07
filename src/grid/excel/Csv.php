<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-11-08
 * Time: 09:55
 */

namespace Eadmin\grid\excel;

/**
 * csv导出
 * Class Csv
 * @package Eadmin\grid\excel
 */
class Csv extends AbstractExporter
{
    static $fp = null;
    public function export()
    {
        static $nums = 0;
        if(is_null(self::$fp)){
            set_time_limit(0);
            ini_set('memory_limit', '128M');
            header('Content-Type: application/vnd.ms-execl');
            header('Content-Disposition: attachment;filename="' . $this->fileName . '.csv"');
            self::$fp = fopen('php://output', 'a');
        }
        $this->filterColumns();
        //设置标题
        $title  = array_values($this->columns);
        $fields = array_keys($this->columns);
        foreach ($title as $key => $item) {
            $title[$key] = mb_convert_encoding($item, 'GBK', 'UTF-8');
        }
        //将标题写到标准输出中
        if ($nums == 0) {
            fputcsv(self::$fp, $title);
        }
        foreach ($this->data as $item) {
            $row = [];
            foreach ($fields as $field) {
                $value = is_null($item[$field]) ? '' : $item[$field];
                $row[] = mb_convert_encoding($value, 'GBK', 'UTF-8');
            }
            fputcsv(self::$fp, $row);
            $nums++;
            if ($nums == 5000) {
                $nums = 1;
                ob_flush();
                flush();
            }
        }
    }
}
