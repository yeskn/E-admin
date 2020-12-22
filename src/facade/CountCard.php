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
 * Class CountCard
 * @package \Eadmin\layout\CountCard
 * @method string create($title,$currentTotal,$total,$icon,$iconColor='',$badge='日') static 创建统计卡片
 */
class CountCard extends Facade
{
    protected static function getFacadeClass()
    {
        return \Eadmin\layout\CountCard::class;
    }
}
