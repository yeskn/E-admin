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
 * Class Quick
 * @package \Eadmin\layout\Quick
 * @method \Eadmin\layout\Quick create($title,$icon,$iconColor='') static 创建快捷入口
 * @method \Eadmin\layout\Quick href($url) static 跳转链接
 */
class Quick extends Facade
{
    protected static function getFacadeClass()
    {
        return \Eadmin\layout\Quick::class;
    }
}
