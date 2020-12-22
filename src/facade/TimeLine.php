<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-06-22
 * Time: 21:55
 */

namespace Eadmin\facade;

use think\Facade;

/**
 * Class TimeLine
 * @package \Eadmin\layout\TimeLine
 * @method \Eadmin\layout\TimeLine create(array $datas,$timeField,$contentField) static 创建
 * @method \Eadmin\layout\TimeLine asc() static 排序小到大
 */
class TimeLine extends Facade
{
    protected static function getFacadeClass()
    {
        return \Eadmin\layout\TimeLine::class;
    }
}
