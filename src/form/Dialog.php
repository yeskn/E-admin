<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-04-21
 * Time: 22:26
 */

namespace Eadmin\form;


use Eadmin\View;

class Dialog extends View
{
    protected $attrs = [
        'title',
        'modal-append-to-body',
        'close-on-click-modal',
        'fullscreen',
    ];
    public function __construct($title,$content)
    {
        $this->setAttr('title',$title);
        $this->setAttr('close-on-click-modal',false);
        $this->content = $content;
    }
    public function getVisibleVar(){
        return 'dialogVisible'.$this->varMark;
    }
    public function getTitleVar(){
        return 'dialogTitle'.$this->varMark;
    }
    public function render()
    {
        list($attrStr, $tableScriptVar) = $this->parseAttr();
        $html ="<el-dialog :visible.sync='dialogVisible' $attrStr>{$this->content}</el-dialog>";
        return $html;

    }
}
