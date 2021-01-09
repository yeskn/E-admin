<?php


namespace Eadmin\grid;


use think\Db;
use think\facade\Request;
use think\Model;
use think\model\relation\BelongsTo;
use think\model\relation\HasMany;
use think\model\relation\HasOne;
use think\model\relation\MorphMany;
use think\model\relation\MorphOne;

/**
 * Trait GridModel
 * @package Eadmin\grid
 * @property Db $db
 */
trait GridModel
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
        }
        if (count($fields) > 1) {
            foreach ($fields as $field) {
                $this->setRelation($field, $relation);
            }
        } else {
            $this->setRelation($relation, $relation);
        }
    }
    protected function modelData(){
       if($this->db){
           //预关联加载
           $this->withRelations();
           if($this->hidePage){
               $this->data = $this->db->select()->toArray();
           }else{
               $this->data = $this->db->page(Request::get('page', 1), Request::get('size', $this->pagination->attr('pageSize')))->select()->toArray();
           }
       }
    }
    //获取数据总条数
    protected function getCount()
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
    /**
     * 获取当前模型的数据库查询对象
     * @return Model
     */
    public function model()
    {
        return $this->db;
    }
    public function setModel(Model $model){
        $this->model = $model;
        $this->db = $this->model->db();
        $this->pkField = $this->model->getPk();
    }
}
