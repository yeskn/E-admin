<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-04-12
 * Time: 16:11
 */

namespace Eadmin\grid;

use Eadmin\form\Dialog;
use think\helper\Str;
use Eadmin\form\Drawer;
use Eadmin\service\TableViewService;
use Eadmin\View;

class Table extends View
{
    protected $attrs = [
        'data',
        'stripe',
        'border',
        'fit',
        'show-header',
        'highlight-current-row',
        'current-row-key',
        'row-style',
        'cell-class-name',
        'cell-style',
        'header-row-class-name',
        'header-row-style',
        'default-expand-all',
        'expand-row-keys',
        'default-sort',
        'show-summary',
        'span-method',
        'select-on-indeterminate',
        'lazy',
        'load',
        'tree-props',
    ];
    protected $scriptVarStr = '';
    protected $headers;
    protected $data;
    protected $cellComponent = [];
    protected $scriptArr = [];

    public function __construct($headers, array $data)
    {
        $this->template = 'table';
        $this->headers = $headers;
        $this->setAttr('data', $data);
        $this->setAttr('@sort-change', 'sortHandel');
        //$this->setAttr('@selection-change', 'selectionChange');
        $this->setAttr('@cell-click', 'cellClick');
        $this->setAttr('@row-dblclick', 'rowDblclick');
        $this->setAttr(':row-class-name', 'tableRowClassName');
        $this->setAttr('@row-click', 'rowClick');
        $this->setAttr('ref', 'dragTable');
        $this->setAttr('v-loading', 'loading');
        $this->setAttr(':header-cell-style', '{background:"linear-gradient(to top,#fafafa,#ffffff)",color:"#606266",borderTop:"solid 1px #ededed"}');
    }

    //获取自定义内容组件
    public function cellComponent()
    {
        return $this->cellComponent;
    }

    /**
     * 对话框表单
     * @param $title 标题
     * @param bool $fullscreen 是否全屏
     * @param string $width 宽度
     */
    public function setFormDialog($title, $fullscreen = false, $width = "40%")
    {
        $dialog = new Dialog($title, "<component :is='plugDialog' :dialogVisible.sync='dialogVisible' :tableDataUpdate.sync='tableDataUpdate'></component>");
        $dialog->setAttr('width', $width);
        if ($fullscreen) {
            $dialog->setAttr('fullscreen', true);
        }
        $this->setVar('dialog', $dialog->render());
        $this->setVar('dialogVar', $dialog->getVisibleVar());
        $this->setVar('dialogTitleVar', $dialog->getTitleVar());
        $this->scriptArr = array_merge($this->scriptArr, $dialog->getScriptVar());
    }

    /**
     * 抽屉表单
     * @param $title 标题
     * @param bool $direction 打开的方向 rtl / ltr / ttb / btt
     * @param string $size 窗体的大小
     */
    public function setFormDrawer($title, $direction = 'rtl', $size = '30%')
    {
        $drawer = new Drawer($title, "<component :is='plugDialog' :dialogVisible.sync='dialogVisible' :tableDataUpdate.sync='tableDataUpdate'></component>");
        $drawer->setAttr('size', $size);
        $drawer->setAttr('direction', $direction);
        $drawer->setAttr('wrapper-closable', false);
        $this->setVar('dialog', $drawer->render());
        $this->setVar('dialogVar', $drawer->getVisibleVar());
        $this->setVar('dialogTitleVar', $drawer->getTitleVar());
        $this->scriptArr = array_merge($this->scriptArr, $drawer->getScriptVar());
    }

    /**
     * 设置列
     * @param $cloumns
     */
    public function setColumn($cloumns)
    {
        $this->headers = $cloumns;
    }

    public function setScriptArr($scriptArr)
    {
        $this->scriptArr = array_merge($this->scriptArr, $scriptArr);
    }

    /**
     * 返回视图
     * @return string
     */
    public function view()
    {
        $columnHtml = '';
        $mobileHtml = '';
        $tableGrid = TableViewService::instance()->grid();
        $checkboxOptions = [];
        $checkboxColumn = [];
        $columns = [];
        $eadminColumn = [];
        $insertIndex = 1;
        foreach ($this->headers as $field => $label) {
            if ($label instanceof Column) {
                $column = $label;
            } else {
                $column = new Column($field, $label);
            }
            $label = trim(strip_tags($column->label));
            $key = $column->field . $label;
            $columns[$key] = $column;
            if(strpos($column->field,'eadminColumnIndex') !== false){
                $eadminColumn[] = $column;
            }elseif (strpos($column->field,'eadminColumnAction') !== false){
                $insertIndex = count($eadminColumn);
                $eadminColumn[] = $column;
            }
            if (!$column->isHide()){
                $checkboxOptions[] = [
                    'field' => $column->field,
                    'label' => $label,
                ];
            }
        }

        //选择当前自定义视图表格字段方案
        if (!empty($tableGrid)) {
            $fields = array_flip($tableGrid['fields']);
            $columns = array_replace($fields,$columns);
            $columns = array_intersect_key($columns,$fields);
            array_splice($eadminColumn,$insertIndex,0,$columns);
            $columns = $eadminColumn;

        }

        foreach ($columns as $column) {
            if (!($column instanceof Column)) {
                continue;
            }
            if ($column->label == '删除时间') {
                $column->setAttr('v-if', "checkboxColumn.indexOf(\"$column->field\") !== -1 && deleteColumnShow");
                $checkboxColumn[] = $column->field;
            } elseif ($column->isHide()) {
                $column->setAttr('v-if', "1 == 0");
            } else {
                $column->setAttr('v-if', "checkboxColumn.indexOf(\"$column->field\") !== -1");
                $checkboxColumn[] = $column->field;
            }
            $i = count($this->cellComponent);
            $this->cellComponent = array_merge($this->cellComponent, $column->getDisplay($i));
            $columnHtml .= $column->render();
            $mobileHtml .= "<el-form-item label='{$column->label}'>{$column->html}</el-form-item>";
            $this->scriptArr = array_merge($this->scriptArr, $column->getScriptVar());
        }
        $this->setVar('tableDataScriptVar', json_encode($this->getAttr('data')));
        $this->removeAttr('data');
        $this->setAttr(':data','tableData');
        $columnScriptVar = implode(',', $this->scriptArr);
        list($attrStr, $tableScriptVar) = $this->parseAttr();
        if (!empty($columnScriptVar)) {
            if(empty($tableScriptVar)){
                $tableScriptVar = $columnScriptVar;
            }else{
                $tableScriptVar .= ',' . $columnScriptVar;
            }
        }
        $mobileHtml = "<el-table-column type='expand' v-if=\"device === 'mobile'\"><template slot-scope=\"scope\"><el-form label-position='left'>$mobileHtml</el-form></template></el-table-column>";
        $tableHtml = '<el-table  @selection-change="handleSelect" ' . $attrStr . '>' . $mobileHtml . $columnHtml . '</el-table>';
        $this->setVar('cellComponent', json_encode($this->cellComponent, JSON_UNESCAPED_UNICODE));
        $this->setVar('tableHtml', $tableHtml);

        $this->setVar('checkboxOptions', json_encode($checkboxOptions, JSON_UNESCAPED_UNICODE));
        $this->setVar('tableScriptVar', $tableScriptVar);
        $this->setVar('checkboxColumn', json_encode($checkboxColumn, JSON_UNESCAPED_UNICODE));

        $tableFieldView = new TableFieldView();
        $this->setVar('tableFieldView', "<eadmin-component data='" . rawurlencode($tableFieldView->render()) . "' :fields='checkboxOptions'></eadmin-component>");
        return $this->render();
    }


}
