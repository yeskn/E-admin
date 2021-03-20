<?php


namespace Eadmin\component;


trait Where
{
    protected $where = [
        'AND' => [],
        'OR'  => []
    ];

    protected function getWhere()
    {
        return $this->where;
    }

    protected function setWhere($where)
    {
        return $this->where = $where;
    }

    /**
     * 指定AND查询条件
     * @access public
     * @param mixed $field 查询字段
     * @param mixed $op 查询表达式
     * @param mixed $condition 查询条件
     * @param mixed $logic AND OR
     * @return $this
     */
    public function where($field, $op = null, $condition = null, $logic = 'AND')
    {
        if ($field instanceof \Closure) {
            $where = clone $this;
            $where->setWhere([
                'AND' => [],
                'OR'  => []
            ]);
            call_user_func_array($field, [$where]);
            $this->where[$logic][] = [
                'where' => $where->getWhere()
            ];
        } else {

            if ($op === '=') {
                $op = '==';
            }
           
            if (is_null($condition)) {
                $condition = $op;
                $op        = '==';
            }

            $this->where[$logic][] = [
                'field'     => $field,
                'op'        => $op,
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
    public function whereOr(string $field, $op = null, $condition = null)
    {
        return $this->where($field, $op, $condition, 'OR');
    }
}
