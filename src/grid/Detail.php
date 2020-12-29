<?php


namespace Eadmin\grid;


use think\exception\HttpResponseException;
use think\facade\Request;
use think\Model;
use think\model\Collection;
use think\model\relation\HasMany;
use Eadmin\facade\Component;
use Eadmin\layout\Card;
use Eadmin\View;

class Detail extends View
{
    //当前模型
    protected $model;

    //当前模型的数据库查询对象
    protected $db;

    //数据
    protected $data = [];

    //表字段
    protected $tableFields = [];

    //列
    protected $columns = [];
    protected $cellComponent=[];
    protected $component=[];
    protected $scriptArr = [];
    protected $manyColumnHtml = '';
    protected $columnHtml = '';
    protected $title = '';
    public function __construct(Model $model)
    {
        $this->template = 'detail';
        $this->model = $model;
        $this->db = $this->model->db();
        $this->tableFields = $this->model->getTableFields();
        $this->setTitle('详情');
        $this->detailData(Request::param('id'));
    }

    /**
     * 设置详情数据
     * @param int $id 详情id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function detailData($id){
        if ($id) {
            $this->id = $id;
            $this->data = $this->model->find($id);
            if (empty($this->data)) {
                throw new HttpResponseException(json(['code' => 0, 'message' => '数据不存在！', 'data' => []]));
            }
        }
        return $this;
    }
    public function data(){
        return $this->data;
    }
    /**
     * 设置标题
     * @param $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
        $this->setVar('title', $title);
    }
    public function title(){
        return $this->title;
    }
    /**
     * 布局
     * @param string $title 标题
     * @param int $md 占列
     * @param \Closure $closure
     * @return $this
     */
    public function layout($title, $md, \Closure $closure)
    {
        array_push($this->columns, ['type' => 'layout', 'title' => $title, 'md' => $md, 'closure' => $closure]);
        return $this;
    }
    /**
     * 设置列
     * @Author: rocky
     * 2019/7/25 16:20
     * @param $field 字段
     * @param $label 标签
     * @return Column
     */
    public function column($field, $label)
    {
        $column = new Column($field, $label);
        array_push($this->columns, $column);
        return $column;
    }

    /**
     * 一对多
     * @param $relationMethod 一对多方法
     * @param $title 标题
     * @param $md 占列
     * @param \Closure $closure
     * @return $this
     */
    public function hasMany($relationMethod,$title, $md,\Closure $closure)
    {
        array_push($this->columns, ['type' => 'hasMany', 'title' => $title, 'md' => $md,'relationMethod'=>$relationMethod,  'closure' => $closure]);
        return $this;
    }
    /**
     * 解析一对多
     * @Author: rocky
     * 2019/8/1 15:00
     * @param $relationMethod 一对多关联方法
     * @return string
     */
    private function parsehasManData($relationMethod){
        foreach ($this->data->$relationMethod as $rowIndex=>$val){
            foreach ($this->columns as $column) {
                $column->setData($val);
            }
        }
        if($this->data->$relationMethod instanceof Collection){
            $table = new Table($this->columns, $this->data->$relationMethod->toArray());
        }else{
            $table = new Table($this->columns, $this->data->$relationMethod);
        }
        return $table->view();
    }

    /**
     * 解析布局
     * @param string $title 标题
     * @param int $md 占列
     * @return string
     */
    private function paseLayout($title,$md){
        $card = new Card();
        $card->setAttr(':body-style','{padding: "0px 15px" }');
        $card->header("<span style='font-weight: bold'>{$title}</span>");
        $html = $this->parseColumn();
        $card->body($html);
        return "<el-col :span='{$md}'>{$card->render()}</el-col>";
    }
    protected function parseColumn(){
        $columnHtml = '';
		static $index = 0;
        foreach ($this->columns as $i=>$column) {
            if($column instanceof Column){
                $column->setData($this->data);
                $this->cellComponent[] = $column->getDetailDisplay($index);
				$index++;
                $columnHtml .= $column->detailRender();
                $this->scriptArr = array_merge($this->scriptArr, $column->getScriptVar());
            }elseif($column['type'] == 'hasMany'){
                $columnsArr = array_slice($this->columns, $i + 1);
                $this->columns = [];
                $card = new Card();
                $card->setAttr(':body-style','{padding: "0px 15px" }');
                call_user_func($column['closure'], $this);
                $component = $this->parsehasManData($column['relationMethod']);
                $componentKey = 'component'.mt_rand(10000,99999);
                $this->component[$componentKey] = "() => new Promise(resolve => {
                            resolve(this.\$splitCode(decodeURIComponent('".rawurlencode($component)."')))
                        })";
                $card->header("<span style='font-weight: bold'>{$column['title']}</span>");
                $card->body('<component :is="'.$componentKey.'" />');
                $this->manyColumnHtml .= "<el-col :span='{$column['md']}'>{$card->render()}</el-col>";

                $this->columns = $columnsArr;
            }elseif($column['type'] == 'layout'){
                $columnsArr = array_slice($this->columns, $i + 1);
                $this->columns = [];
                call_user_func($column['closure'], $this);
                $this->manyColumnHtml .= $this->paseLayout($column['title'],$column['md']);
                $this->columns = $columnsArr;
            }
        }
        return $columnHtml;
    }
    public function view(){
        $this->columnHtml = $this->parseColumn();
        $columnScriptVar = implode(',', $this->scriptArr);
        list($attrStr, $scriptVar) = $this->parseAttr();
        if (!empty($columnScriptVar)) {
            $scriptVar = $scriptVar . ',' . $columnScriptVar;
        }
        $this->setVar('data',json_encode($this->data,JSON_UNESCAPED_UNICODE));
        $this->setVar('cellComponent', json_encode($this->cellComponent, JSON_UNESCAPED_UNICODE));
        foreach ($this->component as $key=>$value){
            $scriptVar .= "$key:$value,";
        }
        $this->setVar('html', $this->columnHtml);
        $this->setVar('manyColumnHtml', $this->manyColumnHtml);
        $this->setVar('scriptVar', $scriptVar);
        if (Request::has('build_dialog')) {
            $this->setVar('title', '');
            Component::view($this->render());
        }
        return $this->render();
    }
    //头像昵称列
    public function userInfo($headimg = 'headimg', $nickname = 'nickname',$label = '')
    {
        $column = $this->column($headimg, $label);
        return $column->display(function ($val, $data) use ($column, $nickname) {
            $nicknameValue = $column->getValue($data, $nickname);
            $html = <<<EOF
<el-card style="margin-top: 10px;" :body-style="{padding: '10px'}">
 <div style='text-align: center;line-height: 25px'><el-image style='width: 80px; height: 80px;border-radius: 50%' src='{$val}' fit='cover' :preview-src-list='["{$val}"]' lazy></el-image><br>{$nicknameValue}</div>
</el-card>
EOF;
            return $html;
        });
    }
}
