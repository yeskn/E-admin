<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2021-06-27
 * Time: 09:10
 */

namespace Eadmin\component\basic;


use Eadmin\component\Component;

/**
 * 分割线
 * Class Divider
 * @package Eadmin\component\basic
 */
class Divider extends Component
{
    protected $name = 'ElDivider';
    public static function create()
    {
        return new static();
    }
}