<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-12-27
 * Time: 21:04
 */

namespace Eadmin\component\form;


use Eadmin\component\basic\Button;
use Eadmin\component\Component;

/**
 * Class Input
 * @package Eadmin\component\form
 * @method $this size(string $value) 尺寸 medium / small / mini
 * @method $this type(string $value) 输入框类型  text / textarea / hidden / password / number
 */
class Input extends Component
{
    protected $name = 'ElInput';
    public static function create(){
        return new self();
    }
}
