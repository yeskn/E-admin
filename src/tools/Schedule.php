<?php
/**
 * @Author: rocky
 * @Copyright: 广州拓冠科技 <http://my8m.com>
 * Date: 2019/8/19
 * Time: 9:47
 */


namespace Eadmin\tools;


/**
 * 定时任务调度
 * Class Schedule
 * @package app\common\tools
 */
class Schedule
{
    protected $datetime;
    protected $closure;
    protected $minuteRule = 0;
    protected $hourRule = 0;
    protected $hourAtMinute = 0;
    protected $dayRule = 0;
    protected $dayAtTime = '';
    protected $weekly = false;
    protected $monthly = false;
    protected $monthAtDay = 1;
    protected $quarterly = false;
    protected $yearly = false;
    protected $rule = [];
    protected $nowTime;

    public function __construct()
    {
        $this->datetime = new \DateTime;
        $this->nowTime = date('Y-m-d H:i');
    }

    public function setClosure(\Closure $closure)
    {
        $this->closure = $closure;
    }

    public function call(\Closure $closure)
    {
        $new = new self();
        $new->setClosure($closure);
        return $new;
    }

    /**
     * 定时每几分钟执行
     * @Author: rocky
     * 2019/8/19 10:01
     * @param int $minte 每几分钟
     */
    public function everyMinute($minte = 1)
    {
        $this->minuteRule = $minte;
    }

    /**
     * 定时每几时执行
     * @Author: rocky
     * 2019/8/19 10:01
     * @param int $minte 每几小时
     */
    public function hourly($hour = 1)
    {
        $this->hourRule = $hour;
    }

    /**
     * 定时每小时的第几分钟执行
     * @Author: rocky
     * 2019/8/19 10:01
     * @param int $minte 第几分钟 1-59之间
     */
    public function hourlyAt($minute)
    {
        if ($minute > 0 && $minute <= 59) {
            $this->hourly(1);
            $this->hourAtMinute = $minute;
        }
    }

    /**
     * 定时每几天执行
     * @Author: rocky
     * 2019/8/19 10:01
     * @param int $minte 每几天
     */
    public function everyDay($day = 1)
    {
        $this->dayRule = $day;
    }

    /**
     * 定时每天的几点执行
     * @Author: rocky
     * 2019/8/19 10:01
     * @param int $time 时间  格式-小时:分钟 13:00
     */
    public function everyDayAt($time)
    {
        $this->everyDay(1);
        $this->dayAtTime = $time;
    }

    /**
     * 定时每周执行
     * @Author: rocky
     * 2019/8/19 10:01
     */
    public function weekly()
    {
        $this->weekly = true;
    }

    /**
     * 定时每月执行
     * @Author: rocky
     * 2019/8/19 10:01
     */
    public function monthly()
    {
        $this->monthly = true;
    }

    /**
     * 定时每月第几天的某个时间执行
     * @Author: rocky
     * 2019/8/19 11:50
     * @param $day 第几天
     * @param $time 时间 格式-小时:分钟 13:00
     */
    public function monthlyOn($day, $time)
    {
        $this->monthly = true;
        $this->monthAtDay = $day;
        $this->dayAtTime = $time;
    }

    /**
     * 定时每季度执行
     * @Author: rocky
     * 2019/8/19 10:01
     */
    public function quarterly()
    {
        $this->quarterly = true;
    }

    /**
     * 定时每年执行
     * @Author: rocky
     * 2019/8/19 10:01
     */
    public function yearly()
    {
        $this->yearly = true;
    }

    /**
     * 分钟规则生成
     * @Author: rocky
     * 2019/8/19 12:00
     */
    private function minuteRule()
    {
        $minute = 0;
        $nowHour = date('H');
        while ($minute <= 59) {
            $this->datetime->setTime($nowHour, $minute);
            $minute += $this->minuteRule;
            if ($minute <= 59) {
                array_push($this->rule, $this->datetime->format('Y-m-d H:i'));
            }
        }
    }

    /**
     * 小时规则生成
     * @Author: rocky
     * 2019/8/19 12:00
     */
    private function HourRule()
    {
        $hour = 0;
        while ($hour <= 23) {
            $this->datetime->setTime($hour, $this->hourAtMinute);
            $hour += $this->hourRule;
            if ($hour <= 23) {
                array_push($this->rule, $this->datetime->format('Y-m-d H:i'));
            }
        }
    }

    /**
     * 每天规则生成
     * @Author: rocky
     * 2019/8/19 12:00
     */
    private function dayRule()
    {
        $day = 0;
        $monthDayNum = date("t");
        $year = date('Y');
        $month = date('m');
        if (!empty($this->dayAtTime)) {
            list($hour, $minute) = explode(':', $this->dayAtTime);
        } else {
            $hour = 0;
            $minute = 0;
        }
        while ($day < $monthDayNum) {
            $day += $this->dayRule;
            if ($day <= $monthDayNum) {
                $this->datetime->setDate($year, $month, $day);
                $this->datetime->setTime($hour, $minute, 0);
                array_push($this->rule, $this->datetime->format('Y-m-d H:i'));
            }
        }
    }

    /**
     * 每周规则生成
     * @Author: rocky
     * 2019/8/19 12:00
     */
    private function weekRule()
    {
        $monthDayNum = date("t");
        $day = 0;
        $year_month = date('Y-m');
        $year = date('Y');
        $month = date('m');
        while ($day < $monthDayNum) {
            $day += 1;
            $week = date("w", strtotime($year_month . '-' . $day));
            if ($week == 0) {
                $this->datetime->setDate($year, $month, $day);
                $this->datetime->setTime(0, 0, 0);
                array_push($this->rule, $this->datetime->format('Y-m-d H:i'));
            }
        }
    }

    /**
     * 每月规则生成
     * @Author: rocky
     * 2019/8/19 12:00
     */
    private function monthRule()
    {
        $month = 0;
        $year = date('Y');
        if (!empty($this->dayAtTime)) {
            list($hour, $minute) = explode(':', $this->dayAtTime);
        } else {
            $hour = 0;
            $minute = 0;
        }
        while ($month < 12) {
            $month += 1;
            $this->datetime->setDate($year, $month, $this->monthAtDay);
            $this->datetime->setTime($hour, $minute, 0);
            array_push($this->rule, $this->datetime->format('Y-m-d H:i'));
        }
    }

    /**
     * 季度规则生成
     * @Author: rocky
     * 2019/8/19 12:00
     */
    private function quarterlyRule()
    {
        $year = date('Y');
        $this->datetime->setDate($year, 1, 1);
        $this->datetime->setTime(0, 0, 0);
        array_push($this->rule, $this->datetime->format('Y-m-d H:i'));
        $this->datetime->setDate($year, 4, 1);
        $this->datetime->setTime(0, 0, 0);
        array_push($this->rule, $this->datetime->format('Y-m-d H:i'));
        $this->datetime->setDate($year, 8, 1);
        $this->datetime->setTime(0, 0, 0);
        array_push($this->rule, $this->datetime->format('Y-m-d H:i'));
        $this->datetime->setDate($year, 12, 1);
        $this->datetime->setTime(0, 0, 0);
        array_push($this->rule, $this->datetime->format('Y-m-d H:i'));
    }

    /**
     * 每年规则生成
     * @Author: rocky
     * 2019/8/19 12:00
     */
    private function yearRule()
    {
        $year = date('Y');
        $this->datetime->setDate($year, 1, 1);
        $this->datetime->setTime(0, 0, 0);
        array_push($this->rule, $this->datetime->format('Y-m-d H:i'));
    }

    public function __destruct()
    {
        if ($this->minuteRule > 0) {
            $this->minuteRule();
        } elseif ($this->hourRule > 0) {
            $this->hourRule();
        } elseif ($this->dayRule > 0) {
            $this->dayRule();
        } elseif ($this->weekly) {
            $this->weekRule();
        } elseif ($this->monthly) {
            $this->monthRule();
        } elseif ($this->quarterly) {
            $this->quarterlyRule();
        } elseif ($this->yearly) {
            $this->yearRule();
        }
        if (in_array($this->nowTime, $this->rule)) {
            call_user_func($this->closure);
        }
    }
}
