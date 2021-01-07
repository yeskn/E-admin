<?php


namespace Eadmin\component;


trait Where
{
    protected $where = [
        'AND' => [],
        'OR' => []
    ];
    protected function getWhere(){
        return $this->where;
    }
    protected function setWhere($where){
        return $this->where = $where;
    }
    /**
     * 指定AND查询条件
     * @access public
     * @param mixed $field 查询字段
     * @param mixed $op 查询表达式
     * @param mixed $condition 查询条件
     * @return $this
     */
    public function where($field, $op = null, $condition = null)
    {
        if($field instanceof \Closure){
            $where = clone $this;
            $where->setWhere([
                'AND' => [],
                'OR' => []
            ]);
            call_user_func_array($field,[$where]);
            $this->where['AND'][] = [
                'where' => $where->getWhere()
            ];
        }else{
            if (is_null($condition)) {
                $condition = $op;
                $op = '==';
            }
            $this->where['AND'][] = [
                'field' => $field,
                'op' => $op,
                'condition' => $condition
            ];
        }
        return $this;
    }

    /**
     * 指定OR查询条件
     * @access public
     * @param mixed $field 查询字段
     * @param mixed $op 查询表达式
     * @param mixed $condition 查询条件
     * @return $this
     */
    public function whereOr($field, $op = null, $condition = null)
    {
        if (is_null($condition)) {
            $condition = $op;
            $op = '==';
        }
        $this->where['OR'][] = [
            'field' => $field,
            'op' => $op,
            'condition' => $condition
        ];
        return $this;
    }
}
