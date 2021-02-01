<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2021-01-12
 * Time: 23:43
 */
namespace Eadmin\grid\drive;


use Eadmin\contract\GridInterface;
use think\facade\Db;
use think\facade\Request;
use think\model\relation\BelongsTo;
use think\model\relation\BelongsToMany;
use think\model\relation\HasMany;
use think\model\relation\HasOne;
use think\model\relation\MorphMany;
use think\model\relation\MorphOne;
class Model implements GridInterface
{
    //模型
    protected $model;
    //主键字段
    protected $pkField;
    //数据源
    protected $data = [];
    //当前模型的数据库查询对象
    protected $db;
    //关联
    protected $relations = [];
    //表字段
    protected $tableFields = [];
    //软删除字段
    protected $softDeleteField = 'delete_time';

    //是否开启软删除
    protected $isSotfDelete = false;
    //排序字段
    protected $sortField = 'sort';
    //删除前回调
    protected $beforeDel = null;
    public function __construct($model){
        $this->model = $model;
        $this->db = $this->model->db();
        $this->tableFields = $this->model->getTableFields();
        $this->pkField = $this->model->getPk();
    }
    //预关联加载
    protected function withRelations()
    {
        $this->relations = array_unique($this->relations);
        $with = $this->db->getOptions('with');
        if (is_null($with)) {
            $with = [];
        }
        $with = array_merge($with, $this->relations);
        if (count($with) > 0) {
            $this->db->with($with);
        }
    }

    /**
     * 解析关联方法设置
     * @param $realiton 关联方法
     */
    protected function realiton($relation){
        $fields = explode('.', $relation);
        if (count($fields) > 1) {
            array_pop($fields);
            $relation = implode('.', $fields);
            foreach ($fields as $field) {
                $this->setRelation($field, $relation);
            }
        } else {
            $this->setRelation($relation, $relation);
        }
    }
    public function getData(bool $hidePage,int $page, int $size)
    {
        if($hidePage){
            return $this->db->select();;
        }else{
            return $this->db->page($page, $size)->select();
        }
    }
    public function getPk()
    {
        return $this->pkField;
    }
    //获取数据总条数
    public function getTotal(): int
    {
        if($this->db){
            $sql = $this->db->buildSql();
            $sql = "SELECT COUNT(*) FROM {$sql} userCount";
            $res = \think\facade\Db::query($sql);
            $count = $res[0]['COUNT(*)'];
        }else{
            $count = count($this->data);
        }
        return $count;
    }
    /**
     * 设置预加载关联
     * @param $ifRelation 关联方法名称
     * @param $relation 关联方法
     */
    private function setRelation($ifRelation, $relation)
    {
        if (method_exists($this->model, $ifRelation) &&
            ($this->model->$ifRelation() instanceof BelongsTo ||
                $this->model->$ifRelation() instanceof HasOne ||
                $this->model->$ifRelation() instanceof HasMany ||
                $this->model->$ifRelation() instanceof MorphOne ||
                $this->model->$ifRelation() instanceof MorphMany)) {
            $this->relations[] = $relation;
        }
    }
    //快捷搜索
    public function quickFilter($keyword,$columns)
    {
        if ($keyword) {
            $whereFields = [];
            $whereOr = [];
            $relationWhereFields = [];
            $relationWhereOr = [];
            foreach ($columns as $column) {
                $field = $column->getField();
                $fields = explode('.', $field);
                $field = end($fields);
                $usings = $column->getUsing();
                if (count($fields) > 1) {
                    array_pop($fields);
                    $relation = array_pop($fields);;
                    if (empty($usings)) {
                        $relationWhereFields[$relation][] = $field;
                    } else {
                        foreach ($usings as $key => $value) {
                            if (strpos($value, $keyword) !== false) {
                                $relationWhereOr[$relation][$field] = $key;
                            }
                        }
                    }
                } else {
                    if (in_array($column->getField(), $this->tableFields)) {
                        if (empty($usings)) {
                            $whereFields[] = $field;
                        } else {
                            foreach ($usings as $key => $value) {
                                if (stripos($value, $keyword) !== false) {
                                    $whereOr[$field] = $key;
                                }
                            }
                        }
                    }
                }
            }
            //快捷搜索
            $relationWhereSqls = [];
            foreach ($this->relations as $relationName) {
                $relations = explode('.', $relationName);
                $Tmprelations = $relations;
                $relation = array_pop($Tmprelations);
                $relationFilter = implode('.', $relations);
                $model = get_class($this->model);
                $filter = new Filter(new $model);
                $filter->setIfWhere(false);
                $db = $this->model;
                foreach ($relations as $relation) {
                    $db = $db->getModel()->$relation();
                }
                $filter->relationLastDb($db,$relation);
                $relationName = $relation;
                $relationTableFields = $db->getTableFields();
                if (isset($relationWhereFields[$relationName])) {
                    $relationWhereFields[$relationName] = array_intersect($relationWhereFields[$relationName], $relationTableFields);
                    $fields = implode('|', $relationWhereFields[$relationName]);
                    $relationWhereCondtion = $relationWhereOr[$relationName] ?? [];
                    $db->where(function ($q) use ($fields, $keyword, $relationWhereCondtion) {
                        foreach ($relationWhereCondtion as $field => $value) {
                            $q->whereOr($field, $value);
                        }
                        $q->whereLike($fields, "%{$keyword}%", 'OR');
                    });
                    $filter->paseFilter(null, $relationFilter . '.');
                    $wheres = $filter->db()->getOptions('where');
                    foreach ($wheres['AND'] as $where){
                        if($where[1] == 'EXISTS'){
                            $relationWhereSqls[]  = $where[2];
                            break;
                        }
                    }
                }
            }
            $fields = implode('|', $whereFields);
            $this->db->where(function ($q) use ($relationWhereSqls, $fields, $keyword, $whereOr) {
                $q->whereLike($fields, "%{$keyword}%", 'OR');
                foreach ($whereOr as $field => $value) {
                    $q->whereOr($field, $value);
                }
                foreach ($relationWhereSqls as $sql) {
                    $q->whereExists($sql, 'OR');
                }
            });
        }
    }
    /**
     * 更新数据
     * @param array $ids 更新条件id
     * @param array $data 更新数据
     * @return Model
     */
    public function update(array $ids,array $data)
    {
        $action = isset($data['action']) ? $data['action'] : '';
        if ($action == 'eadmin_sort') {
            $field = "id,(@rownum := @rownum+1),case when @rownum = {$data['sort']} then @rownum := @rownum+1 else @rownum := @rownum end AS rownum";
            $sortSql = $this->db->table("(SELECT @rownum := -1) r," . $this->model->getTable())
                ->fieldRaw($field)
                ->removeOption('order')
                ->order($this->sortField)
                ->where('id', '<>', $data['id'])
                ->buildSql();
            $this->model->where($this->model->getPk(), $data['id'])->update([$this->sortField => $data['sort']]);
            $res = Db::execute("update {$this->model->getTable()} inner join {$sortSql} a on a.id={$this->model->getTable()}.id set {$this->sortField}=a.rownum");
            admin_success('操作完成','排序成功');
        } else {
            $res = $this->model->removeWhereField($this->softDeleteField)->strict(false)->whereIn($this->model->getPk(), $ids)->update($data);
            if ($res) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * @param string $sortField
     */
    public function sortField(string $sortField): void
    {
        $this->sortField = $sortField;
    }
    /**
     * 删除数据
     */
    public function destroy($ids)
    {
        $trueDelete = Request::delete('trueDelete');
        
        $res = false;
        Db::startTrans();
        try {
            $this->db->removeWhereField($this->softDeleteField);
            if ($ids === true) {
                if ($this->isSotfDelete && !$trueDelete) {
                    $res = $this->db->where('1=1')->update([$this->softDeleteField => date('Y-m-d H:i:s')]);
                } else {
                    if (in_array($this->softDeleteField, $this->tableFields)) {
                        $deleteDatas = $this->db->whereNotNull($this->softDeleteField)->select();
                        $this->deleteRelationData($deleteDatas);
                        $res = $this->db->whereNotNull($this->softDeleteField)->delete();
                    } else {
                        $deleteDatas = $this->model->select();
                        $this->deleteRelationData($deleteDatas);
                        $res = $this->db->where('1=1')->delete();
                    }
                }
            } else {
                if ($this->isSotfDelete && !$trueDelete) {
                    $res = Db::name($this->model->getTable())->whereIn($this->model->getPk(), $ids)->update([$this->softDeleteField => date('Y-m-d H:i:s')]);
                } else {
                    if ($ids === true) {
                        $this->deleteRelationData(true);
                    } else {
                        $deleteDatas = $this->model->removeOption('where')->whereIn($this->model->getPk(), $ids)->select();
                        $this->deleteRelationData($deleteDatas);
                    }
                    $res = Db::name($this->model->getTable())->delete($ids);
                }
            }
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            $res = false;
        }
        return $res;
    }

    /**
     * 删除关联数据
     * @param $deleteDatas
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function deleteRelationData($deleteDatas)
    {
        $reflection = new \ReflectionClass($this->model);
        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
        $className = $reflection->getName();
        $relatonMethod = [];

        foreach ($methods as $method) {
            if ($method->class == $className) {
                $relation = $method->name;
                $p = new \ReflectionMethod($method->class, $relation);
                if ($p->getNumberOfParameters() == 0 && !$p->isStatic()) {
                    if ($this->model->$relation() instanceof BelongsToMany) {
                        if ($deleteDatas === true) {
                            $deleteDatas = $this->model->select();
                        }
                        foreach ($deleteDatas as $deleteData) {
                            $deleteData->$relation()->detach();
                        }
                    } elseif ($this->model->$relation() instanceof HasOne) {
                        if ($deleteDatas === true) {
                            $deleteDatas = $this->model->select();
                        }
                        foreach ($deleteDatas as $deleteData) {
                            if (!is_null($deleteData->$relation)) {
                                $deleteData->$relation->delete();
                            }
                        }
                    }
                }
            }
        }
    }
    /**
     * 获取当前模型的数据库查询对象
     * @return Model
     */
    public function model()
    {
        return $this->model;
    }
    /**
     * 获取当前模型的数据库查询对象
     * @return Model
     */
    public function db()
    {
        return $this->db;
    }

}
