<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-05-14
 * Time: 22:00
 */

namespace Eadmin\grid;

use think\db\Query;
use think\facade\Db;
use think\Model;
use think\model\Relation;
use think\model\relation\BelongsTo;
use think\model\relation\HasMany;
use think\model\relation\HasOne;
use think\model\relation\MorphMany;
use think\model\relation\MorphOne;
use Eadmin\form\field\Input;
use Eadmin\View;

class Filter extends View
{
    public $formItem = [];
    public $scriptArr = [];
    //模型
    protected $model;
    //当前模型db
    protected $db;
    //

    protected $fields = [];
    protected $mode = 'filter';
    protected $columnLabel = '';
    protected $usingData = [];
    protected $jsonNode = '';
    protected $relationExistSql = '';
    protected $ifWhere = true;
    protected $relationLastDb = null;
    protected $relationLastMethod = '';
    public function __construct($model)
    {
        if ($model instanceof Model) {
            $this->model = $model;
            $this->db = $this->model->db();
        } elseif ($model instanceof Query) {
            $this->db = $model;
            $this->model = $model->getModel();
        } else {
            $this->db = Db::name($model);
        }
        $this->tableFields = $this->db->getTableFields();
    }
    public function label($value){
        $this->columnLabel  = $value;
    }
    /**
     * 筛选模式
     * @param string $mode
     * @return mixed
     */
    public function mode(string $mode = '')
    {
        if(!empty($mode)){
            $this->mode = $mode;
        }
        return $this->mode;
    }

    public function __call($name, $arguments)
    {
        if (count($this->formItem) > 0) {
            $formItem = end($this->formItem);
            call_user_func_array([$formItem, $name], $arguments);
        }
    }
    /**
     * 模糊查询
     * @param string $field 字段
     * @param string $label 标签
     * @return $this
     */
    public function like($field, $label = '')
    {
        $this->paseFilter(__FUNCTION__, $field);
        $this->formItem($field, $label);
        return $this;
    }
    /**
     * json查询
     * @param string $field 字段
     * @param string $node json属性字段
     * @param string $label 标签
     * @return $this
     */
    public function json($field,$node, $label = '')
    {
        $this->jsonNode = $node;
        $this->paseFilter(__FUNCTION__, $field);
        $this->formItem($field.'__json_'.$node, $label);
        return $this;
    }
    /**
     * json模糊查询
     * @param string $field 字段
     * @param string $node json属性字段
     * @param string $label 标签
     * @return $this
     */
    public function jsonLike($field, $node,$label = '')
    {
        $this->jsonNode = $node;
        $this->paseFilter(__FUNCTION__, $field);
        $this->formItem($field.'__json_'.$node, $label);
        return $this;
    }
    /**
     * json数组模糊查询
     * @param string $field 字段
     * @param string $node json属性字段
     * @param string $label 标签
     * @return $this
     */
    public function jsonArrLike($field, $node,$label = ''){
        $this->jsonNode = $node;
        $this->paseFilter(__FUNCTION__, $field);
        $this->formItem($field.'__json_'.$node, $label);
        return $this;
    }
    /**
     * in查询
     * @param string $field 字段
     * @param string $label 标签
     * @return $this
     */
    public function in($field, $label = '')
    {
        $this->paseFilter(__FUNCTION__, $field);
        $this->formItem($field, $label);
        return $this;
    }

    /**
     * not in查询
     * @param string $field 字段
     * @param string $label 标签
     * @return $this
     */
    public function notIn($field, $label = '')
    {
        $this->paseFilter(__FUNCTION__, $field);
        $this->formItem($field, $label);
        return $this;
    }

    /**
     * 等于查询
     * @param string $field 字段
     * @param string $label 标签
     * @return $this
     */
    public function eq($field, $label = '')
    {
        $this->paseFilter(__FUNCTION__, $field);
        $this->formItem($field, $label);
        return $this;
    }

    /**
     * findIn查询
     * @param string $field 字段
     * @param string $label 标签
     * @return $this
     */
    public function findIn($field, $label = '')
    {
        $this->paseFilter(__FUNCTION__, $field);
        $this->formItem($field, $label);
        return $this;
    }

    /**
     * 不等于查询
     * @param string $field 字段
     * @param string $label 标签
     * @return $this
     */
    public function neq($field, $label = '')
    {
        $this->paseFilter(__FUNCTION__, $field);
        $this->formItem($field, $label)->prepend('不等于');
        return $this;
    }

    /**
     * 大于等于
     * @param string $field 字段
     * @param string $label 标签
     * @return $this
     */
    public function egt($field, $label = '')
    {
        $this->paseFilter(__FUNCTION__, $field);
        $this->formItem($field, $label)->prepend('大于等于');
        return $this;
    }

    /**
     * 大于
     * @param string $field 字段
     * @param string $label 标签
     * @return $this
     */
    public function gt($field, $label = '')
    {
        $this->paseFilter(__FUNCTION__, $field);
        $this->formItem($field, $label)->prepend('大于');
        return $this;
    }

    /**
     * 小于等于
     * @param string $field 字段
     * @param string $label 标签
     * @return $this
     */
    public function elt($field, $label = '')
    {
        $this->paseFilter(__FUNCTION__, $field);
        $this->formItem($field, $label)->prepend('小于等于');
        return $this;
    }

    /**
     * 大于
     * @param string $field 字段
     * @param string $label 标签
     * @return $this
     */
    public function lt($field, $label = '')
    {
        $this->paseFilter(__FUNCTION__, $field);
        $this->formItem($field, $label)->prepend('小于');
        return $this;
    }

    /**
     * 区间查询
     * @param string $field 字段
     * @param string $label 标签
     * @return $this
     */
    public function between($field, $label = '')
    {
        $this->paseFilter(__FUNCTION__, $field);
        $this->formItem($field . '__between_start', $label)->placeholder("请输入开始$label");
        $this->formItem($field . '__between_end', '-')->placeholder("请输入结束$label");
        return $this;
    }

    /**
     * 日期筛选
     * @param string $field 字段
     * @param string $label 标签
     * @return \Eadmin\form\field\DateTime|mixed
     */
    public function date($field, $label = '')
    {
        $this->paseFilter('eq', $field);
        $this->formItem($field, $label, 'DateTime');
        return $this;
    }

    /**
     * 时间筛选
     * @param string $field 字段
     * @param string $label 标签
     * @return \Eadmin\form\field\DateTime|mixed
     */
    public function time($field, $label = '')
    {
        $this->paseFilter('eq', $field);
        $formItem = $this->formItem($field, $label, 'DateTime');
        $formItem->setType('time');
        return $formItem;
    }

    /**
     * 日期时间筛选
     * @param string $field 字段
     * @param string $label 标签
     * @return \Eadmin\form\field\DateTime|mixed
     */
    public function datetime($field, $label = '')
    {
        $this->paseFilter('eq', $field);
        $formItem = $this->formItem($field, $label, 'DateTime');
        $formItem->setType('datetime');
        return $formItem;
    }

    /**
     * 日期时间范围筛选
     * @param string $field 字段
     * @param string $label 标签
     * @return \Eadmin\form\field\DateTime|mixed
     */
    public function datetimeRange($field, $label = '')
    {
        $this->paseFilter('dateBetween', $field);
        $formItem = $this->formItem($field, $label, 'DateTime');
        $formItem->setType('datetime')->range();
        return $formItem;
    }

    /**
     * 级联筛选
     * @param ...$field 字段1,字段2,字段3...
     * @param $label 标签
     * @return \Eadmin\form\field\Cascader|mixed
     */
    public function cascader(...$field)
    {
        $label = array_pop($field);
        $this->paseFilter('cascader', $field);
        $formItem = $this->formItem($field[0], $label, 'cascader', array_slice($field, 1));
        return $formItem;
    }

    /**
     * 日期范围筛选
     * @param string $field 字段
     * @param string $label 标签
     * @return \Eadmin\form\field\DateTime|mixed
     */
    public function dateRange($field, $label = '')
    {
        $this->paseFilter('dateBetween', $field);
        $formItem = $this->formItem($field, $label, 'DateTime');
        $formItem->setType('date')->range();
        return $formItem;
    }

    /**
     * 时间范围筛选
     * @param string $field 字段
     * @param string $label 标签
     * @return \Eadmin\form\field\DateTime|mixed
     */
    public function timeRange($field, $label = '')
    {
        $this->paseFilter('dateBetween', $field);
        $formItem = $this->formItem($field, $label, 'DateTime');
        $formItem->setType('time')->range();
        return $formItem;
    }

    /**
     * 年日期筛选
     * @param string $field 字段
     * @param string $label 标签
     * @return \Eadmin\form\field\DateTime|mixed
     */
    public function year($field, $label = '')
    {
        $this->paseFilter(__FUNCTION__, $field);
        $formItem = $this->formItem($field, $label, 'DateTime');
        $formItem->setType(__FUNCTION__);
        return $formItem;
    }

    /**
     * 月日期筛选
     * @param string $field 字段
     * @param string $label 标签
     * @return \Eadmin\form\field\DateTime|mixed
     */
    public function month($field, $label = '')
    {
        $this->paseFilter(__FUNCTION__, $field);
        $formItem = $this->formItem($field, $label, 'DateTime');
        $formItem->setType(__FUNCTION__);
        return $formItem;
    }

    /**
     * NOT区间查询
     * @param string $field 字段
     * @param string $label 标签
     * @return $this
     */
    public function notBetween($field, $label = '')
    {
        $this->paseFilter(__FUNCTION__, $field);
        $this->formItem($field . '__between_start', $label = '')->prepend('不存在区间');
        $this->formItem($field . '__between_end', '-')->placeholder("请输入$label");
        return $this;
    }

    /**
     * 单选框
     * @param array $options 选项值
     * @return \Eadmin\form\field\Radio|mixed
     */
    public function radio(array $options)
    {
        $this->usingData = $options;
        $formItem = array_pop($this->formItem);
        $formItem = $this->formItem($formItem->field, $formItem->label, 'radio');
        if ($this->mode == 'column') {
            $formItem->vertical();
        }
        $formItem->options($options);
        return $formItem;
    }

    /**
     * 多选框
     * @param array $options 选项值
     * @return \Eadmin\form\field\Checkbox|mixed
     */
    public function checkbox(array $options)
    {
        $this->usingData = $options;
        $formItem = array_pop($this->formItem);
        $formItem = $this->formItem($formItem->field, $formItem->label, 'checkbox');
        if ($this->mode == 'column') {
            $formItem->vertical();
        }
        $formItem->options($options);
        return $formItem;
    }

    /**
     * 分组下拉框
     * @param array $options 选项值
     * @return \Eadmin\form\field\Select|mixed
     */
    public function selectGroup(array $options)
    {
        $this->usingData = $options;
        $formItem = array_pop($this->formItem);
        $formItem = $this->formItem($formItem->field, $formItem->label, 'select');
        $formItem->groupOptions($options);
        return $formItem;
    }

    /**
     * 下拉框
     * @param array $options 选项值
     * @return \Eadmin\form\field\Select|mixed
     */
    public function select(array $options)
    {
        $this->usingData = $options;
        $formItem = array_pop($this->formItem);
        $formItem = $this->formItem($formItem->field, $formItem->label, 'select');
        if ($this->mode == 'column') {
            $formItem->width();
        }
        $formItem->options($options);
        return $formItem;
    }


    /**
     * 添加表单元素
     * @param string $name 组件类
     * @param string $field 字段
     * @param string $label 标签
     * @param array $arguments
     * @return \Eadmin\form\field\Input
     */
    protected function formItem($field, $label, $name = 'input', $arguments = [])
    {
        $class = "Eadmin\\form\\field\\" . ucfirst($name);
        $field = str_replace('.', '__', $field);
        $formItem = new $class($field, $label, $arguments);
        if ($formItem instanceof Input) {
            $formItem->prefixIcon('el-icon-search');
        }
        $this->formItem[] = $formItem;
        if ($name == 'checkbox') {
            $this->fields[$field] = [];
        } else {
            $this->fields[$field] = '';
        }
        if ($this->mode == 'column' && $name == 'input') {
            $formItem->placeholder('搜索 按Enter确认');
        }
        $this->usingData = json_encode($this->usingData);

        if(!empty($label) && strpos($field,'__between_end') === false){
            $this->columnLabel = $label;
        }
        $formItem->setAttr('@change',"(e)=>filterColumnChange(e,\"{$field}\",\"{$this->columnLabel}\",\"{$name}\",{$this->usingData})");
        $this->usingData = [];
        return $formItem;
    }

    /**
     * 解析查询过滤
     * @param string $method 方法
     * @param string $field 字段
     * @return mixed
     */
    public function paseFilter($method, $field)
    {
        if (is_string($field)) {
            $field = str_replace('.', '__', $field);
            $fields = explode('__', $field);
            $dbField = array_pop($fields);
            if (count($fields) > 0) {

                $func = function (Filter $filter) use ($dbField, $field, $method) {
                    $filter->filterField($method, $dbField, $field);
                };
                while(count($fields) > 1){
                    $relation = array_pop($fields);
                    $func = function (Filter $filter) use($relation,$func,$dbField) {
                        $filter->relationWhere($relation, $func);
                    };
                }
                $relation = array_pop($fields);
                return $this->relationWhere($relation,$func);
            }
            $requestField = $field;
        } elseif (is_array($field)) {
            $dbField = $field;
            $requestField = array_shift($field);
        }
        $this->filterField($method, $dbField, $requestField);
    }

    /**
     * 查询过滤
     * @param string $method 方法
     * @param string $dbField 数据库字段
     * @param string $field 请求数据字段
     * @param string $request 请求方式
     * @return $this
     */
    private function filterField($method, $dbField, $field = null, $request = 'get')
    {
        if (is_null($field)) {
            $field = $dbField;
        }

        $data = request()->$request();
        if ($method == 'between' || $method == 'notBetween') {
            $field .= '__between_start';
        }
        if ($method == 'json' || $method == 'jsonLike' || $method == 'jsonArrLike') {
            $field .= '__json_'.$this->jsonNode;
        }
        if (is_array($dbField)) {
            $dbFields = $dbField;
        } else {
            $dbFields[] = $dbField;
        }

        $whereOr = [];
        foreach ($dbFields as $f) {
            if (isset($data[$field]) && $data[$field] !== '') {
                if (is_array($data[$field]) && $method == 'cascader') {
                    $value = array_shift($data[$field]);
                    if (is_null($value)) {
                        continue;
                    }
                    $fieldData[$field] = $value;
                    $res = json_decode($fieldData[$field], true);
                    if (!is_null($res)) {
                        $fieldData[$field] = $res;
                    }
                    if (is_array($fieldData[$field])) {
                        $where = [];
                        foreach ($fieldData[$field] as $index => $value) {
                            $where[] = [$dbFields[$index], '=', $value];
                        }
                        $whereOr[] = $where;
                        continue;
                    }
                } else {
                    $fieldData = $data;
                }
                $this->parseRule($method, $f, $field, $fieldData);
            }
        }
        if (!empty($whereOr)) {
            $fieldData[$field] = $whereOr;
            $this->parseRule($method, $f, $field, $fieldData);
        }
        return $this;
    }

    /**
     * @param string $method
     * @param string $dbField
     * @param string $field
     * @param $data
     */
    private function parseRule($method, $dbField, $field, $data): void
    {
        if (in_array($dbField, $this->tableFields)) {
            switch ($method) {
                case 'year':
                    $this->db->whereYear($dbField, $data[$field]);
                    break;
                case 'month':
                    $this->db->whereMonth($dbField, $data[$field]);
                    break;
                case 'dateBetween':
                    list($startTime, $endTime) = $data[$field];
                    $this->db->whereBetween($dbField, [$startTime, $endTime]);
                    break;
                case 'between':
                    $betweenStart = $data[$field];
                    $field = str_replace('__between_start', '__between_end', $field);
                    $betweenEnd = $data[$field];
                    $this->db->whereBetween($dbField, [$betweenStart, $betweenEnd]);
                    break;
                case 'notBetween':
                    $betweenStart = $data[$field];
                    $field = str_replace('__between_start', '__between_end', $field);
                    $betweenEnd = $data[$field];
                    $this->db->whereNotBetween($dbField, [$betweenStart, $betweenEnd]);
                    break;
                case 'like':
                    $this->db->whereLike($dbField, "%$data[$field]%");
                    break;
                case 'eq':
                    $this->db->where($dbField, $data[$field]);
                    break;
                case 'cascader':
                    if (is_array($data[$field])) {
                        $this->db->where(function ($q) use ($data, $field) {
                            $q->whereOr($data[$field]);
                        });
                    } else {
                        $this->db->where($dbField, $data[$field]);
                    }
                    break;
                case 'neq':
                    $this->db->where($dbField, '<>', $data[$field]);
                    break;
                case 'egt':
                    $this->db->where($dbField, '>=', $data[$field]);
                    break;
                case 'gt':
                    $this->db->where($dbField, '>', $data[$field]);
                    break;
                case 'elt':
                    $this->db->where($dbField, '<=', $data[$field]);
                    break;
                case 'lt':
                    $this->db->where($dbField, '<', $data[$field]);
                    break;
                case 'findIn':
                    $this->db->whereFindInSet($dbField, $data[$field]);
                    break;
                case 'in':
                    $this->db->whereIn($dbField, $data[$field]);
                    break;
                case 'notIn':
                    $this->db->whereNotIn($dbField, $data[$field]);
                    break;
                case 'json':
                    $this->db->whereRaw("JSON_EXTRACT({$dbField},'$.{$this->jsonNode}') = '{$data[$field]}'");
                    break;
                case 'jsonLike':
                    $this->db->whereRaw("JSON_EXTRACT({$dbField},'$.{$this->jsonNode}') LIKE \"%{$data[$field]}%\"");
                    break;
                case 'jsonArrLike':
                    $this->db->whereRaw("JSON_EXTRACT({$dbField},'$[*].{$this->jsonNode}') LIKE \"%{$data[$field]}%\"");
                    break;
            }
        }
    }

    /**
     * 关联查询
     * @param $relation_method 关联方法
     * @param $callback
     * @return $this
     * @throws \think\exception\DbException
     */
    protected function relationWhere($relation_method, $callback)
    {
        if (method_exists($this->model, $relation_method)) {
            $relation = $this->model->$relation_method();
            if ($relation instanceof Relation) {
                $relationModel = get_class($relation->getModel());
                $relation_table = $relation->getTable();
                $foreignKey = $relation->getForeignKey();
                $pk = $relation->getLocalKey();
                if ($callback instanceof \Closure) {
                    $this->relationModel = new self(new $relationModel);
                    $this->relationModel->relationLastDb($this->relationLastDb,$this->relationLastMethod);
                    $this->relationModel->setRelationLastDb($relation_method);
                    $this->relationModel->setIfWhere($this->ifWhere);
                    call_user_func($callback, $this->relationModel);
                }
                $tmpDb = clone $this->relationModel->db();
                $relationSql = $tmpDb->removeWhereField('delete_time')->buildSql();
                $res = strpos($relationSql, 'WHERE');
                if ($relation instanceof HasMany) {
                    $sql = $this->relationModel->db()->whereRaw("{$relation_table}.{$foreignKey}={$this->db->getTable()}.{$pk}")->buildSql();
                } elseif ($relation instanceof BelongsTo) {
                    $sql = $this->relationModel->db()->whereRaw("{$pk}={$this->db->getTable()}.{$foreignKey}")->buildSql();
                } else if ($relation instanceof HasOne) {
                    $sql = $this->relationModel->db()->whereRaw("{$foreignKey}={$this->db->getTable()}.{$pk}")->buildSql();
                } else if ($relation instanceof MorphOne || $relation instanceof MorphMany) {
                    $reflectionClass = new \ReflectionClass($relation);
                    $propertys = ['morphKey','morphType','type'];
                    $propertyValues = [];
                    foreach ($propertys as $var){
                        $property = $reflectionClass->getProperty($var);
                        $property->setAccessible(true);
                        $propertyValues[] =  $property->getValue($relation);
                    }
                    list($morphKey,$morphType,$typeValue) = $propertyValues;
                    $sql = $this->relationModel->db()->whereRaw("{$morphKey}={$this->db->getTable()}.{$this->db->getPk()}")->where($morphType,$typeValue)->buildSql();
                }
                $this->relationExistSql = $sql;
                if ($res !== false || $this->ifWhere == false) {
                    $this->db->whereExists($sql);
                }
            }
        }
        return $this;
    }
    public function setRelationLastDb($method){
        if($this->relationLastMethod == $method){
            $this->db = $this->relationLastDb;
        }
    }
    public function relationLastDb($db,$method){
        $this->relationLastDb = $db;
        $this->relationLastMethod  = $method;
    }
    public function setIfWhere(bool $bool){
        $this->ifWhere = $bool;
    }
    public function getRelationExistSql(){
        return $this->relationExistSql;
    }

    /**
     * 返回db对象
     * @return Db
     */
    public function db()
    {
        return $this->db;
    }

    public function render()
    {
        $formItemHtml = '';
        foreach ($this->formItem as $formItem) {
            if ($this->mode == 'column') {
                $formItemTmp = "<el-form-item prop='{$formItem->field}'>" . $formItem->render() . "</el-form-item>";
            } else {
                $formItemTmp = "<el-form-item label='{$formItem->label}' prop='{$formItem->field}'>" . $formItem->render() . "</el-form-item>";
            }
            if (!empty($formItem->md)) {
                $formItemHtml .= sprintf($formItem->md, $formItemTmp);
            } else {
                $formItemHtml .= $formItemTmp;
            }
            $this->scriptArr = array_merge($this->scriptArr, $formItem->getScriptVar());
        }
        if ($this->mode == 'column') {
            $this->formItem = [];
        }
        array_push($this->scriptArr, "form:" . json_encode($this->fields, JSON_UNESCAPED_UNICODE));

        return $formItemHtml;
    }


}
