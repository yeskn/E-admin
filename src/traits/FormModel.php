<?php


namespace Eadmin\traits;



use think\exception\HttpResponseException;
use think\facade\Db;
use think\facade\Log;
use think\Model;
use think\model\relation\BelongsTo;
use think\model\relation\BelongsToMany;
use think\model\relation\HasMany;
use think\model\relation\HasOne;
use think\model\relation\MorphMany;
use think\model\relation\MorphOne;

trait FormModel
{
    //模型
    protected $model;
    //主键字段
    protected $pkField;
    //保存前回调
    protected $beforeSave = null;
    //保存后回调
    protected $afterSave = null;
    //数据源
    protected $data = [];
    //当前编辑数据
    protected $editData = [];
    public function setModel(Model $model){
        $this->model = $model;
        $this->pkField = $this->model->getPk();
    }

    /**
     * 保存数据
     * @param array $data 数据
     * @return bool
     * @throws \Exception
     */
    public function save($data){
        $result = true;
        //保存前回调
        if (!is_null($this->beforeSave)) {
            $beforeData = call_user_func($this->beforeSave, $data);
            if (is_array($beforeData)) {
                $data = array_merge($data, $beforeData);
            }
        }
        if($this->model){
            Db::startTrans();
            try {
                if (isset($data[$this->pkField])) {
                    $isExists = Db::name($this->model->getTable())->where($this->pkField, $data[$this->pkField])->find();
                    if ($isExists) {
                        $this->data = $this->model->where($this->pkField, $data[$this->pkField])->find();
                        $this->model = $this->model->where($this->pkField, $data[$this->pkField])->find();
                    }
                }
                $result = $this->model->save($data);
                foreach ($data as $field => $value) {
                    if (method_exists($this->model, $field)) {
                        //多对多关联保存
                        if ($this->model->$field() instanceof BelongsToMany) {
                            $relationData = $value;
                            $this->model->$field()->detach();
                            if (is_string($relationData)) {
                                $relationData = explode(',', $relationData);
                                $relationData = array_filter($relationData);
                            }
                            if (count($relationData) > 0) {
                                $this->model->$field()->saveAll($relationData);
                            }
                        } elseif ($this->model->$field() instanceof HasOne || $this->model->$field() instanceof BelongsTo || $this->model->$field() instanceof MorphOne) {
                            $relationData = $data[$field];
                            if (is_null($id) || empty($this->data->$field)) {
                                $this->model->$field()->save($relationData);
                            } else {
                                $this->data->$field->save($relationData);
                            }

                        } elseif ($this->model->$field() instanceof HasMany || $this->model->$field() instanceof MorphMany) {
                            $realtionUpdateIds = array_column($value, 'id');
                            if (!empty($this->data->$field)) {
                                $deleteIds = $this->data->$field->column('id');

                                if (is_array($realtionUpdateIds)) {
                                    $deleteIds = array_diff($deleteIds, $realtionUpdateIds);
                                }
                                if (count($deleteIds) > 0) {
                                    $result = $this->model->$field()->whereIn($this->pkField, $deleteIds)->delete();
                                }
                            }
                            foreach ($value as $key => &$val) {
                                $val['sort'] = $key;
                            }
                            if (!empty($value)) {
                                $this->model->$field()->saveAll($value);
                            }
                        }
                    }
                }

                Db::commit();
            } catch (HttpResponseException $e) {
                throw $e;
            } catch (\Exception $e) {
                Db::rollback();
                if (env('APP_DEBUG')) {
                    throw $e;
                } else {
                    Log::error($e->getMessage());
                }
                $result = false;
            }
        }
        //保存回后调
        if (!is_null($this->afterSave)) {
            call_user_func_array($this->afterSave, [$data, $this->model]);
        }
        return $result;
    }
    /**
     * 保存后回调
     * @param \Closure $closure
     */
    public function saved(\Closure $closure)
    {
        $this->afterSave = $closure;
    }

    /**
     * 保存前回调
     * @param \Closure $closure
     */
    public function saving(\Closure $closure)
    {
        $this->beforeSave = $closure;
    }

    /**
     * 设置字段值
     * @param $field 字段
     * @param $value 值
     */
    protected function setData($field,$value){
        if (strpos($field, '.')) {
            list($relation, $field) = explode('.', $field);
            $this->editData[$relation][$field] = $value;
        }else{
            $this->editData[$field] = $value;
        }
    }
    /**
     * 获取字段数据
     * @param string|null $field
     * @return array|mixed|null
     */
    public function getData(string $field = null,$data = null)
    {
        if (is_null($data)) {
            $data = $this->data;
        }
        if (is_null($field)) {
            return $data;
        } else {
            if (method_exists($this->model, $field)) {
                if ($this->model->$field() instanceof BelongsToMany) {
                    if (empty($data->$field)) {
                        $relationData = null;
                    } else {
                        $relationData = $data->$field;
                    }
                    if (is_null($relationData)) {
                        $val = [];
                    } else {
                        $val = $relationData->column($this->pkField);
                    }
                    return $val;
                } elseif ($this->model->$field() instanceof HasMany) {
                    if (empty($data->$field)) {
                        return [];
                    } else {
                        return $data->$field;
                    }
                }
            }
            foreach (explode('.', $field) as $f) {
                if (isset($data[$f])) {
                    $data = $data[$f];
                } else {
                    $data = null;
                }
            }
            return $data;
        }
    }
    /**
     * 设置主键字段
     * @param $field
     */
    public function setPkField($field)
    {
        $this->pkField = $field;
    }
}
