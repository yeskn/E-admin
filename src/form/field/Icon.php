<?php


namespace Eadmin\form\field;
use Eadmin\form\Field;
/**
 * 图标选择器
 * Class Icon
 * @package Eadmin\form
 */
class Icon extends Field
{
    public function render()
    {
        list($attrStr, $tableScriptVar) = $this->parseAttr();
        $html = "<e-icon-picker {$attrStr} />";
        return $html;
    }
}
