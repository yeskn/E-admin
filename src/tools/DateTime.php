<?php
/**
 * @Author: rocky
 * Date: 2020/3/19
 * Time: 14:31
 */


namespace Eadmin\tools;


class DateTime
{
    /**
     * 取时间的前-天/周/月/年
     * @Author: rocky
     * 2020/4/5 11:21
     * @param $num 多少-天/周/月/年
     * @param null $datetime 操作的时间日期
     * @param int $type 默认天，类型：1天，2周，3月，4年
     * @param string $format 返回的时间格式
     * @return false|string|null
     */
    public static function beforeDate($num, $datetime = null, $type = 1,$format='Y-m-d'){
        return self::beforeAndAfterDate($num, $datetime ,'-', $type ,$format);
    }
    /**
     * 取时间的后-天/周/月/年
     * @Author: rocky
     * 2020/4/5 11:21
     * @param $num 多少-天/周/月/年
     * @param null $datetime 操作的时间日期
     * @param int $type 默认天，类型：1天，2周，3月，4年
     * @param string $format 返回的时间格式
     * @return false|string|null
     */
    public static function afterDate($num, $datetime = null, $type = 1,$format='Y-m-d'){
        return self::beforeAndAfterDate($num, $datetime ,'+', $type ,$format);
    }

    /**
     * 取时间的前后-天/周/月/年
     * @Author: rocky
     * 2020/4/5 11:21
     * @param $num 多少-天/周/月/年
     * @param null $datetime 操作的时间日期
     * @param string $op 前还是后，值：(-)前 ，（+）后
     * @param int $type 默认天，类型：1天，2周，3月，4年
     * @param string $format 返回的时间格式
     * @return false|string|null
     */
    public static function beforeAndAfterDate($num, $datetime = null,$op='-', $type = 1,$format='Y-m-d')
    {
            if(is_null($datetime)){
                $datetime = time();
            }
            if(is_string($datetime)){
                $datetime = strtotime($datetime);
            }
            switch ($type){
                //天
                case 1:
                    $resDateTime = date($format,strtotime("{$op}{$num} day",$datetime));
                    break;
                //周
                case 2:
                    $resDateTime = date($format,strtotime("{$op}{$num} week",$datetime));
                    break;
                 //月
                case 3:
                    $resDateTime = date($format,strtotime("{$op}{$num} month",$datetime));
                    break;
                //年
                case 4:
                    $resDateTime = date($format,strtotime("{$op}{$num} year",$datetime));
                    break;
            }
            return $resDateTime;
    }

    /**
     * 求两个日期之间相差的天数
     * @param string $day1
     * @param string $day2
     * @return number
     */
    public static function diffBetweenDayNum($day1, $day2 = '')
    {
        if (is_string($day1)) {
            $second1 = strtotime($day1);
        } else {
            $second1 = $day1;
        }
        if (is_string($day2)) {
            if (empty($day2)) {
                $second2 = time();
            } else {
                $second2 = strtotime($day2);
            }
        } else {
            $second2 = $day2;
        }
        if ($second1 < $second2) {
            $tmp = $second2;
            $second2 = $second1;
            $second1 = $tmp;
        }
        return ceil(($second1 - $second2) / 86400);
    }

    /**
     * 返回两个日期时间的时间差
     * @auth true
     * @menu true
     * @Author: rocky
     * 2020/3/19 14:41
     * @param $begin_time
     * @param $end_time
     * @return array
     */
    public static function timediff($begin_time, $end_time)
    {
        if (is_string($begin_time)) {
            $begin_time = strtotime($begin_time);
        }
        if (is_string($end_time)) {
            $end_time = strtotime($end_time);
        }
        if ($begin_time < $end_time) {
            $starttime = $begin_time;
            $endtime = $end_time;
        } else {
            $starttime = $end_time;
            $endtime = $begin_time;
        }
        $timediff = $endtime - $starttime;
        $days = intval($timediff / 86400);
        $remain = $timediff % 86400;
        $hours = intval($remain / 3600);
        $remain = $remain % 3600;
        $mins = intval($remain / 60);
        $secs = $remain % 60;
        $res = array("day" => $days, "hour" => $hours, "min" => $mins, "sec" => $secs, 'totalSec' => $timediff);
        return $res;
    }
    /**
     * 获取本月所有日期
     * @Author: rocky
     * 2019/6/26
     **/
    public static function thisMonths($format = 'Y-m-d')
    {
        $j = date("t"); //获取当前月份天数
        $start_time = strtotime(date('Y-m-01'));  //获取本月第一天时间戳
        $array = array();
        for ($i = 0; $i < $j; $i++) {
            $array[] = date($format, $start_time + $i * 86400); //每隔一天赋值给数组
        }
        return $array;
    }
    /**
     * 输入两个日期，把这两个日期之间的所有日期取出来
     * @Author: rocky
     * 2019/6/26
     **/
    public static function rangeDates($start, $end,$format = 'Y-m-d')
    {
        if (is_string($start)) {
            $start = strtotime($start);
        }
        if (is_string($end)) {
            $end = strtotime($end);
        }
        $date = [];
        while ($start <= $end) {
            array_push($date, date($format, $start));
            $start = strtotime('+1 day', $start);
        }
        return $date;
    }
}
