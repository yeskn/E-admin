<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-06-18
 * Time: 22:33
 */

namespace Eadmin\form\field;
use Eadmin\form\Field;
/**
 * 颜色选择器
 * Class Color
 * @package Eadmin\form
 */
class Color extends Field
{
    public function render()
    {
        list($attrStr, $tableScriptVar) = $this->parseAttr();
        $html = "<el-color-picker {$attrStr}></el-color-picker>";
        return $html;
    }
}
