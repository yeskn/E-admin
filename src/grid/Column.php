<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-04-14
 * Time: 21:31
 */

namespace Eadmin\grid;


use Eadmin\form\field\Switchs;
use Eadmin\View;

class Column extends View
{
    protected $attrs = [
        'index',
        'render-header',
        'sort-method',
        'sort-by',
        'sort-orders',
        'formatter',
        'selectable',
        'reserve-selection',
        'filters',
        'filter-method',
        'filtered-value',
        'show-overflow-tooltip',
    ];
    protected $scopeTemplate = '<template slot-scope="scope">%s</template>';
    //自定义内容
    protected $display = '';

    //字段
    public $field = '';

    public $label = '';
    protected $filterLabel = '';
    //组件行字段
    protected $rowField = '';
    protected $relationRowField = '';

    //内容颜射
    protected $usings = [];
    //映射标签颜色
    protected $tagColor = [];
    //映射标签颜色主题
    protected $tagTheme = 'light';
    //标签
    protected $tag = '';
    //自定义闭包
    protected $displayClosure = null;

    protected $cellVue;
    //占位栅格数
    protected $md = 24;
    //开启合计行
    protected $isTotalRow = false;
    //导出自定义显示闭包
    protected $exportClosure = null;
    //导出数据值
    protected $exportValue = '';

    public $totalText = '';
    public $closeExport = false;
    public $html = '';
    protected $hide = false;
    //是否行内编辑
    protected $edit = false;
    protected $relation = '';
    protected $grid = null;
    protected $childColumn = [];
    public function __construct($field = '', $label = '', $grid = null)
    {
        $this->grid = $grid;
        $this->label = $label;
        if (!empty($field)) {
            $this->field = $field;
            $field = $this->getField($field);
            $this->rowField = "scope.row.{$this->field}";
            $fields = explode('.', $this->field);
            $this->relation = array_shift($fields);
            $this->relationRowField = "scope.row.{$this->relation}";
            $this->setAttr('prop', $field);
        }
    }


    /**
     * 设置子级column
     * @param array $columns
     */
    public function setChild(array $columns){
        $this->childColumn = $columns;
    }
    /**
     * 隐藏
     * @return $this
     */
    public function hide()
    {
        $this->hide = true;
        return $this;
    }

    public function isHide()
    {
        return $this->hide;
    }

    public function getField($field = '')
    {
        if (empty($field)) {
            $field = $this->field;
        }
        $fields = explode('.', $field);
        return end($fields);
    }

    /**
     * 行内编辑
     */
    public function edit()
    {
        $this->edit = true;
    }

    /**
     * 设置当内容过长被隐藏时显示
     * @return $this
     */
    public function tip()
    {
        $this->setAttr('show-overflow-tooltip', true);
        return $this;
    }

    /**
     * 弹出框
     * @param $val 显示的内容
     * @param $content 弹出内容
     * @param int $width 弹出宽度
     * @param string $placement 弹出方向
     * @return $this
     */
    public function popover($val, $content, $width = 200, $placement = 'top-start')
    {
        $this->display(function () use ($val, $content, $placement, $width) {
            return "<el-popover
    placement='{$placement}'
    width='{$width}'
    trigger='hover'>
    {$content}
    <span slot='reference'>{$val}</span>
  </el-popover>";
        });
        return $this;
    }

    /**
     * 评分显示
     * @param int $max 最大长度
     * @return $this
     */
    public function rate($max = 5)
    {
        $this->display(function ($val) use ($max) {
            return "<el-rate v-model='data.{$this->field}' disabled :max='{$max}'></el-rate>";
        });
        return $this;
    }

    /**
     * 列是否固定
     * @param string $fixed
     */
    public function fixed($fixed = 'left')
    {
        $this->setAttr('fixed', $fixed);
        return $this;
    }

    /**
     * 设置宽度
     * @param int $number
     */
    public function width(int $number)
    {
        $this->setAttr('width', $number);
        return $this;
    }

    /**
     * 对齐方式
     * @param $align 左对齐 left/ 居中 center/ 右对齐 right
     */
    public function align($align)
    {
        $this->setAttr('align', $align);
        return $this;
    }

    /**
     * 设置最小宽度
     * @param int $number
     */
    public function minWidth(int $number)
    {
        $this->setAttr('min-width', $number);
        return $this;
    }

    /**
     * 标签显示
     * @param $color 标签颜色：success，info，warning，danger
     * @param $theme 主题：dark，light，plain
     * @param $size 尺寸:medium，small，mini
     */
    public function tag($color = '', $theme = 'dark', $size = 'mini')
    {
        $this->tag = "<el-tag effect='{$theme}' type='{$color}' size='{$size}'>%s</el-tag>";
        return $this;
    }

    /**
     * 内容映射
     * @param array $usings 映射内容
     * @param array $tagColor 标签颜色
     * @param tagTheme 标签颜色主题：dark，light，plain
     */
    public function using(array $usings, array $tagColor = [], $tagTheme = 'light')
    {
        $this->tagColor = $tagColor;
        $this->tagTheme = $tagTheme;
        $this->usings = $usings;
        return $this;
    }

    public function getUsings()
    {
        return $this->usings;
    }

    /**
     * 自定义显示
     * @param \Closure $closure
     * @return $this
     */
    public function display(\Closure $closure)
    {
        $this->displayClosure = $closure;
        return $this;
    }

    /**
     * 显示html
     * @return $this
     */
    public function html()
    {
        $this->display(function ($val) {
            return $val;
        });
        return $this;
    }

    public function getClosure()
    {
        return $this->displayClosure;
    }

    /**
     * switch开关
     * @param array $active 开启状态 [1=>'开启']
     * @param array $inactive 关闭状态 [0=>'关闭']
     */
    public function switch(array $active = [], array $inactive = [])
    {
        $this->display(function ($val, $data) use ($active, $inactive) {
            $switch = new Switchs('switch', '');
            $switch->setAttr(':row-data', 'data');
            if (count($active) > 0 && count($inactive) > 0) {
                $switch->state($active, $inactive);
            }
            $switch->setAttr('field', $this->field);
            $switch->setAttr('v-model', 'data.' . $this->field);
            return $switch->render();
        });
        return $this;
    }

    /**
     * 开启合计行
     * @param string $text
     */
    public function total($text = '')
    {
        $this->isTotalRow = true;
        $this->totalText = $text;
    }

    public function isTotal()
    {
        return $this->isTotalRow;
    }

    /**
     * 显示语音
     * @return $this
     */
    public function audio()
    {
        $this->display(function ($val, $data) {
            if (empty($val)) {
                return '--';
            }
            if (is_array($val)) {
                $audios = implode(',', $val);
            } else {
                $audios = $val;
            }
            return "<eadmin-audio url='$audios'></eadmin-audio>";
        });
        return $this;
    }

    /**
     * 显示文件
     * @return $this
     */
    public function file()
    {
        $this->display(function ($val, $data) {
            if (empty($val)) {
                return '--';
            }
            if (is_array($val)) {
                $files = $val;
                $htmlArr = [];
                foreach ($files as $file) {
                    $htmlArr[] = "<eadmin-download-file style='margin: 5px' url='$file'></eadmin-download-file>";
                }
                return implode('', $htmlArr);
            } else {
                return "<eadmin-download-file style='margin: 5px' url='$val'></eadmin-download-file>";
            }
        });
        return $this;
    }

    /**
     * 显示视频
     * @param int $width 宽度
     * @param int $height 高度
     * @return $this
     */
    public function video($width = 0, $height = 0)
    {
        $this->display(function ($val, $data) use ($width, $height) {
            if (empty($val)) {
                return '--';
            }
            if (is_array($val)) {
                $videos = implode(',', $val);
            } else {
                $videos = $val;
            }
            if ($width && $height) {
                return "<eadmin-video style='width: {$width}px;height:{$height}px' url='$videos'></eadmin-video>";
            } else {
                return "<eadmin-video url='$videos'></eadmin-video>";
            }
        });
        return $this;
    }

    /**
     * 显示图片
     * @param int $width 宽度
     * @param int $height 高度
     * @param int $radius 圆角
     * @param int $multi 是否显示多图
     * @return $this
     */
    public function image($width = 80, $height = 80, $radius = 5, $multi = false)
    {
        $this->display(function ($val, $data) use ($width, $height, $radius, $multi) {
            if (empty($val)) {
                return '--';
            }
            if (is_string($val)) {
                $images = explode(',', $val);
            } elseif (is_array($val)) {
                $images = $val;
            }
            $html = '';
            $jsonImage = json_encode($images);
            if ($multi) {
                foreach ($images as $image) {
                    $html .= "<el-image style='width: {$width}px; height: {$height}px;border-radius: {$radius}%' src='{$image}' fit='cover' :preview-src-list='{$jsonImage}'></el-image>&nbsp;";
                }
            } else {
                $html = "<el-image style='width: {$width}px; height: {$height}px;border-radius: {$radius}%' src='{$images[0]}' fit='cover' :preview-src-list='{$jsonImage}'></el-image>&nbsp;";
            }
            return $html;
        });
        return $this;
    }

    /**
     * 显示多图片
     * @param int $width 宽度
     * @param int $height 高度
     * @param int $radius 圆角
     * @return $this
     */
    public function images($width = 80, $height = 80, $radius = 5)
    {
        $this->image($width, $height, $radius, true);
    }

    /**
     * 设置数据
     * @param $data
     */
    public function setData($data)
    {
        if(count($this->childColumn) > 0){
            foreach ($this->childColumn as $column){
                $column->setData($data);
            }
        }else{
            $rowData = $data;
            $val = $this->getValue($data);
            if (count($this->usings) > 0) {
                $this->exportValue = isset($this->usings[$val]) ? $this->usings[$val] : '';
            } else {
                $this->exportValue = $val;
            }

            if (isset($rowData['id'])) {
                $id = $rowData['id'];
            } else {
                $id = 0;
            }
            if (!is_null($this->displayClosure)) {
                if (empty($rowData)) {
                    $res = '';
                } else {
                    $clone = clone $this;
                    $res = call_user_func_array($this->displayClosure, [$val, $rowData, $clone]);
                    if ($res instanceof self) {
                        $res = call_user_func_array($clone->getClosure(), [$val, $rowData, $clone]);
                    }

                    $this->exportValue = $res;
                }
                if(empty($this->cellVue)){
                    $this->cellVue .= "<span v-if='data.id == {$id}'>{$res}</span>";
                }else{
                    $this->cellVue .= "<span v-else-if='data.id == {$id}'>{$res}</span>";
                }
            }

            if (!is_null($this->exportClosure) && $rowData) {
                $res = call_user_func_array($this->exportClosure, [$val, $rowData]);
                $this->exportValue = $res;
            }
        }
    }

    /**
     * 关闭excel 导出
     * @Author: rocky
     * 2019/10/9 16:54
     */
    public function closeExport()
    {
        $this->closeExport = true;
        return $this;
    }

    /**
     * 获取导出数据值
     * @return string
     */
    public function getExportValue()
    {
        return $this->exportValue;
    }

    /**
     * 导出数据自定义
     * @param \Closure $closure
     * @return $this
     */
    public function export(\Closure $closure)
    {
        $this->exportClosure = $closure;
        return $this;
    }

    /**
     * 获取数据
     * @param $data 行数据
     * @param null $field 字段
     * @return |null
     */
    public function getValue($data, $field = null)
    {

        if (is_null($field)) {
            $dataField = $this->field;
        } else {
            $dataField = $field;
        }
        if (empty($dataField)) {
            return null;
        }

        foreach (explode('.', $dataField) as $f) {
            if (isset($data[$f])) {
                $data = $data[$f];
            } else {
                $data = null;
            }
        }
        return $data;
    }

    public function getDisplay($key)
    {
        if(count($this->childColumn) > 0){
            $cellVue = [];
            foreach ($this->childColumn as $column){
                $cellVue = array_merge($cellVue,$column->getDisplay($key));
                $key++;
            }
            return $cellVue;
        }else{
            if (!empty($this->cellVue)) {
                $this->display = '<component :is="cellComponent[' . $key . ']" :data="scope.row" :globalRequestParams="globalRequestParams" :width.sync="actionWidth" :table-data-update.sync="tableDataUpdate" :index="scope.$index" :showEditId.sync="showEditId" :showDetailId.sync="showDetailId" :page="page" :size="size" :total="total" :table-data.sync="tableData"></component>';
                $cell = new Cell();
                $cell->setVar('cell', $this->cellVue);
                list($attrStr, $scriptVar) = $cell->parseAttr();
                $cell->setVar('scriptVar', $scriptVar);
                $this->cellVue = $cell->render();
            }
            return [$this->cellVue];
        }
    }

    public function getDetailDisplay($key)
    {

        if (!empty($this->cellVue)) {
            $this->display = '<component :is="cellComponent[' . $key . ']" :data="data"></component>';
            $cell = new Cell();
            $cell->setVar('cell', $this->cellVue);
            list($attrStr, $scriptVar) = $cell->parseAttr();
            $cell->setVar('scriptVar', $scriptVar);
            $this->cellVue = $cell->render();
        }
        return $this->cellVue;
    }

    public function filter(\Closure $closure)
    {
        $filter = $this->grid->getFilter();
        $filter->mode('column');
        $filter->label($this->label);
        call_user_func($closure,$filter);
        $filterHtml = $filter->render();
        $html = <<<EOF
<el-popover
    placement="bottom"
    popper-class="eadmin_popover_filter"
    width="250"
    trigger="click">
    <el-form label-width="100px" class="eadmin_form_box" size="small" ref="form" label-position="top" @submit.native.prevent :model="form">
    {$filterHtml}
    </el-form>
 
    <i class="el-icon-caret-bottom" style="color: #c0c4cc;cursor: pointer;padding:5px 10px" slot="reference" @click.stop=""></i>
  </el-popover>
EOF;
        $this->filterLabel .= $html;
        return $this;
    }

    /**
     * 开启排序
     */
    public function sortable()
    {
        $this->setAttr('sortable', 'custom');
        return $this;
    }

    /**
     * 占位栅格数，24栏占满
     * @param $num 数量
     * @return $this
     */
    public function md($num = 3)
    {
        $this->md = $num;
        return $this;
    }

    public function detailRender()
    {
        $label = '';
        if (!empty($this->label)) {
            $label = "<span style='font-size: 14px;color: #888888'>{$this->label}:</span>&nbsp;";
        }
        $this->rowField = 'data.' . $this->field;
        $this->relationRowField = 'data.' . $this->relation;
        $fields = explode('.', $this->field);
        $fieldVar = '';
        foreach ($fields as $field){
            if(empty($fieldVar)){
                $fieldVar = $field;
            }else{
                $fieldVar .= '.'.$field;
            }
            $ifCondtion[] = "data.{$fieldVar} === null";
        }
        $ifCondtion  = implode(' || ',$ifCondtion);
        if (!empty($this->tag)) {
            $this->display = sprintf($this->tag, "{{{$this->rowField}}}");
        } elseif (count($this->usings) > 0) {
            $html = '';
            foreach ($this->usings as $key => $value) {
                $html = "<span style='font-size: 14px;' v-if=\"{$ifCondtion}\">--</span>";
                if (is_string($key)) {
                    $html .= "<span style='font-size: 14px;' v-else-if=\"{$this->relationRowField} != undefined && {$this->rowField} == '{$key}'\">%s</span>";
                } else {
                    $html .= "<span style='font-size: 14px;' v-else-if='{$this->relationRowField} != undefined && {$this->rowField} == {$key}'>%s</span>";
                }
                if (isset($this->tagColor[$key])) {
                    $this->tag($this->tagColor[$key], $this->tagTheme);
                    $value = sprintf($this->tag, $value);
                }
                $html = sprintf($html, $value);

            }
            $this->display = $html;
        } elseif (empty($this->display) && !empty($this->field)) {
            $this->display = "<span style='font-size: 14px;' v-if=\"{$ifCondtion} || {$this->rowField} === null || {$this->rowField} === ''\">--</span><span style='font-size: 14px;' v-else>{{{$this->rowField}}}</span>";
        }
        $this->display = "<el-col :span='{$this->md}' style='border-bottom-width: 1px; padding-top: 15px;padding-bottom: 15px;border-bottom-style: solid;border-bottom-color: #f0f0f0;display: flex;align-items: center'>" . $label . $this->display . "</el-col>";
        list($attrStr, $dataStr) = $this->parseAttr();
        return $this->display;
    }

    public function render()
    {
        if(count($this->childColumn) > 0){
            $this->removeAttr('prop');
            list($attrStr, $dataStr) = $this->parseAttr();
            $childHtml = '';
            foreach ($this->childColumn as $column){
                $childHtml.=$column->render();
            }
            $columnHtml = "<el-table-column $attrStr label='$this->label'>{$childHtml}</el-table-column>";
            return $columnHtml;
        }else{
            if ($this->getAttr('type')) {
                $this->setField('');
            }
            $this->html = '';
            $fields = explode('.', $this->field);
            $fieldVar = '';
            foreach ($fields as $field){
                if(empty($fieldVar)){
                    $fieldVar = $field;
                }else{
                    $fieldVar .= '.'.$field;
                }
                $ifCondtion[] = "scope.row.{$fieldVar} === null";
            }
            $ifCondtion  = implode(' || ',$ifCondtion);
            if (empty($this->display)) {
                if (!empty($this->tag)) {
                    $this->html = sprintf($this->tag, "{{{$this->rowField}}}");
                    $this->display = sprintf($this->scopeTemplate, $this->html);
                }
                if (count($this->usings) > 0) {
                    $this->html = "<span style='font-size: 14px;' v-if=\"{$ifCondtion}\">--</span>";
                    foreach ($this->usings as $key => $value) {
                        if (is_string($key)) {
                            $this->html .= "<span v-else-if=\"{$this->relationRowField} != undefined && {$this->rowField} == '{$key}'\">%s</span>";
                        } else {
                            $this->html .= "<span v-else-if='{$this->relationRowField} != undefined && {$this->rowField} == {$key}'>%s</span>";
                        }
                        if (isset($this->tagColor[$key])) {
                            $this->tag($this->tagColor[$key], $this->tagTheme);
                            $value = sprintf($this->tag, $value);
                        }
                        $this->html = sprintf($this->html, $value);
                    }
                    $this->display = sprintf($this->scopeTemplate, $this->html);
                }
            } else {
                $this->html = $this->display;
                $this->display = sprintf($this->scopeTemplate, $this->display);
            }
            if (empty($this->display) && !empty($this->field)) {
                $this->html = "<span v-if=\"{$ifCondtion} || {$this->rowField} === null || {$this->rowField} === ''\">--</span><span v-else>{{{$this->rowField}}}</span>";
                $this->display = sprintf($this->scopeTemplate, $this->html);
            }
            list($attrStr, $dataStr) = $this->parseAttr();
            if ($this->edit) {
                $this->html = "<el-input v-if=\"scope.row.eadmin_edit && inputEditField == '{$this->field}'\" :ref=\"'{$this->field}' + scope.\$index\" @change='editInput' @blur='blurInput' v-model='{$this->rowField}'  size='small' /><template v-else>{$this->html}</template>";
                $this->display = sprintf($this->scopeTemplate, $this->html);
            }
            $columnHtml =  "<el-table-column $attrStr><template slot=\"header\" slot-scope=\"scope\">{$this->label}{$this->filterLabel}</template>" . $this->display . "</el-table-column>";
            return $columnHtml;
        }
    }
}
