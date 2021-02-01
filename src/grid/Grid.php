<?php


namespace Eadmin\grid;


use Eadmin\component\basic\Button;
use Eadmin\component\basic\Html;
use Eadmin\component\basic\Image;
use Eadmin\component\basic\Router;
use Eadmin\component\Component;
use Eadmin\component\grid\Column;
use Eadmin\component\grid\Pagination;
use Eadmin\component\layout\Content;
use Eadmin\contract\GridInterface;
use Eadmin\form\Form;
use Eadmin\traits\CallProvide;
use think\facade\Request;
use think\helper\Str;
use think\Model;

/**
 * 表格
 * Class Grid
 * @package Eadmin\grid
 * @method $this size(string $size) Radio的尺寸，仅在border为真时有效 medium / small / mini
 * @method $this height(int $height) 高度
 * @method $this maxHeight(int $height) 最大高度
 * @method $this stripe(bool $bool = true) 是否为斑马纹
 * @method $this border(bool $bool = true) 是否带有纵向边框
 * @method $this fit(bool $bool) 列的宽度是否自撑开
 * @method $this quickSearch(bool $bool = true) 快捷搜索
 * @method $this hideDeleteButton(bool $bool = true) 隐藏删除按钮
 * @method $this hideTools(bool $bool = true) 隐藏工具栏
 * @method $this hideDeleteSelection(bool $bool = true) 隐藏删除选中按钮
 * @method $this defaultExpandAll(bool $bool) 是否默认展开所有行
 * @method $this showHeader(bool $bool = true) 是否显示表头
 * @method $this highlightCurrentRow(bool $bool = true) 是否要高亮当前行
 * @method $this headerRowStyle(array $value) 表头行样式
 * @method $this rowStyle(array $value) 行样式
 * @method $this cellStyle(array $value) 单元格样式
 * @method $this headerCellStyle(array $value) 表头单元格的 style样式
 * @method $this loadDataUrl(string $value) 设置加载数据url
 * @method $this params(array $value) 加载数据附加参数
 * @property Filter $filter
 */
class Grid extends Component
{
    use CallProvide;

    protected $name = 'EadminGrid';

    protected $column = [];

    protected $pagination;
    //是否隐藏分页
    protected $hidePage = false;
    //操作列
    protected $actionColumn;
    //是否隐藏操作列
    protected $hideAction = false;
    //是否隐藏添加按钮
    protected $hideAddButton = false;
    //查询过滤
    protected $filter = null;

    protected $drive;

    protected $formAction = null;

    protected $detailAction = null;
    //删除前回调
    protected $beforeDel = null;
    //更新前回调
    protected $beforeUpdate = null;
    //工具栏
    protected $tools = [];

    public function __construct($data)
    {
        if ($data instanceof Model) {
            $this->drive = new \Eadmin\grid\drive\Model($data);
        } elseif ($data instanceof GridInterface) {
            $this->drive = $data;
        } else {
            $this->drive = new \Eadmin\grid\drive\Arrays($data);
        }
        //表格表头颜色
        $this->headerCellStyle([
            'background' => 'linear-gradient(to top,#fafafa,#ffffff)',
            'color' => '#606266',
            'borderTop' => 'solid 1px #ededed'
        ]);
        //分页初始化
        $this->pagination = new Pagination();
        $this->pagination->pageSize(20)->background()->layout('total, sizes, prev, pager, next, jumper');
        //操作列
        $this->actionColumn = new Actions($this);
        $this->bindAttValue('modelValue', false);
        $this->attr('eadmin_grid', $this->bindAttr('modelValue'));
        $this->loadDataUrl('eadmin.rest');

        $this->getCallMethod();
    }

    /**
     * 设置标题
     * @param string $title
     * @return string
     */
    public function title(string $title)
    {
        return $this->bind('eadmin_title', $title);
    }

    //头像昵称列
    public function userInfo($headimg = 'headimg', $nickname = 'nickname', $label = '会员信息')
    {
        $column = $this->column($nickname, $label);
        return $column->display(function ($val, $data) use ($column, $headimg) {
            $headimgValue = $data[$headimg];
            $image = Image::create()
                ->src($headimgValue)
                ->fit('cover')
                ->attr('style', ['width' => '80px', 'height' => '80px', "borderRadius" => '50%'])
                ->previewSrcList([$headimgValue]);
            return Html::create()->content($image)->content("<br>{$val}");
        })->align('center');
    }

    public function formAction()
    {
        return $this->formAction;
    }

    public function detailAction()
    {
        return $this->detailAction;
    }

    /**
     * 设置from表单
     * @param Form $form
     */
    public function setForm(Form $form)
    {
        $this->formAction = new ActionMode();
        $this->formAction->form($form);
        return $this->formAction;
    }

    /**
     * 设置detail详情
     * @param Detail $detail
     */
    public function setDetail($detail)
    {
        $this->detailAction = new ActionMode();
        $this->detailAction->detail($detail);
        return $this->detailAction;
    }

    public function drive()
    {
        return $this->drive;
    }

    /**
     * 获取当前模型
     * @return drive\Model|null
     */
    public function model()
    {
        return $this->drive->model();
    }

    /**
     * 查询过滤
     * @param $callback
     */
    public function filter($callback)
    {
        if ($callback instanceof \Closure) {
            call_user_func($callback, $this->getFilter());
        }
    }

    public function getFilter()
    {
        if (is_null($this->filter)) {
            $this->filter = new Filter($this->drive->db());
        }
        return $this->filter;
    }

    //更新前回调
    public function updateing(\Closure $closure)
    {
        $this->beforeUpdate = $closure;
    }

    //删除前回调
    public function deling(\Closure $closure)
    {
        $this->beforeDel = $closure;
    }

    /**
     * 删除
     * @param $id 删除的id
     * @return bool|int
     */
    public function destroy($id)
    {
        if (!is_null($this->beforeDel)) {
            call_user_func($this->beforeDel, $id);
        }
        return $this->drive->destroy($id);
    }

    /**
     * 更新
     * @param $ids
     * @param $data
     * @return mixed
     */
    public function update($ids, $data)
    {
        if (!is_null($this->beforeUpdate)) {
            call_user_func($this->beforeUpdate, $ids, $data);
        }
        return $this->drive->update($ids, $data);
    }

    /**
     * 设置索引列
     * @param string $type 列类型：selection 多选框 ， index 索引 ， expand 可展开的
     * @return Column
     */
    public function indexColumn($type = 'selection', $label = '')
    {
        $column = $this->column('eadminColumnIndex' . $type, $label);
        $column->attr('type', $type);
        return $column;
    }

    /**
     * 拖拽排序
     * @param $field 排序字段
     */
    public function sortDrag($field = 'sort'){
        $this->drive->sortField($field);
        $this->attr('sortDrag',true);
    }
    public function sortInput($field = 'sort'){
        $this->drive->sortField($field);
        $this->attr('sortInput',true);
    }
    /**
     * 操作列定义
     * @param \Closure $closure
     */
    public function actions(\Closure $closure)
    {
        $this->actionColumn->setClosure($closure);
    }

    /**
     * 隐藏添加按钮
     * @param bool $bool
     */
    public function hideAddButton(bool $bool = true)
    {
        $this->hideAddButton = $bool;
    }

    /**
     * 隐藏操作列
     * @param bool $bool
     */
    public function hideAction(bool $bool = true)
    {
        $this->hideAction = $bool;
    }
    public function tools($tools)
    {
        if (is_string($tools)) {
            $this->tools[] = Html::create()->content($tools);
        } elseif (is_array($tools)) {
            foreach ($tools as $tool){
                $this->tools($tool);   
            }
        }elseif($tools instanceof Component){
            $this->tools[] = $tools;
        }
        return $this;
    }

    /**
     * 关闭分页
     */
    public function hidePage(bool $bool = true)
    {
        $this->hidePage = $bool;
    }

    /**
     * 分页组件
     * @return Pagination
     */
    public function pagination()
    {
        return $this->pagination;
    }

    /**
     * 设置分页每页限制
     * @Author: rocky
     * 2019/11/6 14:01
     * @param $limit
     */
    public function setPageLimit($limit)
    {
        $this->pagination->pageSize($limit);
    }

    /**
     * 添加表格列
     * @param string $field 字段
     * @param string $label 显示的标题
     * @return Column
     */
    public function column(string $field = '', string $label = '')
    {
        $column = new Column($field, $label, $this);
        $this->column[] = $column;
        $this->realiton($field);
        return $column;
    }

    /**
     * 解析列返回表格数据
     * @param $datas 数据源
     * @return array
     */
    protected function parseColumn($datas)
    {

        //添加操作列
        if (!$this->hideAction) {
            $this->column[] = $this->actionColumn->column();
        }
        $tableData = [];
        //解析行数据
        foreach ($datas as $data) {
            $row = ['id' => $data[$this->drive->getPk()]];
            foreach ($this->column as $column) {
                $field = $column->attr('prop');
                $row[$field] = $column->row($data);
            }
            if (!$this->hideAction) {
                $actionColumn = clone $this->actionColumn;
                $actionColumn->row($data);
                $row['EadminAction'] = $actionColumn;
            }
            $tableData[] = $row;
        }
        $field = Str::random(15, 3);
        $this->bind($field, $tableData);
        $this->bindAttr('data', $field);
        return $tableData;
    }


    public function jsonSerialize()
    {
        //添加按钮
        if (!$this->hideAddButton) {
            $form = $this->formAction->form();
            $form->eventSuccess([$this->bindAttr('modelValue') => true, $form->bindAttr('model') => $form->getCallMethod()]);
            $button = Button::create('添加')
                ->type('primary')
                ->size('small')
                ->icon('el-icon-plus');
            $action = clone $this->formAction->component();
            if ($action instanceof Router) {
                $button = $action->content($button)->to("/eadmin/create.rest", $form->getCallMethod());
            } else {
                $button = $action->bindValue(null, false)->reference($button)->title($form->bind('eadmin_title'))->content($form);
            }
            $this->attr('addButton', $button);
        }
        //工具栏
        $this->attr('tools',$this->tools);
        //快捷搜索
        $keyword = Request::get('quickSearch', '', ['trim']);
        $this->drive->quickFilter($keyword, $this->column);
        //查询视图
        if (!is_null($this->filter)) {
            $form = $this->filter->render();
            $form->eventSuccess([$this->bindAttr('modelValue') => true]);
            $this->attr('filter', $form);
            $this->attr('filterField', $form->bindAttr('model'));
        }
        //总条数
        $this->pagination->total($this->drive->getTotal());
        //是否分页
        $page = Request::get('page', 1);
        $size = Request::get('size', $this->pagination->attr('pageSize'));
        if (!$this->hidePage) {
            $this->attr('pagination', $this->pagination->attribute);
        }
        $data = $this->drive->getData($this->hidePage, $page, $size);
        //解析列
        $data = $this->parseColumn($data);

        if (request()->has('ajax_request_data')) {

            return ['code' => 200, 'data' => $data, 'total' => $this->pagination->attr('total')];
        } else {
            $params = (array)$this->attr('params');
            $this->params(array_merge($params, $this->getCallMethod()));
            $this->attr('columns', array_column($this->column, 'attribute'));
            return parent::jsonSerialize(); // TODO: Change the autogenerated stub
        }
    }
}
