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

class Input extends Component
{
    protected $name = 'ElInput';
    public static function create(){
        return new self();
    }
    /**
     * 类型
     * @param string $value
     * @return Input
     */
    public function type(string $value){
        return $this->attr(__FUNCTION__,$value);
    }
    /**
     * 尺寸
     * @param string $value medium / small / mini
     * @return $this
     */
    public function size(string $value)
    {
        return $this->attr(__FUNCTION__, $value);
    }
}