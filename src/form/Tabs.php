<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-04-20
 * Time: 21:23
 */

namespace Eadmin\form;


use Eadmin\View;

class Tabs extends View
{
    protected $tabPane = [];
    public function __construct(array $tabPanes = [])
    {

        foreach ($tabPanes as $tabPane){
            $this->tabPane[] = "<el-tab-pane label='{$tabPane['label']}'>{$tabPane['content']}</el-tab-pane>";
        }
    }

    /**
     * 添加标签页
     * @param string $label 标签
     * @param string $contentHtml 内容
     */
    public function push($label,$contentHtml){
        $this->tabPane[] = "<el-tab-pane label='{$label}'>{$contentHtml}</el-tab-pane>";
    }
    public function render()
    {
        list($attrStr, $tableScriptVar) = $this->parseAttr();
        
        $html = "<el-tabs {$attrStr}>%s</el-tabs>";
        $html =  sprintf($html,implode('',$this->tabPane));
        return $html;
    }
}
