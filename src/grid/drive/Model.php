<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2021-01-12
 * Time: 23:43
 */
namespace Eadmin\grid\drive;

use Eadmin\grid\GridInterface;
use think\Db;
use think\facade\Request;
use think\model\relation\BelongsTo;
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