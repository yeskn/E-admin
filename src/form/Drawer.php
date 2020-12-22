<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-04-21
 * Time: 22:26
 */

namespace Eadmin\form;


use Eadmin\View;

class Drawer extends View
{
    protected $attrs = [
        'title',
        'append-to-body',
        'wrapper-closable',
        'size',
    ];
    public function __construct($title,$content)
    {
        $this->setAttr('title',$title);
        $this->content = $content;
    }
    public function getVisibleVar(){
        return 'drawerVisible'.$this->varMark;
    }
    public function getTitleVar(){
        return 'drawerTitle'.$this->varMark;
    }
    public function render()
    {
        list($attrStr, $tableScriptVar) = $this->parseAttr();
        $html ="<el-drawer :visible.sync='dialogVisible' $attrStr>{$this->content}</el-drawer>";
        return $html;

    }
}
